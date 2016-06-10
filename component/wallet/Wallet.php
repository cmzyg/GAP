<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 13/10/14
 * Time: 15:05
 */

namespace component\wallet;


use application\BaseComponent;
use component\wallet\fun\Fun;
use component\wallet\tournament\Tournament;
use component\wallet\nyx\Nyx;
use component\wallet\seamless\Seamless;
use component\wallet\purse\Purse;
use exceptions\WalletException;
use model\GMAPIDataRepository;

/**
 * Class Wallet
 * @author Samuel I. Amaziro
 * @package component\wallet
 */
class Wallet extends BaseComponent{

    //some sets to manage errors
    protected $retryCount = 0;
    const CURL_EXCEPTION = 1;
    const HTTP_EXCEPTION = 3;
    const THIRD_PARTY_EXCEPTION = 2;
    const MYSQL_EXCEPTION = 4;
    
    
    /**
     * Status of the current process
     * @var bool
     */
    protected $processStatus = false;
    /**
     * Output of the current process as an associative array
     * @var array
     */
    protected $processResponse = array();
    /**
     * If the error encountered should be shown or not
     * @var bool
     */
    protected $showProcessError = false;
    /**
     * The request object containing all the details of the Request
     * @var \component\request\Request
     */
    protected $request;
    /**
     * The accounting object containing all the methods used for the Accounting operations
     * @var \component\accounting\v1\Accounting
     */
    protected $accounting;
    /**
     * The progressive object containing all the methods used for the Progressive operations
     * @var \component\progressive\v1\Progressive
     */
    protected $progressive;
    /**
     * The Communication component to use for communicating with third parties using various methods
     * @var \component\communication\Communication|\component\communication\v1\Communication
     */
    protected $communicationComponent;


    public function __construct()
    {
        parent::__construct();
        $aComp = new \component\accounting\Accounting();
        $this->accounting = $aComp->loadComponent();
        $pComp = new \component\progressive\Progressive();
        $this->progressive = $pComp->loadComponent();
    }

    /**
     * Loads a component based on current version
     * @return mixed|$this|\component\wallet\nyx\Nyx
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * Selects current version of component
     * @param $version
     * @return $this|\component\wallet\nyx\Nyx
     */
    private function selectVersion($version)
    {
        switch($version)
        {
            case "nyx":
                return new Nyx();
            case "fun":
                return new Fun();
            case "tournament":
                return new Tournament();
            case "seamless":
                return new Seamless();
            case "purse":
                return new Purse();
            default:
                return $this;
        }

    }

    public function NotSupported()
    {
        return "Action is not Supported";
    }

    /**
     * @return bool
     */
    public function isProcessSuccessful()
    {
        return $this->processStatus;
    }

    /**
     * @return bool
     */
    public function getShowProcessError()
    {
        return $this->showProcessError;
    }

    /**
     * @return string
     */
    public function getProcessResponse()
    {
        return $this->processResponse;
    }

    /**
     * Returns an array of available versions for a component
     * @return array
     */
    public function getAvailableVersions()
    {
        return array("nyx", "fun", "lega", "openbet", "seamless","purse","tournament");
    }

    protected function errorLogger($constant, $msg, $url, $response, $statusCode, $httpCode = 0, $httpHeader = null)
    {
        $message = $msg;
        $message .= "3rd PARTY REQUEST: ".$url;
        $message .= "3rd PARTY RESPONSE: ".$response;
        $message .= "3rd PARTY CURL CODE: ".$statusCode;
        $message .= "3rd PARTY HTTP CODE: ".$httpCode;
        $message .= "3rd PARTY HTTP DESC: ".(is_array($httpHeader)?json_encode($httpHeader):"");

        $ex = new WalletException($message, $constant);
        self::getLogger()->addException($ex);
    }

    /**
     * Gets coin values based on the given operator id and currency
     * @param $operator
     * @param $currency
     * @return bool|string
     */
    public function validateCurrencyAndGetCoinValue($operator, $operatorStatus, $currency, $skinID)
    {
        $databaseName = $this->request->getLicenseeObject()->getDbPrefix()."_casino_games";
        $data         = new GMAPIDataRepository($databaseName);
        $coinValues   = $data->getCoinValueBaseOnCurrencyAndOperatorId($operator, $currency, $skinID);

        if(!is_array($coinValues) || $operatorStatus == 0 || (is_array($coinValues) && !isset($coinValues[0]['coin_values'])))
        {
            return false;
        }

        return $coinValues[0]['coin_values'];
    }


    /**
     * Checks if operator name is specified. If not - then it means that
     * there's a master operator. In that case just set id to 0, status to 1
     * otherwise query the database for licensee id and operator name for validation
     * @return array
     */
    public function getOperatorIdAndStatus($licenseeId, $operatorName)
    {
        $operator = array();

        if(!$operatorName)
        {
            $operator['operator_id'] = 0;
            $operator['status']      = 1;
        }
        else
        {
            $data = new GMAPIDataRepository('irsbo');
            $operator = $data->fetchOperatorIdByLicenseeAndOperatorName($licenseeId, $operatorName);
        }

        return $operator;
    }


    /**
     * Checks if currency exists in an external JSON document
     * @return bool
     */
    function validateCurrencyFormat($currency)
    {
        $availableCurrencyURL     = 'http://currencies.isoftbet.com/currencies';
        $availableCurrencyFormats = json_decode(file_get_contents($availableCurrencyURL), true);
        $currencyExists           = false;
        foreach($availableCurrencyFormats as $value) {
            if($value['ccode'] == $currency)
            {
                $currencyExists = true;
                break;
            }
        }

        return $currencyExists;
    }

}