<?php

/**
 * Description of AccountinGames
 *
 * @author rafal
 */

namespace component\accounting\v1\entities;

class AccountingGames extends AbstractAccountingEntity {
    
    //table
    protected $table = 'accounting_games';
    //fields
    protected $gameId;
    protected $skinId;
    protected $totalBetsCash;
    protected $totalWinsCash;
    protected $dailyBetsCash;
    protected $dailyWinsCash;
    protected $coinValue;
    protected $totalSpins;
    protected $gameGenerationVersion;

    function getGameId()
    {
        return $this->gameId;
    }

    function getSkinId()
    {
        return $this->skinId;
    }

    function getTotalBetsCash()
    {
        return $this->totalBetsCash;
    }

    function getTotalWinsCash()
    {
        return $this->totalWinsCash;
    }

    function getDailyBetsCash()
    {
        return $this->dailyBetsCash;
    }

    function getDailyWinsCash()
    {
        return $this->dailyWinsCash;
    }

    function getCoinValue()
    {
        return $this->coinValue;
    }

    function getTotalSpins()
    {
        return $this->totalSpins;
    }

    function getGameGenerationVersion()
    {
        return $this->gameGenerationVersion;
    }

    function setGameId($gameId)
    {
        $this->gameId = $gameId;
        return $this;
    }

    function setSkinId($skinId)
    {
        $this->skinId = $skinId;
        return $this;
    }

    function setTotalBetsCash($totalBetsCash)
    {
        $this->totalBetsCash = $totalBetsCash;
        return $this;
    }

    function setTotalWinsCash($totalWinsCash)
    {
        $this->totalWinsCash = $totalWinsCash;
        return $this;
    }

    function setDailyBetsCash($dailyBetsCash)
    {
        $this->dailyBetsCash = $dailyBetsCash;
        return $this;
    }

    function setDailyWinsCash($dailyWinsCash)
    {
        $this->dailyWinsCash = $dailyWinsCash;
        return $this;
    }

    function setCoinValue($coinValue)
    {
        $this->coinValue = $coinValue;
        return $this;
    }

    function setTotalSpins($totalSpins)
    {
        $this->totalSpins = $totalSpins;
        return $this;
    }

    function setGameGenerationVersion($gameGenerationVersion)
    {
        $this->gameGenerationVersion = $gameGenerationVersion;
        return $this;
    }


}
