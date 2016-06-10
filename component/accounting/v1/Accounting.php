<?php


namespace component\accounting\v1;

use component\accounting\v1\entities\AcccountingPlayers;
//use component\accounting\v1\entities\AccountingProgressive;
//use component\accounting\v1\entities\AccountingGames;
use component\accounting\v1\entities\AccountingWagers;
use exceptions\AccountingException;
use model\GMAPIDataRepository;

//use exceptions\AccountingException;

/**
 * Class Accounting
 * @package component\accounting\v1
 * @version 1.0
 */
class Accounting extends \component\accounting\Accounting {

    /**
     *
     * @var null|\component\request\Request|\component\request\BeforeLoginRequest|\component\request\PlaceBetRequest|\component\request\PlaceAndSettleBetRequest|\component\request\SettleBetRequest
     */
    private $request;

    private $accountingStatus = false;

    public function startSession($userBalance, $startFreebetBalance = 0, $startCashBalance = 0)
    {
        try
        {
            $ap = new AcccountingPlayers($this->request);

            $ap->create($this->request->getPlayerId(), $this->request->getSessionId(), $this->request->getGameId(), $this->request->getSkinId(), $this->request->getCurrency())
                ->setUserName($this->request->getPlayerId())
                ->setCountryCode($this->request->getCountry())
                ->setOperator($this->request->getOperatorName())
                ->setSessionStartDate($this->getGMTTimeNow())
                ->setUserType($userBalance)
                ->setStartCashBalance($userBalance)
                ->setStartFreebetBalance($startFreebetBalance)
                ->setObGameId($this->request->getSkinId());



            /* if($this->request->getProviderId()==2){
                $pp = explode(',',$this->request->getPp());
                $ap->setObGameId($pp[5]);
            } */

            $ap->insert();

        }catch (\Exception $ex)
        {
            $newEx = new AccountingException("Failed to create player session", 2,0);
            self::getLogger()->addException($newEx);
            $this->errorMessage = $newEx->getMessage();

        }
    }

    /**
     * Performs the accounting for bet
     * @param $playerBalance
     */
    public function betSession($playerBalance)
    {
        try
        {
            $amount = $this->request->getBetAmount() / 100;

            $ap = new AcccountingPlayers($this->request);
            $ap->fetch(array('session_id' => $this->request->getSessionId()));

            $aw = new AccountingWagers($this->request);
            $aw->create('BET', $ap->getId(), $this->request->getRoundId(), $this->request->getTransactionId())
                ->setBetAmount($amount)
                ->setPlayerBalance($playerBalance / 100)
                ->setCoinValue($this->request->getCoinValue())
                ->insert();

            $ap->setBetsSum($ap->getBetsSum() + $amount)
                ->update();
        }catch (\Exception $ex)
        {
            $newEx = new AccountingException("Failed to create wager", 2, 2);
            self::getLogger()->addException($newEx);
            $this->errorMessage = $newEx->getMessage();
        }
    }

    public function winSession($playerBalance, $roundStatus = 'INACTIVE')
    {

        try
        {
            $amount = $this->request->getWinAmount() / 100;
            $this->getLogger()->log('acc', array('win' => $amount));
            $ap = new AcccountingPlayers($this->request);
            $ap->fetch(array('session_id' => $this->request->getSessionId()));

            $aw = new AccountingWagers($this->request);
            $aw->create('WIN', $ap->getId(), $this->request->getRoundId(), $this->request->getTransactionId())
                ->setWinAmount($amount)
                ->setPlayerBalance($playerBalance / 100)
                ->setCoinValue($this->request->getCoinValue())
                ->setFreeSpin($this->request->getFreeSpin() == 2 ? 1 : $this->request->getFreeSpin())
                ->setRoundStatus($roundStatus)
                ->insert();

            $ap->setWinsSum($ap->getWinsSum() + $amount)
                ->update();
            if(strtoupper($roundStatus) === "INACTIVE" || strtoupper($roundStatus) === "ERROR")
            {
                $value = $aw->getRepo()->updateRoundStatus($this->request->getRoundId(), $roundStatus);

                if($value === false)
                {
                    $exp = new AccountingException("Failed to update round status", 2, 6);
                    self::getLogger()->addException($exp);
                }
            }

        }catch (\Exception $ex)
        {
            $newEx = new AccountingException("Failed to create wager", 2, 2);
            self::getLogger()->addException($newEx);
            $this->errorMessage = $newEx->getMessage();
        }
    }

    public function endSession($userBalance = NULL, $endFreebetBalance = NULL, $endCashBalance = NULL)
    {
        try
        {
            $ap = new AcccountingPlayers($this->request);
            $ap->fetch(array('session_id' => $this->request->getSessionId()));
            $ap->setSessionEndDate($this->getGMTTimeNow())
                ->setSessionStatus('INACTIVE')
                ->update();
        }catch (\Exception $ex)
        {
            $newEx = new AccountingException("Failed to update player wager", 2,1);
            self::getLogger()->addException($newEx);
            $this->errorMessage = $newEx->getMessage();
        }
    }

    /**
     * @param $coinValue
     * @param $jackpotContribution
     * @param $jackpotJson
     * @return bool
     */
    public function registerJackPotContribution($coinValue, $jackpotContribution, $jackpotJson)
    {
        $databaseName = $this->request->getLicenseeObject()->getDbPrefix() . "_accounting";
        $data = new GMAPIDataRepository($databaseName);

        try
        {
            $lastId = $data->getLastPlaceBet($this->request->getSessionId(), $this->request->getSkinId());

            if(is_array($lastId) && sizeof($lastId) > 0)
            {
                $lastId = $lastId[0]['lastID'];
            }
            else
            {
                $lastId = $data->insertAWagerToAccountingWagers($this->request->getSkinId(), $this->request->getSessionId(), $coinValue);

                if($lastId === false)
                {
                    throw new AccountingException("Failed to get last placebet id after inserting fake bet", 4);

                }
            }

            $result = $data->updatePlaceBetProgressiveSessionForPlayer($jackpotContribution, $this->request->getSessionId(), $this->request->getPlayerId());

            if($result === false)
            {
                throw new AccountingException("Failed to update place bet progressive session", 4);
            }

            $result = null;

            $result = $data->insertPlaceBetPlayerProgressive($lastId, $coinValue, $jackpotContribution, $jackpotJson);

            if($result === false)
            {
                throw new AccountingException("Failed to insert place bet progressive ", 4);
            }

            return true;
        }catch (\Exception $ex)
        {
            self::getLogger()->addException($ex);
            $this->processResponse = $ex->getMessage();
            $this->processStatus = false;
            return false;
        }



    }


    public function registerJackPotWin($coinValue, $jackpotWin, $jackpotJson)
    {
        $databaseName = $this->request->getLicenseeObject()->getDbPrefix() . "_accounting";
        $data = new GMAPIDataRepository($databaseName);

        try
        {
            $lastId = $data->getLastPlaceBet($this->request->getSessionId(), $this->request->getSkinId());

            if(is_array($lastId) && sizeof($lastId) > 0)
            {
                $lastId = $lastId[0]['lastID'];
            }
            else
            {
                $lastId = $data->insertAWagerToAccountingWagers($this->request->getSkinId(), $this->request->getSessionId(), $coinValue);

                if($lastId === false)
                {
                    throw new AccountingException("Failed to get last placebet id after inserting fake bet", 4);

                }
            }

            $result = $data->updateSettleBetProgressiveSessionForPlayer($jackpotWin, $this->request->getSessionId(), $this->request->getPlayerId());

            if($result === false)
            {
                throw new AccountingException("Failed to update settle bet progressive session", 4);
            }

            $result = null;

            $result = $data->insertSettleBetPlayerProgressive($lastId, $coinValue, $jackpotWin, $jackpotJson);

            if($result === false)
            {
                throw new AccountingException("Failed to insert settle bet progressive ", 4);
            }

            return true;
        }catch (\Exception $ex)
        {
            self::getLogger()->addException($ex);
            $this->processResponse = $ex->getMessage();
            $this->processStatus = false;
            return false;
        }

    }

    public function isSessionExists()
    {
        try
        {
            $ap = new AcccountingPlayers($this->request);
            //check if session exists
            $ap->fetch(array('session_id' => $this->request->getSessionId()));
            if ($ap->getId() == NULL)
            {
                return false;
            }
        }catch (\Exception $ex)
        {
            $newEx = new AccountingException("Failed to check session status", 2,7);
            self::getLogger()->addException(new AccountingException("Failed to check session status", 2,7));
            $this->errorMessage = $newEx->getMessage();
        }
        return true;
    }

    public function isRoundSettled()
    {
        $ar = new entities\AccountingRound($this->request, $this->request->getRoundId());
        if ($ar->getCountWins() > 0)
        {
            return true;
        } else
        {
            return false;
        }
    }

    /**
     * Returns round for given id
     * @return \component\accounting\v1\entities\AccountingRound
     */
    public function getRound()
    {
        return new entities\AccountingRound($this->request, $this->request->getRoundId());
    }

    private function getGMTTimeNow()
    {
        $currentDate = new \DateTime();
        $GMT = new \DateTimeZone("GMT");
        $timeZone = new \DateTimeZone("Etc/GMT+0");
        $newDate = \DateTime::createFromFormat($format = 'Y-m-d H:i:s', $currentDate->format("Y-m-d H:i:s"), $GMT);
        $newDate->setTimezone($timeZone);
        $now = $newDate->format('Y-m-d H:i:s');

        return $now;
    }

    public function setRequest(&$request)
    {
        $this->request = $request;
        return $this;
    }


    public function isAccountingSuccessful()
    {
        return $this->accountingStatus;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }


}
