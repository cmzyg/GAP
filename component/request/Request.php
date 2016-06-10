<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/09/14
 * Time: 15:40
 */

namespace component\request;


use application\BaseComponent;

/**
 * Class Request
 * @package component\request
 * @version 1,0
 */
class Request extends BaseComponent{

    /**
     * The id of the provider
     * @var int
     */
    protected $providerId = 0;

    /**
     * The provider name (default value for isoftbet is null)
     * @var string
     */
    protected $providerName = null;

    /**
     * The id of the licensee e.g 65
     * @var int
     */
    protected $licenseeId;
    /**
     * The id of the game
     * @var int
     */
    protected $gameId;
    /**
     * The id relative to the game id
     * @var int
     */
    protected $skinId;
    /**
     * The configuration id of the licensee
     * @var int
     */
    protected $configurationId;
    /**
     * The regulation number(id) of the licensee
     * @var int
     */
    protected $regulationId;
    /**
     * The operator id
     * @var string
     */
    protected $operatorId;/**
     * The tournament id
     * @var int
     */
    protected $tournamentId;
    /**
     * The value of the coin in cents
     * @var int
     */
    protected $coinValue;
    /**
     * The internal progressive id
     * @var int
     */
    protected $internalProgressiveId;
    /**
     * The name of an operator that belongs to a given licensee
     * @var string
     */
    protected  $operatorName;
    /**
     * The hash string for a request
     * @var string
     */
    protected $hashInformation;
    /**
     * Name of the requested action
     * @var string
     */
    protected $methodName;
    /**
     * The id of the player
     * @var string
     */
    protected $playerId;
    /**
     * Contains all $_REQUEST variables
     * @var string
     */
    protected  $allRequest;
    /**
     * Contains comma separated values
     * @var string
     */
    protected $pp;
    /**
     * Contains comma separated values
     * @var string
     */
    protected $loginC;
    /**
     * Contains comma separated values
     * @var string
     */
    protected $passC;
    /*
     * An object containing the licensee details
     * @var \model\gmapi\GmapiLicensee
     */
    protected $licenseeObject;

    /**
     * The session id from the pp parameter
     * @var string
     */
    protected $sessionId;
    /**
     * The username from the pp parameter
     * @var string
     */
    protected $username;
    /**
     * The currency from the pp parameter
     * @var string
     */
    protected $currency;
    /**
     * The country from the pp parameter
     * @var string
     */
    protected $country;
    /**
     * The mode from the pp parameter
     * @var string
     */
    protected $mode;

    protected $required = false;

    /**
     * Set for game calls 
     * @var boolean
     */
    protected $callGame = true;

    const PASSWORD = 0;

    const USERNAME = 1;

    const CURRENCY = 2;

    const COUNTRY = 3;

    const MODE = 4;

    public function __construct()
    {

        parent::__construct();
        $this->parseDefaultRequest();
    }

    /**
     * Loads a component based on current version
     * @return $this|\component\request\Request
     */
    public function loadComponent()
    {
        return $this->selectVersion($this->currentVersion);
    }

    /**
     * @param $version
     * @return $this|\component\request\Request
     */
    protected function selectVersion($version)
    {
        switch($version)
        {
            default:
                return $this;
        }
    }

    /**
     * @return int
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * @param int $providerId
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @param string $providerName
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
    }



    /**
     * @param int $coinValue
     */
    public function setCoinValue($coinValue)
    {
        $this->coinValue = $coinValue;
    }

    /**
     * @return int
     */
    public function getCoinValue()
    {
        return $this->coinValue;
    }

    /**
     * @param int $configurationId
     */
    public function setConfigurationId($configurationId)
    {
        $this->configurationId = $configurationId;
    }

    /**
     * @return int
     */
    public function getConfigurationId()
    {
        return $this->configurationId;
    }


    /**
     * @param int $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
    }

    /**
     * @return int
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param string $hashInformation
     */
    public function setHashInformation($hashInformation)
    {
        $this->hashInformation = $hashInformation;
    }

    /**
     * @return string
     */
    public function getHashInformation()
    {
        return $this->hashInformation;
    }

    /**
     * @param int $internalProgressiveId
     */
    public function setInternalProgressiveId($internalProgressiveId)
    {
        $this->internalProgressiveId = $internalProgressiveId;
    }

    /**
     * @return int
     */
    public function getInternalProgressiveId()
    {
        return $this->internalProgressiveId;
    }

    /**
     * @param int $licenseeId
     */
    public function setLicenseeId($licenseeId)
    {
        $this->licenseeId = $licenseeId;
    }

    /**
     * @return int
     */
    public function getLicenseeId()
    {
        return $this->licenseeId;
    }

    /**
     * @param string $methodName
     */
    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @param string $operatorId
     */
    public function setOperatorId($operatorId)
    {
        $this->operatorId = $operatorId;
    }

    /**
     * @return string
     */
    public function getOperatorId()
    {
        return $this->operatorId;
    }

    /**
     * @param string $operatorName
     */
    public function setOperatorName($operatorName)
    {
        $this->operatorName = $operatorName;
    }

    /**
     * @return string
     */
    public function getOperatorName()
    {
        return $this->operatorName;
    }

    /**
     * @param string $playerId
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
    }

    /**
     * @return string
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param string $loginC
     */
    public function setLoginC($loginC)
    {
        $this->loginC = $loginC;
    }

    /**
     * @return string
     */
    public function getLoginC()
    {
        return $this->loginC;
    }

    /**
     * @param string $passC
     */
    public function setPassC($passC)
    {
        $this->loginC = $passC;
    }

    /**
     * @return string
     */
    public function getPassC()
    {
        return $this->passC;
    }

    /**
     * @param int $regulationId
     */
    public function setRegulationId($regulationId)
    {
        $this->regulationId = $regulationId;
    }

    /**
     * @return int
     */
    public function getRegulationId()
    {
        return $this->regulationId;
    }


    /**
     * @param int $skinId
     */
    public function setSkinId($skinId)
    {
        $this->skinId = $skinId;
    }

    /**
     * @return int
     */
    public function getSkinId()
    {
        return $this->skinId;
    }

    /**
     * @param string $allRequest
     */
    public function setAllRequest($allRequest)
    {
        $this->allRequest = $allRequest;
    }

    /**
     * @return string
     */
    public function getAllRequest()
    {
        return $this->allRequest;
    }

    /**
     * @param string $pp
     */
    public function setPp($pp)
    {
        $this->pp = $pp;
    }

    /**
     * @return string
     */
    public function getPp()
    {
        return $this->pp;
    }

    /**
     * @param \model\gmapi\GmapiLicensee $licenseeObject
     */
    public function setLicenseeObject($licenseeObject)
    {
        $this->licenseeObject = $licenseeObject;
    }

    /**
     * @return \model\gmapi\GmapiLicensee
     */
    public function getLicenseeObject()
    {
        return $this->licenseeObject;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @param int $tournamentId
     */
    public function setTournamentId($tournamentId)
    {
        $this->tournamentId = $tournamentId;
    }

    /**
     * @return int
     */
    public function getTournamentId()
    {
        return $this->tournamentId;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }





    public function parseDefaultRequest()
    {
        $request = $_REQUEST;
        $this->setProviderId(((isset($request['pid'])) ? $request['pid'] : 0));
        $this->setProviderName(((isset($request['pname'])) ? $request['pname'] : null));
        $this->setMethodName(((isset($request['method_name'])) ? $request['method_name'] : null));
        $this->setCoinValue(((isset($request['cv'])) ? $request['cv'] : null));
        $this->setConfigurationId(((isset($request['cid'])) ? $request['cid'] : null));
        $this->setGameId(((isset($request['gid'])) ? $request['gid'] : null));
        $this->setHashInformation(((isset($request['hinfo'])) ? $request['hinfo'] : null));
        $this->setInternalProgressiveId(((isset($request['progid'])) ? $request['progid'] : ((isset($request['softId'])) ? $request['softId'] : null) ));

        if(isset($request['loginsc']))
        {
            $this->setLoginC($request['loginsc']);
            list($lid, $username, $operatorName) = explode(",", $request['loginsc']);
        }
        else
        {
            $lid = ((isset($request['lid'])) ? $request['lid'] : null);
            $operatorName = ((isset($request['operator'])) ? $request['operator'] : null);
            $username = ((isset($request['lp'])) ? $request['lp'] : null);
        }

        $this->setPassC(((isset($request['passc'])) ? $request['passc'] : null));
        $this->setLicenseeId($lid);
        $this->setOperatorId(((isset($request['oid'])) ? $request['oid'] : null));
        $this->setOperatorName($operatorName);
        $this->setPlayerId($username);
        $this->setRegulationId(((isset($request['rid'])) ? $request['rid'] : null));
        $this->setSkinId(((isset($request['sid'])) ? $request['sid'] : null));

        $this->setPp(((isset($request['pp'])) ? $request['pp'] : null));

        if($this->methodName !== 'progreadcurr' || $this->pp !== 'fun')
        {
            $temp = explode(',', $this->pp );

            if( isset($temp[self::PASSWORD]) )  $this->sessionId        = $temp[self::PASSWORD];
            if( isset($temp[self::USERNAME]) )  $this->username         = $temp[self::USERNAME];
            if( isset($temp[self::CURRENCY]) )  $this->currency         = $temp[self::CURRENCY];
            if( isset($temp[self::COUNTRY]) )   $this->country          = $temp[self::COUNTRY];
            if( isset($temp[self::MODE]) )      $this->mode             = $temp[self::MODE];
        }
        $this->allRequest = $request;
    }

    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Types of game calls: blogin/sbet/pbet... etc. 
     */
    public function isGameTypeCall()
    {
        return $this->callGame;
    }
} 