<?php

/**
 * Description of AccountingRound
 * This class will have some methods to get array of component\accounting\v1\entities\AccountingWagers for a given roundid
 * 
 * @author rafal
 */

namespace component\accounting\v1\entities;

use model\GMAPIDataRepository;

class AccountingRound {

    private $repo;

    /**
     *
     * @var null|\component\request\Request|\component\request\BeforeLoginRequest|\component\request\PlaceBetRequest|\component\request\PlaceAndSettleBetRequest|\component\request\SettleBetRequest
     */
    private $request;
    private $bets = array();
    private $wins = array();
    private $roundId;

    public function __construct($request, $roundId)
    {
        $this->request = $request;
        $dbName = $this->request->getLicenseeObject()->getDbPrefix() . '_accounting';
        
        $this->roundId = $roundId;

        if ($this->request->getProviderName() != '')
        {
            GMAPIDataRepository::$dbProviderPrefix = $this->request->getProviderName() . '_';
        }
        $this->repo = new GMAPIDataRepository($dbName);
        $wagerIds = $this->repo->fetchAccountingWagersIdsForRound($this->roundId);

        foreach ($wagerIds as $id)
        {
            $aw = new AccountingWagers($this->request, $id);
            if ($aw->getWagerType() == 'BET' || $aw->getWagerType() == 'FREE_ROUND_BET')
            {
                $this->bets[] = $aw;
            } else
            {
                $this->wins = $aw;
            }
        }
    }

    public function getCountWins()
    {
        return count($this->wins);
    }

    public function getCountBets()
    {
        return count($this->bets);
    }

    public function getBets()
    {
        return $this->bets;
    }

    public function getWins()
    {
        return $this->wins;
    }

}
