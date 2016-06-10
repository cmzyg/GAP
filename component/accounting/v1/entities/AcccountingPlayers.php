<?php

/**
 * Description of AcccountingPlayers
 *
 * @author rafal
 */

namespace component\accounting\v1\entities;

class AcccountingPlayers extends AbstractAccountingEntity {
    //table
    protected $table = 'accounting_players';
    //fields
    protected $playerId; //required
    protected $userName=NULL;
    protected $sessionId; //required
    protected $gameId; //required
    protected $skindId; //required
    protected $betsSum=0.0;
    protected $winsSum=0.0;
    protected $currencyCode; //required
    protected $countryCode=NULL;
    protected $jackpotContributionSum=0.0;
    protected $jackpotWinSum=0.0;
    protected $sessionStartDate='0000-00-00 00:00:00';
    protected $sessionEndDate='0000-00-00 00:00:00';
    protected $sessionStatus='ACTIVE';
    protected $userType='real';
    protected $operator='0';
    protected $channel=NULL;
    protected $startCashBalance=0.0;
    protected $startFreebetBalance=0.0;
    protected $endCashBalance=0.0;
    protected $endFreebetBalance=0.0;
    protected $obGameId=NULL;
    
    
    /**
     * Creates record of accounting_players with required fields
     * @param type $playerId
     * @param type $sessionId
     * @param type $gameId
     * @param type $skinId
     * @param type $currencyCode
     */
    public function create($playerId,$sessionId,$gameId,$skinId,$currencyCode){
        $this->playerId=$playerId;
        $this->sessionId=$sessionId;
        $this->gameId=$gameId;
        $this->skinId=$skinId;
        $this->currencyCode=$currencyCode;
        $this->arrFields= array('playerId','sessionId','gameId','skinId','currencyCode');
        
        return $this;
    }
    
    public function getPlayerId()
    {
        return $this->playerId;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function getGameId()
    {
        return $this->gameId;
    }

    public function getSkindId()
    {
        return $this->skindId;
    }

    public function getBetsSum()
    {
        return $this->betsSum;
    }

    public function getWinsSum()
    {
        return $this->winsSum;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function getCountryCode()
    {
        return $this->countryCode;
    }

    public function getJackpotContributionSum()
    {
        return $this->jackpotContributionSum;
    }

    public function getJackpotWinSum()
    {
        return $this->jackpotWinSum;
    }

    public function getSessionStartDate()
    {
        return $this->sessionStartDate;
    }

    public function getSessionEndDate()
    {
        return $this->sessionEndDate;
    }

    public function getSessionStatus()
    {
        return $this->sessionStatus;
    }

    public function getUserType()
    {
        return $this->userType;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function getStartCashBalance()
    {
        return $this->startCashBalance;
    }

    public function getStartFreebetBalance()
    {
        return $this->startFreebetBalance;
    }

    public function getEndCashBalance()
    {
        return $this->endCashBalance;
    }

    public function getEndFreebetBalance()
    {
        return $this->endFreebetBalance;
    }

    public function getObGameId()
    {
        return $this->obGameId;
    }

    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
        $this->arrFields[]='playerId';
        return $this;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
        $this->arrFields[]='userName';
        return $this;
    }

    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->arrFields[]='sessionId';
        return $this;
    }

    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
        $this->arrFields[]='gameId';
        return $this;
    }

    public function setSkindId($skindId)
    {
        $this->skindId = $skindId;
        $this->arrFields[]='skindId';
        return $this;
    }

    public function setBetsSum($betsSum)
    {
        $this->betsSum = $betsSum;
        $this->arrFields[]='betsSum';
        return $this;
    }

    public function setWinsSum($winsSum)
    {
        $this->winsSum = $winsSum;
        $this->arrFields[]='winsSum';
        return $this;
    }

    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
        $this->arrFields[]='currencyCode';
        return $this;
    }

    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        $this->arrFields[]='countryCode';
        return $this;
    }

    public function setJackpotContributionSum($jackpotContributionSum)
    {
        $this->jackpotContributionSum = $jackpotContributionSum;
        $this->arrFields[]='jackpotContributionSum';
        return $this;
    }

    public function setJackpotWinSum($jackpotWinSum)
    {
        $this->jackpotWinSum = $jackpotWinSum;
        $this->arrFields[]='jackpotWinSum';
        return $this;
    }

    public function setSessionStartDate($sessionStartDate)
    {
        $this->sessionStartDate = $sessionStartDate;
        $this->arrFields[]='sessionStartDate';
        return $this;
    }

    public function setSessionEndDate($sessionEndDate)
    {
        $this->sessionEndDate = $sessionEndDate;
        $this->arrFields[]='sessionEndDate';
        return $this;
    }

    public function setSessionStatus($sessionStatus)
    {
        $this->sessionStatus = $sessionStatus;
        $this->arrFields[]='sessionStatus';
        return $this;
    }

    public function setUserType($userType)
    {
        $this->userType = $userType;
        $this->arrFields[]='userType';
        return $this;
    }

    public function setOperator($operator)
    {
        $this->operator = $operator;
        $this->arrFields[]='operator';
        return $this;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
        $this->arrFields[]='channel';
        return $this;
    }

    public function setStartCashBalance($startCashBalance)
    {
        $this->startCashBalance = $startCashBalance;
        $this->arrFields[]='startCashBalance';
        return $this;
    }

    public function setStartFreebetBalance($startFreebetBalance)
    {
        $this->startFreebetBalance = $startFreebetBalance;
        $this->arrFields[]='startFreebetBalance';
        return $this;
    }

    public function setEndCashBalance($endCashBalance)
    {
        $this->endCashBalance = $endCashBalance;
        $this->arrFields[]='endCashBalance';
        return $this;
    }

    public function setEndFreebetBalance($endFreebetBalance)
    {
        $this->endFreebetBalance = $endFreebetBalance;
        $this->arrFields[]='endFreebetBalance';
        return $this;
    }

    public function setObGameId($obGameId)
    {
        $this->obGameId = $obGameId;
        $this->arrFields[]='obGameId';
        return $this;
    }

 


    
}
