<?php
/**
 * Created by PhpStorm.
 * User: Pawel
 * Date: 19/09/14
 * Time: 11:44
 */

namespace component\validation\v1;


use component\configurationmanager\ConfigurationManager;
use component\validation\Validation as BaseValidation;
use component\request\Request;
use component\validation\ValidationInterface;
use Symfony\Component\Yaml\Yaml;

class Validation extends BaseValidation implements ValidationInterface{

    private $yaml_config;

    /**
     * @var null|\component\request\Request
     */
    private $validatedRequest = null;

    private $validationError = false;

    private $error_message;

    private $code = "";

    /**
     * @var $gmapiLicensee  - contain current gmapi configuration
     */
    private $gmapiLicensee;


    /**
     * Contain path to validation root directory
     */
    protected $configuration_request;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('GMT');
        $this->setConfigurationPath();
    }

    /**
     * Validates a request
     * @param \component\request\Request $request
     * @return bool
     */
    public function validateRequest(Request $request)
    {
        $this->yaml_config      = $this->loadConfiguration('request');
        $this->validationError  = true;
        try
        {
            // Method Validation
            $methodName = $request->getMethodName();
            $request = $this->getClassByMethodValidation($methodName);

            // Basic validation
            $this->validateRequiredRequest($request);

            // Get Configurations
            $cm                     = new ConfigurationManager();
            $cm                     = $cm->loadComponent();
            $this->gmapiLicensee    = $cm->getConfiguration($request->getLicenseeId());

            $gameValid = new GameValidation($request);

            if(!$gameValid->checkDetailsOfLicense($this->gmapiLicensee))
            {
                $message = "Wrong parameters. License: '".$request->getLicenseeId()."' does not exist";
                throw new \exceptions\ValidationException($message,5,6);
            }


            if(!$gameValid->checkHash())
            {
                $this->error_message    = "Parameter: hash info not correct";
                $this->code             = "102";
                throw new \exceptions\ValidationException($this->error_message,1,0);
            }


            if(!$gameValid->checkRegulation())
            {
                $message = "Wrong parameters. Regulation ID.: " . $request->getRegulationId() . " is not equal to configuration";
                $this->code             = "110";
                throw new \exceptions\ValidationException($message,5,6);
            }

            if(!$gameValid->checkConfiguration())
            {
                $message = "Wrong parameters. Configuration ID.: " . $request->getConfigurationId() . " is not equal to configuration";
                $this->code             = "111";
                throw new \exceptions\ValidationException($message,5,6);
            }

            if(!$gameValid->checkGameConfiguration())
            {
                $message = "Wrong parameters. GameId ".$request->getGameId()." with SkinId ".$request->getSkinId()." does not have a valid configuration in database";
                $this->code             = "112";
                throw new \exceptions\ValidationException($message,5,6);
            }

            // converts country code from UK to GB
            $gameValid->checkCountryCode();


            // this validation can be done only for iSoftBet provider (pid = 1)

            if ($request->getProviderId() == 1) {
                
                if (!$gameValid->checkIfOperatorNameIsAllowed()) {
                    $message = "Wrong parameters. Operator Name: '" . $request->getOperatorName() . "' is not allowed to licensee ID: " . $request->getLicenseeId();
                    $this->code = "113";
                    throw new \exceptions\ValidationException($message, 5, 6);
                }

                if (!$gameValid->checkCurrencyPerCoinValue()) {
                    $message = "Game status checking problem. Game: " . $request->getGameId() . " is inactive in the backoffice. Currency: " . $request->getCurrency() . " is not assigned in game_coin_values.";
                    $this->code = "114";
                    throw new \exceptions\ValidationException($message, 5, 0);
                }

                if (!$gameValid->checkCurrencyPerOperator()) {
                    $message = "Currency/Operator does not exist in backoffice configuration: " . $request->getCurrency() . " / Operator: " . $request->getOperatorName() . " dosn't exist in backoffice configuration.";
                    $this->code = "115";
                    throw new \exceptions\ValidationException($message, 5, 0);
                }

                if (!$gameValid->checkIfCurrencyIsActiveInBackoffice()) {
                    $message = "Currency checking problem. Currency: " . $request->getCurrency() . " is inactive in backoffice.";
                    $this->code = "116";
                    throw new \exceptions\ValidationException($message, 5, 0);
                }

                // it should only for blogin
                if (!$gameValid->checkCurrencyPerPlayer()) {
                    $message = "Wrong parameters. Currency: '" . $request->getCurrency() . "' does not valid for user '" . $request->getPlayerId() . "'";
                    $this->code = "117";
                    throw new \exceptions\ValidationException($message, 5, 6);
                }
            }



            // Client call validation
            if($request->isGameTypeCall() === true && $request->getPp() !== 'fun' )
            {
                $tourValid = new TournamentValidation($request);
                if($tourValid->checkTournamentMode())
                {
                    if(!$tourValid->checkTournamentId())
                    {
                        $message = "Wrong parameters. Password: '" . $request->getPp() . "' does not valid '";
                        $this->code = "000"; // to change
                        throw new \exceptions\ValidationException($message, 5, 6); // to change 
                    }

                    if ($request->getProviderId() == 1) {
                        if (!$gameValid->checkIfOperatorNameIsAllowed()) {
                            $message = "Wrong parameters. Operator Name: '" . $request->getOperatorName() . "' is not allowed to licensee ID: " . $request->getLicenseeId();
                            $this->code = "113";
                            throw new \exceptions\ValidationException($message, 5, 6);
                        }
                    }

                    $request->setTournamentId($tourValid->getTournamentId());
                    $request->setMode('tournament');
                }
            }
            // external method call validation
            elseif( $request->getPp() !== 'fun' )
            {

            }
            // fun client call
            else
            {
                $request->setMode('fun');
            }

            $this->validatedRequest = $request;
            $this->validationError  = true;
        }
        catch(\Exception $error)
        {
            $this->callToErrorLog($error);
        }
        return $this->validationError;
    }

    /**
     * Method name validation
     * @param $methodName
     * @return mixed
     * @throws \exceptions\ValidationException
     */
    private function getClassByMethodValidation($methodName)
    {
        //die(var_dump($methodName));
        if(!isset($this->yaml_config["call_methods"][$methodName]))
        {
            $this->error_message    = "CALL METHOD DOESN'T EXIST";
            $this->code             = "02";
            throw new \exceptions\ValidationException($this->error_message,1,2);
        }

        $namespace      = $this->yaml_config["basic_configuration"]['namespace'];
        $className      = $namespace.$this->yaml_config["call_methods"][$methodName];
        $object         = new $className();
        return $object;
    }

    /**
     * General method to set error and comunicate with logger
     * @param $error
     */
    private function callToErrorLog($error)
    {
        $message = $error->getMessage();
        json_decode($message);
        if(json_last_error() == JSON_ERROR_NONE)
        {
            $this->error_message = $message;
        }
        else
        {
            $this->error_message = $error->getMessage();
        }
        $this->validationError = false;
        self::$logger->addException($error,$this);
    }

    /**
     * Return true if request is valid and false if is not
     * @return bool
     */
    public function isRequestValid()
    {
        return $this->validationError;
    }

    /**
     * @return \component\request\Request|mixed|null
     */
    public function getValidatedRequest()
    {
        return $this->validatedRequest;
    }

    /**
     * Basic validation to validate set of standard parameters
     *
     * @param Request $requestObject 
     * @param $parameters
     */
    private function validateRequiredRequest($requestObject)
    {
        $validation = $this->loadConfiguration('validation');
        $validation = $validation['validation'];

        $parameters = $requestObject->getRequired();
        foreach ($parameters as $parameter => $requestMethod) {

            $requestMethod = 'get'.ucfirst($requestMethod);
            $container = $requestObject->$requestMethod();
            if($container === NULL )
            {
                $message = "Wrong Parameters. Parameter: " . $parameter . " is required";
                $this->code             = "118";
                throw new \exceptions\ValidationException($message,5,6);
            }

            $valid = ParticularValidator::regex($validation[$parameter],$container);
            if($valid === 0)
            {
                $message = "Wrong Parameters. Parameter: " . $parameter . " has wrong format. Current value is: " . $container;
                $this->code             = "119";
                throw new \exceptions\ValidationException($message,5,6);
            }
        }
    }

    /**
     * Internal setter of pattern configuration path
     */
    private function setConfigurationPath()
    {
        $component_validation_root = dirname(dirname(__FILE__));

        $namespace = __NAMESPACE__;
        $name_arr = explode("\\", $namespace);
        $version = end($name_arr);

        $this->configuration_request = $component_validation_root.DIRECTORY_SEPARATOR.$version.DIRECTORY_SEPARATOR.'configuration'.DIRECTORY_SEPARATOR;  
    }

    /**
     * Load Configuration YAML file
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    private function loadConfiguration($name)
    {
        $validation_config_path   = $this->configuration_request.$name.'.yml';
        
        if(is_dir($validation_config_path))
        {
            $message = 'Gmapi Internal Error: ValidationErrorException:Configuration file does not exist!';
            throw new \Exception($message);
        }

        return Yaml::parse($validation_config_path);
    }

    /**
     * Get message of last occurred error
     *
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * Get Code of last occurred error
     *
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }


} 