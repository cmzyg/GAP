<?php

/**
 * Description of AccountingProgressive
 *
 * @author rafal
 */

namespace component\accounting\v1\entities;

class AccountingProgressive extends AbstractAccountingEntity {

    //table
    protected $table = 'accounting_progressive';
    //fields
    protected $accountingWagerId;
    protected $jackpotContributionAmount;
    protected $jackpotWinAmount;
    protected $jackpotValues;
    protected $jackpotType;
    protected $jackpotDate;
    protected $coinValue;

    function getAccountingWagerId()
    {
        return $this->accountingWagerId;
    }

    function getJackpotContributionAmount()
    {
        return $this->jackpotContributionAmount;
    }

    function getJackpotWinAmount()
    {
        return $this->jackpotWinAmount;
    }

    function getJackpotValues()
    {
        return $this->jackpotValues;
    }

    function getJackpotType()
    {
        return $this->jackpotType;
    }

    function getJackpotDate()
    {
        return $this->jackpotDate;
    }

    function getCoinValue()
    {
        return $this->coinValue;
    }

    function setAccountingWagerId($accountingWagerId)
    {
        $this->accountingWagerId = $accountingWagerId;
        return $this;
    }

    function setJackpotContributionAmount($jackpotContributionAmount)
    {
        $this->jackpotContributionAmount = $jackpotContributionAmount;
        return $this;
    }

    function setJackpotWinAmount($jackpotWinAmount)
    {
        $this->jackpotWinAmount = $jackpotWinAmount;
        return $this;
    }

    function setJackpotValues($jackpotValues)
    {
        $this->jackpotValues = $jackpotValues;
        return $this;
    }

    function setJackpotType($jackpotType)
    {
        $this->jackpotType = $jackpotType;
        return $this;
    }

    function setJackpotDate($jackpotDate)
    {
        $this->jackpotDate = $jackpotDate;
        return $this;
    }

    function setCoinValue($coinValue)
    {
        $this->coinValue = $coinValue;
        return $this;
    }

}
