<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 04/11/14
 * Time: 10:33
 */

namespace component\progressive\v1;


use component\configurationmanager\ConfigurationManager;
use component\request\Request;
use model\GMAPIDataRepository;
use exceptions\ProgressiveException;
use Symfony\Component\Yaml\Yaml;

class DataGetter {

    private $yamlConfig;

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getOperatorId()
    {
        if($this->request->getOperatorName() == '0')
        {
            return $this->request->getOperatorName();
        }

        $data = new GMAPIDataRepository('irsbo');
        $result = $data->fetchOperatorIdByLicenseeAndOperatorName($this->request->getLicenseeId(),$this->request->getOperatorName());

        if ((is_array($result) && sizeof($result) == 0) || $result === null )
        {
            throw new ProgressiveException("Problem with fetch operator ID by licensee ID during Progressive read process", 5, 5);
        }
        else
        {
            return $result['operator_id'];
        }
    }

    /**
     * Load Configuration YAML file
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    protected function loadConfiguration($name)
    {
        $component_validation_root = dirname(dirname(__FILE__));
        $namespace = __NAMESPACE__;
        $name_arr = explode("\\", $namespace);
        $version = end($name_arr);

        $configPath = $component_validation_root.DIRECTORY_SEPARATOR.$version.DIRECTORY_SEPARATOR.'configuration'.DIRECTORY_SEPARATOR;

        $validationConfigPath   = $configPath.$name.'.yml';

        if(is_dir($validationConfigPath))
        {
            throw new ProgressiveException('Gmapi Internal Error: ValidationErrorException:Configuration file does not exist!',0);
        }

        return Yaml::parse($validationConfigPath);
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function getCurrency($dbPrefix)
    {
        $this->yamlConfig = $this->loadConfiguration('exceptions');

        $exceptions = $this->yamlConfig['specials'];
        $licenseeId = intval($this->request->getLicenseeId());

        if(array_key_exists($licenseeId,$exceptions))
        {
            $getCurrencyMethod = $exceptions[$licenseeId];
            return $this->$getCurrencyMethod($dbPrefix,$this->request->getOperatorId());
        }
        else
        {
            return $this->defaultCurrencyMethod();
        }

    }

    protected function getCurrencyByUserIdFromRequest($dbPrefix,$oid)
    {
        $dbName = $dbPrefix.'_mdpadmin';
        $data = new GMAPIDataRepository($dbName);
        $result = $data->fetchCurrencyPerPlayerGetCurrency($oid,$this->request->getPlayerId());
        if((is_array($result) && sizeof($result) == 0) || $result === null )
        {
            throw new ProgressiveException('Progressive server. Fetch currency error.',5,5);
        }
        return $result['currency'];
    }

    protected function getCurrencyByUserIdFromDatabase($dbPrefix,$oid)
    {

        if($this->request->getPlayerId() === null || $this->request->getPlayerId() === '')
        {
            $dbName = $dbPrefix.'_accounting';
            $data = new GMAPIDataRepository($dbName);
            $result = $data->fetchAccountingPlayersBySessionId($this->request->getSessionId());
            if((is_array($result) && sizeof($result) == 0) || $result === null )
            {
                throw new ProgressiveException('Progressive server. Fetch user name from accounting table error.',5,5);
            }
            $this->request->setPlayerId($result['user_name']);
        }

        $dbName = $dbPrefix.'_mdpadmin';
        $data = new GMAPIDataRepository($dbName);
        $result = $data->fetchCurrencyPerPlayerGetCurrency($oid,$this->request->getPlayerId());
        if((is_array($result) && sizeof($result) == 0) || $result === null )
        {
            throw new ProgressiveException('Progressive server. Fetch currency error.',5,5);
        }
        return $result['currency'];
    }

    protected function defaultCurrencyMethod()
    {
        $currency = $this->request->getCurrency();
        if($currency === '' || $currency === null )
        {
            throw new ProgressiveException('Progressive server. Received value of currency is wrong.',5,5);
        }
        return $currency;
    }

} 