<?php

/**
 * Description of AccountingWagers
 *
 * @author rafal
 */

namespace component\accounting\v1\entities;

class AccountingWagers extends AbstractAccountingEntity {
    
    //table
    protected $table = 'accounting_wagers';
    //fields
    protected $accountingPlayersId;
    protected $betAmount;
    protected $freeBetAmount;
    protected $winAmount;
    protected $wagerType;
    protected $wagerDate;
    protected $playerBalance;
    protected $playerFreebetBalance;
    protected $coinValue;
    protected $roundId;
    protected $transactionId;
    protected $freeSpin;
    protected $roundStatus;
    protected $wagerStatus;
    
    public function create($wagerType,$accountingPlayersId,$roundId,$transactionId){
        $this->wagerType=$wagerType;
        $this->accountingPlayersId=$accountingPlayersId;
        $this->roundId=$roundId;
        $this->transactionId=$transactionId;
        $this->arrFields= array('wagerType','accountingPlayersId','roundId','transactionId');
        
        return $this;
    }
    
    
    public function getAccountingPlayersId()
    {
        return $this->accountingPlayersId;
    }

    public function getBetAmount()
    {
        return $this->betAmount;
    }

    public function getFreeBetAmount()
    {
        return $this->freeBetAmount;
    }

    public function getWinAmount()
    {
        return $this->winAmount;
    }

    public function getWagerType()
    {
        return $this->wagerType;
    }

    public function getWagerDate()
    {
        return $this->wagerDate;
    }

    public function getPlayerBalance()
    {
        return $this->playerBalance;
    }

    public function getPlayerFreebetBalance()
    {
        return $this->playerFreebetBalance;
    }

    public function getCoinValue()
    {
        return $this->coinValue;
    }

    public function getRoundId()
    {
        return $this->roundId;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function getFreeSpin()
    {
        return $this->freeSpin;
    }

    public function getRoundStatus()
    {
        return $this->roundStatus;
    }

    public function getWagerStatus()
    {
        return $this->wagerStatus;
    }

    public function setAccountingPlayersId($accountingPlayersId)
    {
        $this->accountingPlayersId = $accountingPlayersId;
        $this->arrFields[]='accountingPlayersId';
        return $this;
    }

    public function setBetAmount($betAmount)
    {
        $this->betAmount = $betAmount;
        $this->arrFields[]='betAmount';
        return $this;
    }

    public function setFreeBetAmount($freeBetAmount)
    {
        $this->freeBetAmount = $freeBetAmount;
        $this->arrFields[]='freeBetAmount';
        return $this;
    }

    public function setWinAmount($winAmount)
    {
        $this->winAmount = $winAmount;
        $this->arrFields[]='winAmount';
        return $this;
    }

    public function setWagerType($wagerType)
    {
        $this->wagerType = $wagerType;
        $this->arrFields[]='wagerType';
        return $this;
    }

    public function setWagerDate($wagerDate)
    {
        $this->wagerDate = $wagerDate;
        $this->arrFields[]='wagerDate';
        return $this;
    }

    public function setPlayerBalance($playerBalance)
    {
        $this->playerBalance = $playerBalance;
        $this->arrFields[]='playerBalance';
        return $this;
    }

    public function setPlayerFreebetBalance($playerFreebetBalance)
    {
        $this->playerFreebetBalance = $playerFreebetBalance;
        $this->arrFields[]='playerFreebetBalance';
        return $this;
    }

    public function setCoinValue($coinValue)
    {
        $this->coinValue = $coinValue;
        $this->arrFields[]='coinValue';
        return $this;
    }

    public function setRoundId($roundId)
    {
        $this->roundId = $roundId;
        $this->arrFields[]='roundId';
        return $this;
    }

    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        $this->arrFields[]='transactionId';
        return $this;
    }

    public function setFreeSpin($freeSpin)
    {
        $this->freeSpin = $freeSpin;
        $this->arrFields[]='freeSpin';
        return $this;
    }

    public function setRoundStatus($roundStatus)
    {
        $this->roundStatus = $roundStatus;
        $this->arrFields[]='roundStatus';
        return $this;
    }

    public function setWagerStatus($wagerStatus)
    {
        $this->wagerStatus = $wagerStatus;
        $this->arrFields[]='wagerStatus';
        return $this;
    }


}
