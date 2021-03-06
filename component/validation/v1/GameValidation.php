<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 22/10/14
 * Time: 15:59
 */

namespace component\validation\v1;

use model\GMAPIDataRepository;
use component\configurationmanager\ConfigurationManager;

class GameValidation {

    private $request;

    private $gmapiLicensee;

    private $operatorId;

    private $currencyPerCoinValueFlag;

    private $currencyPerOperatorFlag;

    public function __construct($request)
    {
        $this->request          = $request;

        // Get Configurations
        $cm                     = new ConfigurationManager();
        $cm                     = $cm->loadComponent();
        $this->gmapiLicensee    = $cm->getConfiguration($request->getLicenseeId());
    }

    /**
     * Hash Validation
     * - if method returns True it means call method doesn't require hashinfo
     *
     * @param $request
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Exception
     * @throws \exceptions\ValidationException
     */
    public function checkHash()
    {
        if($this->request->getHashInformation() == null)
        {
            return true;
        }

        $datatime   = new \DateTime();
        $lid = $this->request->getLicenseeId();

        $hash = "";
        $hash .= $lid;
        $hash .= "+";
        $hash .= $this->gmapiLicensee->getSceretKey();
        $hash .= "+";
        $hash .= $this->request->getGameId();
        $hash .= "+";
        $hash .= $this->request->getSkinId();
        $hash .= "+";
        $hash .= $this->gmapiLicensee->getConfigurationId();
        $hash .= "+";

        $hour        = $datatime->format("G");
        $minutes     = $datatime->format("i");
        $day_of_year = $datatime->format("z") + 1;

        $day_in_utc1 = $day_of_year;
        $day_in_utc2 = $day_of_year;

        if ($hour == "23" && $minutes > "49")
        {
            $day_in_utc2 = $day_in_utc1 + 1;
        }
        if ($hour == "00" && $minutes < "11")
        {
            $day_in_utc2 = $day_in_utc1 - 1;
        }

        $hash1 = hash("sha512", ($hash . $day_in_utc1));
        $hash2 = hash("sha512", ($hash . $day_in_utc2));

        if ($this->request->getHashInformation() == $hash1 || $this->request->getHashInformation() == $hash2)
        {

            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkRegulation()
    {
        if($this->request->getRegulationId() == null)
        {
            return true;
        }

        $regulationId = $this->gmapiLicensee->getRegulationNumber();
        if($regulationId != $this->request->getRegulationId())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public  function checkConfiguration()
    {
        if($this->request->getConfigurationId() == null)
        {
            return true;
        }

        $configurationId = $this->gmapiLicensee->getConfigurationId();
        if($configurationId != $this->request->getConfigurationId())
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Simple game configuration validation
     * - if method returns True it means call method doesn't require game or skin ID
     *
     * @param $request
     * @throws \exceptions\ValidationException
     */
    public function checkGameConfiguration()
    {
        $gameId     = $this->request->getGameId();
        $skinId     = $this->request->getSkinId();

        if($gameId == null || $skinId == null)
        {
            return true; // exception for other calls
        }

        $db = new GMAPIDataRepository('irsbo');
        $check = $db->fetchGameAndSkinId($gameId,$skinId);

        if( (is_array($check) && sizeof($check) == 0) || $check === false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function checkIfOperatorNameIsAllowed()
    {
        $lid = $this->request->getLicenseeId();
        $operator = $this->request->getOperatorName();

        if ($lid == null || $operator == null)
        {
            return true;
        }
        elseif ($operator == '0') 
        {
            $this->operatorId = $operator;
            return true;
        }
        else
        {
            $db = new GMAPIDataRepository('irsbo');
            $check = $db->fetchOperatorIdByLicenseeAndOperatorName($lid, $operator);

            if ((is_array($check) && sizeof($check) == 0) || $check === false)
            {
                return false;
            }
            else
            {
                $this->operatorId = $check["operator_id"];
                return true;
            }
        }
    }

    public function checkCurrencyPerPlayer()
    {
        if($this->request->getMethodName() !== 'blogin')
        {
            return true;
        }

        $currency   = $this->request->getCurrency();
        $playerId   = $this->request->getPlayerId();

        if($currency === null || $playerId === null)
        {
            return true;
        }

        $dbname = $this->gmapiLicensee->getDbPrefix().'_mdpadmin';
        $db = new GMAPIDataRepository($dbname);
        $check = $db->fetchCurrencyPerPlayerCheckPlayer($playerId);

        if ((is_array($check) && sizeof($check) == 0) || $check === false)
        {
            $db->insertCurrencyPerPlayer($playerId, $this->operatorId, $currency); // <<--- NEW ONE to make it
            return true;
        }
        else
        {
            $check = $db->fetchCurrencyPerPlayerCheckCurrency($playerId, $currency); //check if currency is valid
            if ((is_array($check) && sizeof($check) == 0) || $check === false) // <<--- NEW ONE to make it
            {
                return false; // pair doesn't exist
            }
            else
            {
                return true; // pair exist
            }
        }
    }

    public function checkCurrencyPerCoinValue()
    {
        $currency = $this->request->getCurrency();
        $skinId = $this->request->getSkinId();

        if($currency === null || $skinId === null)
        {
            $this->currencyPerCoinValueFlag = null;
            return true;
        }

        $dbname = $this->gmapiLicensee->getDbPrefix().'_casino_games';
        $db = new GMAPIDataRepository($dbname);
        $check = $db->fetchCurrencyPerCoinValue($skinId,$this->operatorId,$currency);


        if ((is_array($check) && sizeof($check) == 0) || $check === false)
        {
           return false;
        }
        else
        {
           $this->currencyPerCoinValueFlag = (int)$check['flag'];
           if(!$this->checkIfGameIsActiveInBackoffice())
           {
                return false;
           }
           return true;
        }
    }

    public function checkIfGameIsActiveInBackoffice()
    {
        if($this->currencyPerCoinValueFlag != 1)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function checkCurrencyPerOperator()
    {
        $currency = $this->request->getCurrency();
        $lid = $this->request->getLicenseeId();

        if($currency === null || $lid === null)
        {
            $this->currencyPerOperatorFlag = null;
            return true;
        }

        $db = new GMAPIDataRepository('irsbo');
        $check = $db->fetchCurrencyPerOperatorId($lid,$this->operatorId,$currency);

        if ((is_array($check) && sizeof($check) == 0) || $check === false)
        {
            return false;
        }
        else
        {
            $this->currencyPerOperatorFlag = $check['status'];
            return true;
        }
    }

    public function checkIfCurrencyIsActiveInBackoffice()
    {
        if($this->currencyPerCoinValueFlag === null)
        {
            return true;
        }

        if($this->currencyPerCoinValueFlag != 1)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function checkDetailsOfLicense($configuration)
    {
        if($this->request->getLicenseeId() === null)
        {
            return false;
        }

        if($configuration === null)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function checkCountryCode()
    {
        if(strtoupper($this->request->getCountry()) == 'UK')
        {
            $this->request->setCountry('GB');
        }

        return $this;
    }
} 