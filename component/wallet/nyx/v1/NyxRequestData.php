<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 28/10/14
 * Time: 09:49
 */

namespace component\wallet\nyx\v1;


class NyxRequestData {

    private $callerAuthentication;
    private $callerPassword;
    private $requestMethodName;
    private $sessionId;
    private $gameId;
    private $accountId;
    private $roundId;
    private $betAmount;
    private $description;
    private $transactionId;
    private $gameClientType;
    private $jackpotContribution;
    private $jackpotWin;
    private $result;
    private $gameStatus;
    private $cancelWagerAmount;
    private $requestData = array();

    /**
     * @param $callerAuthentication
     * @param $callerPassword
     * @param $sessionId
     * @param $requestMethodName
     */
    public function __construct($callerAuthentication, $callerPassword, $sessionId, $requestMethodName)
    {
        $this->setCallerAuthentication($callerAuthentication);
        $this->setCallerPassword($callerPassword);
        $this->setSessionId($sessionId);
        $this->setRequestMethodName($requestMethodName);
    }


    /**
     * @param mixed $callerAuthentication
     */
    public function setCallerAuthentication($callerAuthentication)
    {
        $this->callerAuthentication = $callerAuthentication;
        $this->requestData['callerauth'] = $this->callerAuthentication;
    }

    /**
     * @return mixed
     */
    public function getCallerAuthentication()
    {
        return $this->callerAuthentication;
    }

    /**
     * @param mixed $callerPassword
     */
    public function setCallerPassword($callerPassword)
    {
        $this->callerPassword = $callerPassword;
        $this->requestData['callerpassword'] = $this->callerPassword;
    }

    /**
     * @return mixed
     */
    public function getCallerPassword()
    {
        return $this->callerPassword;
    }

    /**
     * @param mixed $request
     */
    public function setRequestMethodName($request)
    {
        $this->requestMethodName = $request;
        $this->requestData['request'] = $this->requestMethodName;
    }

    /**
     * @return mixed
     */
    public function getRequestMethodName()
    {
        return $this->requestMethodName;
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
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
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
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
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
     * @return mixed
     */
    public function getBetAmount()
    {
        return $this->betAmount;
    }

    /**
     * @param mixed $cancelWagerAmount
     */
    public function setCancelWagerAmount($cancelWagerAmount)
    {
        $this->cancelWagerAmount = $cancelWagerAmount;
        $this->requestData['cancelwageramount'] = $this->cancelWagerAmount;
    }

    /**
     * @return mixed
     */
    public function getCancelWagerAmount()
    {
        return $this->cancelWagerAmount;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        $this->requestData['description'] = $this->description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $gameClientType
     */
    public function setGameClientType($gameClientType)
    {
        $this->gameClientType = $gameClientType;
        $this->requestData['gameclienttype'] = $this->gameClientType;
    }

    /**
     * @return mixed
     */
    public function getGameClientType()
    {
        return $this->gameClientType;
    }

    /**
     * @param mixed $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
        $this->requestData['gameid'] = $this->gameId;
    }

    /**
     * @return mixed
     */
    public function getGameId()
    {
        return $this->gameId;
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
     * @return mixed
     */
    public function getGameStatus()
    {
        return $this->gameStatus;
    }

    /**
     * @param mixed $jackpotContribution
     */
    public function setJackpotContribution($jackpotContribution)
    {
        $this->jackpotContribution = $jackpotContribution;
        $this->requestData['jackpotcontribution'] = $this->jackpotContribution;
    }

    /**
     * @return mixed
     */
    public function getJackpotContribution()
    {
        return $this->jackpotContribution;
    }

    /**
     * @param mixed $jackpotWin
     */
    public function setJackpotWin($jackpotWin)
    {
        $this->jackpotWin = $jackpotWin;
        $this->requestData['jackpotwin'] = $this->jackpotWin;
    }

    /**
     * @return mixed
     */
    public function getJackpotWin()
    {
        return $this->jackpotWin;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
        $this->requestData['result'] = $this->result;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
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
     * @return mixed
     */
    public function getRoundId()
    {
        return $this->roundId;
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
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return array
     */
    public function getRequestData()
    {
        return $this->requestData;
    }


} 