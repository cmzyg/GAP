<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/11/14
 * Time: 11:28
 */

namespace component\wallet\fun\v1;


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
use component\progressive\Progressive;
use component\wallet\WalletInterface;
use component\wallet\WalletResponse;

class Fun extends \component\wallet\fun\Fun implements WalletInterface{
    /**
     * Before Login Process
     * @return mixed
     */
    public function beforeLogin()
    {
        $userBalance = 100000;
        $userId = "0";
        $cashBalance = 100000;
        $sessionId = "fun";
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
        $walletResponse->setFun(true);
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
        $walletResponse = new WalletResponse();
        $walletResponse->setFun(true);
        $this->processResponse = $walletResponse->placeBetResponse(100000, 100000, 0, '',0);
        $this->processStatus = true;
    }

    /**
     * Settle Bet Process
     * @return mixed
     */
    public function settleBet()
    {
        $walletResponse = new WalletResponse();
        $walletResponse->setFun(true);
        $this->processResponse = $walletResponse->settleBetResponse(100000, 100000, '',0);
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