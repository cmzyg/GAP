<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 28/10/14
 * Time: 09:49
 */

namespace component\wallet\nyx\v2;


class NyxRequestData {

    private $apiVersion = "1.2";

    private $clientType;

    private $currency;

    private $gameProviderGameId;

    private $gameProviderId;

    private $loginName;

    private $operatorId;

    private $password;

    private $request;

    private $sessionId;

    private $accountId;

    private $betAmount;

    private $transactionId;

    private $roundId;

    private $wonAmount;

    private $gameStatus;

    private $rollBackAmount;

    private $campaignId;

    private $activationId;

    private $requestData;


    public function __construct()
    {
        $this->requestData = array();
        $this->requestData['apiversion'] = $this->apiVersion;
    }

    /**
     * @param string $apiVersion
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
        $this->requestData['apiversion'] = $this->apiVersion;
    }

    /**
     * @param mixed $clientType
     */
    public function setClientType($clientType)
    {
        $this->clientType = $clientType;
        $this->requestData['clienttype'] = $this->clientType;
    }


    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        $this->requestData['currency'] = $this->currency;
    }



    /**
     * @param mixed $gameProviderGameId
     */
    public function setGameProviderGameId($gameProviderGameId)
    {
        $this->gameProviderGameId = $gameProviderGameId;
        $this->requestData['gpgameid'] = $this->gameProviderGameId;
    }

    /**
     * @param mixed $gameProviderId
     */
    public function setGameProviderId($gameProviderId)
    {
        $this->gameProviderId = $gameProviderId;
        $this->requestData['gpid'] = $this->gameProviderId;
    }

    /**
     * @param mixed $loginName
     */
    public function setLoginName($loginName)
    {
        $this->loginName = $loginName;
        $this->requestData['loginname'] = $this->loginName;
    }

    /**
     * @param mixed $operatorId
     */
    public function setOperatorId($operatorId)
    {
        $this->operatorId = $operatorId;
        $this->requestData['opid'] = $this->operatorId;
    }

    /**
     * @param mixed $passowrd
     */
    public function setPassword($passowrd)
    {
        $this->password = $passowrd;
        $this->requestData['password'] = $this->password;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
        $this->requestData['request'] = $this->request;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->requestData['sessionid'] = $this->sessionId;
    }

    /**
     * @return array
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * @param mixed $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        $this->requestData['accountid'] = $this->accountId;
    }

    /**
     * @param mixed $betAmount
     */
    public function setBetAmount($betAmount)
    {
        $this->betAmount = $betAmount;
        $this->requestData['betamount'] = $this->betAmount;
    }

    /**
     * @param mixed $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        $this->requestData['transactionid'] = $this->transactionId;
    }

    /**
     * @param mixed $roundId
     */
    public function setRoundId($roundId)
    {
        $this->roundId = $roundId;
        $this->requestData['roundid'] = $this->roundId;
    }

    /**
     * @param mixed $gameStatus
     */
    public function setGameStatus($gameStatus)
    {
        $this->gameStatus = $gameStatus;
        $this->requestData['gamestatus'] = $this->gameStatus;
    }

    /**
     * @param mixed $wonAmount
     */
    public function setWonAmount($wonAmount)
    {
        $this->wonAmount = $wonAmount;
        $this->requestData['wonamount'] = $this->wonAmount;
    }

    /**
     * @param mixed $rollBackAmount
     */
    public function setRollBackAmount($rollBackAmount)
    {
        $this->rollBackAmount = $rollBackAmount;
        $this->requestData['rollbackamount'] = $this->rollBackAmount;
    }

    /**
     * @param mixed $activationId
     */
    public function setActivationId($activationId)
    {
        $this->activationId = $activationId;
        $this->requestData['activationid'] = $this->activationId;
    }

    /**
     * @param mixed $campaignId
     */
    public function setCampaignId($campaignId)
    {
        $this->campaignId = $campaignId;
        $this->requestData['campaignid'] = $this->campaignId;
    }

} 