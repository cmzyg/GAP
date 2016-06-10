<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/10/14
 * Time: 10:15
 */

namespace component\configurationmanager\v1;


use application\BaseComponent;
use component\configurationmanager\ConfigurationManagerInterface;
use component\configurationmanager\Licensee;
use Doctrine\ORM\EntityManager;
use exceptions\GMAPIGeneralException;
use model\gmapi\GmapiConfiguration;
use model\gmapi\GmapiLicensee;
use model\gmapi\GmapiServerConfiguration;
use model\gmapi\GmapiWallet;
use model\gmapi\GmapiWalletAuthorisation;

class ConfigurationManager extends \component\configurationmanager\ConfigurationManager implements ConfigurationManagerInterface{

    protected $configPath;
    private $loadConfig = false;
    private $loadConfigErrorMessage;

    public function __construct()
    {
        parent::__construct();
        //$this->configPath = _SITE_PATH."component".DIRECTORY_SEPARATOR."configurationmanager".DIRECTORY_SEPARATOR."v1".DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR;
        $this->configPath = DIRECTORY_SEPARATOR."web_cache".DIRECTORY_SEPARATOR."gap.isoftbet.com".DIRECTORY_SEPARATOR;
        $this->entityManager = \component\EntityManager::createEntityManager();
    }

    /**
     * @param null $serverName
     * @return null|array
     */
    protected function loadServerConfiguration($serverName = null)
    {
        $readConfig = function($configPath){
            $jsonFile = $configPath."servers.json";

            if(file_exists($jsonFile))
            {
                $array = file_get_contents($jsonFile);
                $servers = unserialize($array);

                return $servers;
            }
            BaseComponent::getLogger()->addException(new GMAPIGeneralException("Server Configuration file : ".$jsonFile." not found when trying to load file", 3));
            return null;
        };

        $storeConfig = function(array $servers, $configPath){
            $config = serialize($servers);
            $value = @file_put_contents($configPath."servers.json", $config);

            if($value === false)
            {
                return false;
            }

            return true;
        };


        $fetchDBConfig = function(EntityManager $em, $serverName = null){
            if(is_null($serverName))
            {
                $lArray = $em->getRepository("model\\gmapi\\GmapiServerConfiguration")->fetchAllServerConfig();
            }
            else
            {
                $result = $em->getRepository("model\\gmapi\\GmapiServerConfiguration")->fetchServer($serverName);
                $server = ((is_array($result)) ? $result[0] : $result);
                $lArray = $em->getRepository("model\\gmapi\\GmapiServerConfiguration")->fetchServerConfig($server);
            }


            if(sizeof($lArray) > 0)
            {
                return $lArray;
            }

            return null;
        };

        $serverParser = function($servers)
        {
            $sArray = array();

            if($servers instanceof GmapiServerConfiguration)
            {
                $sArray[$servers->getConfigurationName()] = $servers;
            }else
            {
                foreach($servers as $server)
                {
                    $sArray[$server->getConfigurationName()] = $server->getConfigurationValue();
                }
            }

            return $sArray;
        };

        $rConfig =  $readConfig($this->configPath);

        if(!is_null($rConfig))
        {
            if(!is_null($serverName))
            {
                if(isset($rConfig[$serverName]))
                {
                    return $rConfig[$serverName];
                }else
                {
                    $dConfig = $fetchDBConfig($this->entityManager, $serverName);

                    if(!is_null($dConfig))
                    {
                       return $serverParser($dConfig);

                    }
                }
            }
            else
            {
                if(sizeof($rConfig) < 1)
                {
                    $dConfig = $fetchDBConfig($this->entityManager);
                    $result = $serverParser($dConfig);
                    $storeConfig($result, $this->configPath);
                    return $result;
                }
                else
                {
                    return $rConfig;
                }

            }
        }
        else
        {
            if(!is_null($serverName))
            {
                $dConfig = $fetchDBConfig($this->entityManager, $serverName);
                $result = $serverParser($dConfig);
            }
            else
            {
                $dConfig = $fetchDBConfig($this->entityManager);
                $result = $serverParser($dConfig);
                $storeConfig($result, $this->configPath);
            }

            return $result;


        }

        return null;
    }


    public function reloadServerConfiguration()
    {
        $fetchDBConfig = function(EntityManager $em){

            $lArray = $em->getRepository("model\\gmapi\\GmapiServerConfiguration")->fetchAllServerConfig();

            if(sizeof($lArray) > 0)
            {
                return $lArray[0];
            }

            return null;
        };

        $storeConfig = function($servers, $configPath){
            $configObject = serialize($servers);
            $value = @file_put_contents($configPath."servers.json", $configObject);

            if($value === false)
            {
                return false;
            }

            return true;
        };

        $serverParser = function($servers)
        {
            $sArray = array();

            if($servers instanceof GmapiServerConfiguration)
            {
                $sArray[$servers->getServer()->getServerName()][$servers->getConfigurationName()] = $servers;
            }else
            {
                foreach($servers as $server)
                {
                    $sArray[$server->getServer()->getServerName()][$server->getConfigurationName()] = $server;
                }
            }

            return $sArray;
        };

           $servers = $fetchDBConfig($this->entityManager);

            $config = $serverParser($servers);

            if(!$storeConfig($config, $this->configPath))
            {
                $this->loadConfigErrorMessage = "Failed to store config";
            }

            return $config;

    }

    /**
     * @param Licensee $licensee
     * @return null|\model\gmapi\GmapiLicensee
     */
    protected function loadConfiguration(Licensee $licensee)
    {
        $readConfig = function($licenseeId, $configPath){
            $jsonFile = $configPath.$licenseeId.".json";

            if(file_exists($jsonFile))
            {
                $obj = file_get_contents($jsonFile);

                $licensee = unserialize($obj);

                return $licensee;
            }
            BaseComponent::getLogger()->addException(new GMAPIGeneralException("Configuration file : ".$jsonFile." not found when trying to load file", 3));
            return null;
        };


        $storeConfig = function(GmapiLicensee $licenseeObj, $configPath){
            $config = serialize($licenseeObj);
            $value = @file_put_contents($configPath.$licenseeObj->getIdLicensee().".json", $config);

            if($value === false)
            {
                return false;
            }

            return true;
        };

        $fetchDBConfig = function($licenseeId, EntityManager $em, $oName = null){
            if(is_null($oName))
            {
                $lArray = $em->getRepository("model\\gmapi\\GmapiLicensee")->fetchLicenseeByOperatorId($licenseeId);
            }
            else
            {
                $lArray = $em->getRepository("model\\gmapi\\GmapiLicensee")->fetchLicenseeByOperatorName($oName);
            }


            if(sizeof($lArray) > 0)
            {
                return $lArray[0];
            }

            return null;
        };

        $isConfig = function($config){

            if($config instanceof GmapiLicensee)
            {
                return true;
            }

            return false;
        };


        $config = null;


        if(!is_null($licensee->getSpecialFlag()))
        {
            if(!is_null($licensee->getOperatorId()))
            {
                $config = $fetchDBConfig($licensee->getOperatorId(), $this->entityManager);
            }
            else
            {
                $config = $fetchDBConfig($licensee->getOperatorId(), $this->entityManager, $licensee->getOperatorName());
            }

            if($isConfig($config))
            {
                $this->loadConfig = true;
            }
            else
            {
                $this->loadConfigErrorMessage = "Failed to load config from database";
            }

        }
        else
        {
            $config = $readConfig($licensee->getOperatorId(), $this->configPath);

            if($isConfig($config))
            {
                $this->loadConfig = true;
            }
            else
            {
                $config = $fetchDBConfig($licensee->getOperatorId(), $this->entityManager);

                if($isConfig($config))
                {
                    $this->loadConfig = true;
                    $config->getGmapiConfiguration()->getWallet()->setName($config->getGmapiConfiguration()->getWallet()->getName());
                    $config->getGmapiConfiguration()->getWalletAuthorisation()->setCertificate($config->getGmapiConfiguration()->getWalletAuthorisation()->getCertificate());
                    $config->getGmapiConfiguration()->getWalletAuthorisation()->setLogin($config->getGmapiConfiguration()->getWalletAuthorisation()->getLogin());
                    $config->getGmapiConfiguration()->getWalletAuthorisation()->setPassword($config->getGmapiConfiguration()->getWalletAuthorisation()->getPassword());

                  if(!$storeConfig($config, $this->configPath))
                  {
                      $this->loadConfigErrorMessage = "Failed to store config";
                  }

                }
                else
                {
                    $this->loadConfigErrorMessage = "Failed to load config from database";
                }
            }
        }

        return $config;

    }

    public function reloadConfiguration($licenseeId)
    {
        $fetchDBConfig = function($licenseeId, EntityManager $em){

            $lArray = $em->getRepository("model\\gmapi\\GmapiLicensee")->fetchLicenseeByOperatorId($licenseeId);

            if(sizeof($lArray) > 0)
            {
                return $lArray[0];
            }

            return null;
        };

        $storeConfig = function(GmapiLicensee $licenseeObj, $configPath){

            $configObject = serialize($licenseeObj);
            $value = @file_put_contents($configPath.$licenseeObj->getIdLicensee().".json", $configObject);

            if($value === false)
            {
                return false;
            }

            return true;
        };

        $config = $fetchDBConfig($licenseeId, $this->entityManager);

        if($config instanceof GmapiLicensee)
        {
            $this->loadConfig = true;
            $config->getGmapiConfiguration()->getWallet()->setName($config->getGmapiConfiguration()->getWallet()->getName());
            $config->getGmapiConfiguration()->getWalletAuthorisation()->setCertificate($config->getGmapiConfiguration()->getWalletAuthorisation()->getCertificate());
            $config->getGmapiConfiguration()->getWalletAuthorisation()->setLogin($config->getGmapiConfiguration()->getWalletAuthorisation()->getLogin());
            $config->getGmapiConfiguration()->getWalletAuthorisation()->setPassword($config->getGmapiConfiguration()->getWalletAuthorisation()->getPassword());

            if(!$storeConfig($config, $this->configPath))
            {
                $this->loadConfigErrorMessage = "Failed to store config";
            }

            return $config;
        }
        else
        {
            $this->loadConfigErrorMessage = "Failed to read config from database";
            BaseComponent::getLogger()->addException(new GMAPIGeneralException($this->loadConfigErrorMessage." for licensee id :".$licenseeId, 3));
        }

        return $config;
    }

    /**
     * @param $licenseeId
     * @param null $operatorName
     * @param null $useOperatorNameInsteadOfLicenseeId
     * @param null $licenseeName
     * @return GmapiLicensee|null
     */
    public function getConfiguration($licenseeId, $operatorName = null, $useOperatorNameInsteadOfLicenseeId = null, $licenseeName = null)
    {
        $licensee = new Licensee();
        $licensee->setOperatorId($licenseeId);
        $licensee->setOperatorName($operatorName);
        $licensee->setLicensee($licenseeName);

        if(!is_null($useOperatorNameInsteadOfLicenseeId))
        {
            $licensee->setSpecialFlag(true);
        }

        return $this->loadConfiguration($licensee);
    }

    public function getExtraConfiguration($licenseeObject)
    {
        return $this->entityManager->getRepository("model\\gmapi\\GmapiLicensee")->fetchLicenseeExtraConfiguration($licenseeObject);
    }

    public function getServerConfiguration($server = null)
    {
        if(is_null($server))
        {
            return $this->loadServerConfiguration();
        }

        return $this->loadServerConfiguration($server);
    }


    public function isLoadConfigSuccessful()
    {
        return $this->loadConfig;
    }

    /**
     * @return mixed
     */
    public function getLoadConfigErrorMessage()
    {
        return $this->loadConfigErrorMessage;
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

}