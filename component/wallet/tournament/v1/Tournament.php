<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/11/14
 * Time: 11:28
 */

namespace component\wallet\tournament\v1;

use component\communication\Communication;
use component\request\BeforeLoginRequest;
use component\request\EndRequest;
use component\request\LoadScreenRequest;
use component\request\PlaceAndSettleBetRequest;
use component\request\PlaceBetRequest;
use component\request\ProgressiveServerReadByCurrencyRequest;
use component\request\ProgressiveWinByCurrencyRequest;
use component\request\Request;
use component\request\SaveScreenRequest;
use component\request\SettleBetRequest;
use component\wallet\WalletInterface;
use component\wallet\WalletResponse;
use component\progressive\Progressive;
use model\TournamentDataRepository;

class Tournament extends \component\wallet\tournament\Tournament implements WalletInterface{

    private $registerDate; 

    /**
     * Before Login Process
     * @return mixed
     */
    public function beforeLogin()
    {
        $database = new TournamentDataRepository('tournaments');

        $opName = $this->request->getOperatorName();
        $skinId = $this->request->getSkinId();
        $playerId = $this->request->getPlayerId();
        $playerNick = $this->request->getUsername();
        $tournamentId = $this->request->getTournamentId();


        // check if tournament exist for tournament ID
        $result = $database->fetchTournamentIdFromTournament($opName,$skinId,$tournamentId);
        if($result === false || $result === null)
        {
            // temporary have to be change 
            $this->getLogger()->log("tournament",array(
                    'message' => 'Tournament not exist',
                    'operator' => $opName,
                    'tournamentId' => $tournamentId,
                    'skinId' => $skinId,
                ));
            $this->processStatus = false;
            throw new \Exception('Tournament not exist');
        }

        // check if wallet exist for player ID
        $result = $database->fetchPlayerIdFromActiveTournamentWallet($tournamentId,$playerId);
        if(isset($result['player_id']) && $result['player_id'] === $playerId && ($result !== false || $result !== null))
        {
            // temporary have to be change 
            $this->getLogger()->log("tournament",array(
                    'message' => 'Tournament wallet exist',
                    'playerId' => $playerId,
                    'tournamentId' => $tournamentId,
                ));
            $this->processStatus = false;
            throw new \Exception('Wallet exist');
        }

        // get value of fees
        $result = $database->fetchFeeDataFromTournamentGroup($tournamentId);
        $feeAmount = $result['fee_price'];
        $feePoints = $result['fee_points'];

        /**
         * TEMPORARY SOLUTION !!!
         */
        $playerNick = $playerId."_nick";

        // create new tournament wallet
        $database->insertNewTournamentWallet($tournamentId,$opName,$playerId,$playerNick,$feePoints,$skinId);

        // update tournament
        $database->updateTournamentTotalPlayers($tournamentId);

        // add tournament accounting
        $database->updateTournamentAccounting($tournamentId,$opName,$playerId,$feeAmount,$skinId);

        // set response
        $userBalance = intval($feePoints);
        $userId = $playerId;
        $cashBalance = intval($feePoints);
        $sessionId = $tournamentId;
        $token = "";
        $freeRoundId = 0;
        $freeRoundProvider = 0;
        $betWinSupport = 0;
        $coinValue = '1,2,5,10,20,50,100';
        $coinValueDefault = '1';
        $currencyCode =  "EUR";
        $currencyDecimal = "";
        $currencyThousand = "";
        $currencyDecimalDigits = "";
        $currencyPrefix = "";
        $currencySuffix = "";
        $freeBalance = 0;
        $promotion = 0;

        $walletResponse = new WalletResponse();
        $walletResponse->setFun(false);
        $this->processResponse = $walletResponse->beforeLoginResponse($userId,$userBalance, $cashBalance, $sessionId, $freeRoundProvider, $freeRoundId, $token, $betWinSupport,
            $coinValue, $coinValueDefault, $currencyCode, $currencyDecimal, $currencyThousand, $currencyDecimalDigits, $currencyPrefix, $currencySuffix, $promotion, $freeBalance);
        $this->processStatus = true;
    }

    /**
     * Place Bet Process
     * @return mixed
     */
    public function placeBet()
    {
        $database = new TournamentDataRepository('tournaments');

        $playerId = $this->request->getPlayerId();
        $betAmount = $this->request->getBetAmount();
        $tournamentId = $this->request->getTournamentId();

        // update tournament wallet
        $database->updateTournamentWalletPointsForPlaceBet($tournamentId,$playerId,$betAmount);

        // get balance 
        $result = $database->fetchTournamentWalletByPlayerAndTournamentId($tournamentId,$playerId);
        $userBalance = $result['points'];
        $cashBalance = $result['points'];

        $walletResponse = new WalletResponse();
        $walletResponse->setFun(false);
        $this->processResponse = $walletResponse->placeBetResponse(intval($userBalance), intval($cashBalance), 0, '',0);
        $this->processStatus = true;
    }

    /**
     * Settle Bet Process
     * @return mixed
     */
    public function settleBet()
    {
        $database = new TournamentDataRepository('tournaments');

        $playerId = $this->request->getPlayerId();
        $tournamentId = $this->request->getTournamentId();
        $winAmount = $this->request->getWinAmount();

        // update tournament wallet
        $database->updateTournamentWalletPointsForSettleBet($tournamentId,$playerId,$winAmount);

        // get balance 
        $result = $database->fetchTournamentWalletByPlayerAndTournamentId($tournamentId,$playerId);
        $userBalance = $result['points'];
        $cashBalance = $result['points'];

        $walletResponse = new WalletResponse();
        $walletResponse->setFun(false);
        $this->processResponse = $walletResponse->settleBetResponse(intval($userBalance), intval($cashBalance), '',0);
        $this->processStatus = true;
    }

    /**
     * Place and Settle Bet Process
     * @return mixed
     */
    public function placeAndSettleBet()
    {
        $this->processResponse = array("error" => $this->NotSupported());
    }

    /**
     * End Process
     * @return mixed
     */
    public function end()
    {
        $this->processResponse = array("result" => "OK");
        $this->processStatus = true;
    }

    public function loadScreen()
    {
        $this->processResponse = array("state" => 0, "connected" => 0, "ssstring" => "-", "gmstring" => "-");
        $this->processStatus = true;
    }

    public function progressiveServerReadByCurrency()
    {
        $this->processResponse = 501;
        $this->processStatus = true;
    }

    public function progressiveWinByCurrency()
    {
        $progressive = new Progressive();
        $pr = $progressive->loadComponent();
        $pr->progressiveWinUpdateByCurrency(0);
    }

    /**
     * Execute Process
     * @param Request $request
     * @param Communication $communication
     * @return mixed|void
     */
    public function executeProcess(Request $request, Communication $communication)
    {
        $this->communicationComponent = $communication;
        $this->request = $request;

        if($request instanceof BeforeLoginRequest)
        {
            $this->beforeLogin();
        }
        elseif($request instanceof PlaceBetRequest)
        {
            $this->placeBet();
        }
        elseif($request instanceof SettleBetRequest)
        {
            $this->settleBet();
        }
        elseif($request instanceof PlaceAndSettleBetRequest)
        {
            $this->processResponse = array("error" => $this->NotSupported());
        }
        elseif($request instanceof LoadScreenRequest)
        {
            $this->loadScreen();
        }
        elseif($request instanceof ProgressiveServerReadByCurrencyRequest)
        {
            $this->progressiveServerReadByCurrency();
        }
        elseif($request instanceof ProgressiveWinByCurrencyRequest)
        {
            $this->progressive->setRequest($this->request);
            
            $this->processResponse = $this->progressive->progressiveWinUpdateByCurrency(0);
            $this->processStatus = true;
        }
        elseif($request instanceof EndRequest || $request instanceof SaveScreenRequest || $request instanceof ProgressiveWinByCurrencyRequest)
        {
            $this->end();
        }
        else
        {
            $this->processResponse = array("error" => $this->NotSupported());
        }
    }


} 