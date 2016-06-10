<?php
/**
 * Created by PhpStorm.
 * User: anna.rabiej
 * Date: 04/01/2015
 * Time: 11:41
 */

namespace component\soap\v1;

use component\communication\Provider;
use component\soap\ErrorObserver;
use component\soap\SoapRequest;
use model\GMAPIDataRepository;

class BetradarRequest extends SoapRequest{

    private $gameCodes =array("VTO" => 1, "VHC" => 2,"VDR" => 3,"VFL" => 4);

    private $gameCategories =  array("VIRTUAL_SPORTS" => 100);

    private $error = false;

    private $secretKey = null;

    private $licenseeId;

    private $configurationId;

    private $regulationId;

    private $licenseeDbPrefix;

    private $licenseeSecretKey;

    private $gameCode = "VFL";
    private $gameCategory = "VIRTUAL_SPORTS";

    public static $currencyForFree = "NGN";

    private $parameters;
    /**
     * @var \component\communication\Provider
     */
    private $provider;

    private $statusCodes = array(
        1 => array("REQUEST_FORMAT", "Format of	the	associated	request	element	was	not	correct"),
        2 => array("INVALID_TOKEN", "The specified user	token is not valid or was revoked"),
        3 => array("INSUFFICIENT_FUNDS", "User does not have enough funds to complete the requested action"),
        4 => array("USER_NOT_FOUND", "User specified by the userid does not exist in the Wallet system"),
        5 => array("INVALID_CREDENTIALS", "Provided credentials are not valid"),
        6 => array("USER_FROZEN", "User specified with the userid is frozen"),
        7 => array("DUPLICATE_PAYMENT_ID", "Specified paymentid already in use"),
        8 => array("PAYMENT_ID_NOT_FOUND", "Transaction	with the specified	paymentId was not found."),
        9 => array("RISK_VALIDATION", "Payment request was denied due to failed risk validation"),
        10 => array("CANCEL_NOT_POSSIBLE", "Transaction cannot be canceled because it has already been approved"),
        11 => array("USER_EXISTS", "User with specified identifier already exists"),
        12 => array("ERROR", "An unknown error occurred while processing the request element"),
    );



    public function __construct($parameter, $keyHandle)
    {
        ErrorObserver::attach($this);

        $this->entityManager = \component\EntityManager::createEntityManager();

        $this->provider = new Provider();

       //die(var_dump($parameter));
       //die(var_dump($parameter));
        if(!isset($parameter->secretKey) || is_null($parameter->secretKey))
        {
            $this->error = true;
            $this->fault = $this->statusCodes[1][1];
            $this->faultCode = $this->statusCodes[1][0];
            ErrorObserver::notify($this, "validation");
            goto blackHole;
        }

       // $this->secretKey = "471c75ee6643a10934502bdafee198fb";//$parameter->secretKey;
        $this->licenseeId = $parameter->secretKey;

        if(!isset($parameter->requests) || is_null($parameter->requests))
        {
            $this->error = true;
            $this->fault = $this->statusCodes[1][1];
            $this->faultCode = $this->statusCodes[1][0];
            ErrorObserver::notify($this, "validation");
            goto blackHole;
        }

        $this->parameters = $parameter;

        blackHole:{}
    }

    /**
     * @return bool
     */
    public function validateLicensee()
    {
        $lId = $cId = $rId = $dbPrefix = $secretKey = null;

        $licensee = $this->getLicenseeDetails($this->licenseeId);

        list($lId, $cId, $rId, $dbPrefix, $secretKey) = $licensee;

        if(is_null($lId) || is_null($cId) || is_null($rId) || is_null($dbPrefix))
        {
            $this->error = true;
            $this->faultCode = $this->statusCodes[5][0];
            ErrorObserver::notify($this, "validation");
            return false;
        }

        $this->licenseeId = $lId;
        $this->configurationId = $cId;
        $this->regulationId = $rId;
        $this->licenseeDbPrefix = $dbPrefix;
        $this->secretKey = $secretKey;

        return true;
    }

    /**
     * @param $status
     * @param null $gameCode
     * @param null $gameCategory
     * @param null $methodName
     * @param null $lp
     * @param null $pp
     * @param null $operator
     * @param null $otherParameters
     * @return bool
     */
    private function similarRequest($status , $gameCode = null, $gameCategory = null, $methodName = null, $lp = null, $pp = null, $operator = null,
                                    $otherParameters = null)
    {
        if($status === false )
        {
            $this->error = true;
            $this->fault = $this->statusCodes[1][1];
            $this->faultCode = $this->statusCodes[1][0];
            ErrorObserver::notify($this, "validation");
            return false;
        }

        $this->doRequest($this->secretKey, $gameCode, $gameCategory, $methodName, $lp, $pp, $this->provider->getProviderId(),
            $this->provider->getProviderName(), $operator, $otherParameters);
        return true;

    }

    /**
     * @param bool $makeBalanceCall
     * @param int $index
     * @return bool
     */
    public function userInfoRequest($makeBalanceCall = false, $index = 0)
    {
        $status = false;
        $requestParameter = null;
        if(isset($this->parameters->requests->UserInfoRequest))
        {
            $requestParameter = ((is_array($this->parameters->requests->UserInfoRequest)) ? $this->parameters->requests->UserInfoRequest[$index] : $this->parameters->requests->UserInfoRequest);
        }


        if(!isset($requestParameter) || is_null($requestParameter)
            || !isset($requestParameter->CorrelationNumber)
            || is_null($requestParameter->CorrelationNumber)
            || !isset($requestParameter->Token)
            || !isset($requestParameter->GameCode)
            || is_null($requestParameter->GameCode)
        )
        {
            goto End;
        }

        $this->gameCode = $requestParameter->GameCode;
        $launcherData = array();
        if(strpos($requestParameter->Token, ",") !== false)
        {
            $launcherData = explode(",",$requestParameter->Token);
        }

        if(sizeof($launcherData) < 5)
        {
            $this->error = true;
            $this->fault = $this->statusCodes[2][1];
            $this->faultCode = $this->statusCodes[2][0];
            ErrorObserver::notify($this, "token");
            return false;
        }

        if(!$this->validateLicensee())
        {
            goto End;
        }

        $lp = $launcherData[1];
        $pp = $launcherData[0].",".$launcherData[5].",".$launcherData[2].",".$launcherData[3].",".$launcherData[4].",".$requestParameter->GameCode;
        if($makeBalanceCall)
        {
            $methodName = "balance";
        }
        else
        {
            $methodName = "blogin";
        }

        $operator = "0";
        $otherParameters = array("free_spin" => "0", "pp" => $pp);
        $status = true;
        goto End;

        End:
        {
            if($status === false)
            {
                return $this->similarRequest($status);
            }
            return $this->similarRequest($status, $this->gameCodes[$this->gameCode], $this->gameCategory, $methodName, $lp, $requestParameter->Token,
                $operator, $otherParameters);
        }

    }

    /**
     * @param int $index
     * @return bool
     */
    public function queryBalanceRequest($index = 0)
    {
        $status = false;
        $requestParameter = null;
        if(isset($this->parameters->requests->QueryBalanceRequest))
        $requestParameter = ((is_array($this->parameters->requests->QueryBalanceRequest)) ? $this->parameters->requests->QueryBalanceRequest[$index] : $this->parameters->requests->QueryBalanceRequest);

        if(!isset($requestParameter) || is_null($requestParameter)
            ||!isset($requestParameter->CorrelationNumber)
            || is_null($requestParameter->CorrelationNumber)
            || !isset($requestParameter->Token) || is_null($requestParameter->Token)
            || !isset($requestParameter->UserId) || is_null($requestParameter->UserId)
        )
        {
            goto End;
        }

        $launcherData = array();
        if(strpos($requestParameter->Token, ",") !== false)
        {
            $launcherData = explode(",",$requestParameter->Token);
        }

        if(sizeof($launcherData) < 1)
        {
            goto End;
        }

        if(!$this->validateLicensee())
        {
            goto End;
        }

        $methodName = "balance";
        $sessionId = $launcherData[0];
        $lp = $launcherData[1];
        $operator = "0";

        $db = new GMAPIDataRepository($this->licenseeDbPrefix."_accounting");
        GMAPIDataRepository::$dbProviderPrefix = $this->provider->getProviderName()."_";
        $result = $db->fetchAccountingPlayersBySessionId($sessionId);
        if($result === false)
        {
            $this->error = true;
            $this->fault = $this->statusCodes[12][1];
            $this->faultCode = $this->statusCodes[12][0];

            return false;
        }
        $this->gameCode = $result['skin_id'];

        $otherParameters = array("free_spin" => "0", "sesid" => $sessionId, "uid" =>$requestParameter->UserId);
        $status = true;
        goto End;

        End:
        {
            if($status === false)
            {
                return $this->similarRequest($status);
            }

            return $this->similarRequest($status, $this->gameCode, $this->gameCategory, $methodName, $lp, $requestParameter->Token,
                $operator, $otherParameters);
        }
    }


    public function reserveFundRequest($index = 0)
    {
        $status = false;

        $requestParameter = null;

        if(isset($this->parameters->requests->ReserveFundsRequest))
        {
            $requestParameter = ((is_array($this->parameters->requests->ReserveFundsRequest)) ? $this->parameters->requests->ReserveFundsRequest[$index] : $this->parameters->requests->ReserveFundsRequest);
        }

        if( is_null($requestParameter)
            || !isset($requestParameter->CorrelationNumber) || is_null($requestParameter->CorrelationNumber) || !isset($requestParameter->Token)
            || is_null($requestParameter->Token) || !isset($requestParameter->CurrencyCode) || is_null($requestParameter->CurrencyCode)
            || !isset($requestParameter->UserId) || is_null($requestParameter->UserId) || !isset($requestParameter->Stake)
            || is_null($requestParameter->Stake) || !isset($requestParameter->PaymentId) || is_null($requestParameter->PaymentId)
            || !isset($requestParameter->GameCode) || is_null($requestParameter->GameCode)
        )
        {
            goto End;
        }

        $launcherData = array();
        if(strpos($requestParameter->Token, ",") !== false)
        {
            $launcherData = explode(",",$requestParameter->Token);
        }

        if(sizeof($launcherData) < 5)
        {
            goto End;
        }

        if(!$this->validateLicensee())
        {
            goto End;
        }
        $this->gameCode = $requestParameter->GameCode;
        $lp = $launcherData[1];
        $sessionId = $launcherData[0];

        $methodName = "pbet";

        $operator = "0";
        $otherParameters = array("free_spin" => "0");
        //$transactionId = $this->generateTransactionId($requestParameter->Stake->Timestamp);
        $roundId = $requestParameter->PaymentId;
        $transactionId = $roundId.$requestParameter->UserId."BET";
        $otherParameters['ai'] = $transactionId.",".$roundId.",0,0,0,1";
        $otherParameters['wager'] =  $requestParameter->Stake->Amount * 100;
        $otherParameters['sesid'] = $sessionId;
        $otherParameters['uid'] = $requestParameter->UserId;
        $otherParameters['fround_id'] = "0";
        $otherParameters['lines'] = "0";
        $otherParameters['line_bet'] = "0";
        $otherParameters['fround_provider'] = "0";

        $status = true;
        goto End;

        End:
        {
            if($status === false)
            {
                return $this->similarRequest($status);
            }
            return $this->similarRequest($status, $this->gameCodes[$this->gameCode], $this->gameCategory, $methodName, $lp, $requestParameter->Token,
                $operator, $otherParameters);
        }
    }


    public function paymentRequest($index = 0)
    {
        $status = false;

        $requestParameter = null;

        if(isset($this->parameters->requests->PaymentRequest))
        {
            $requestParameter = ((is_array($this->parameters->requests->PaymentRequest)) ? $this->parameters->requests->PaymentRequest[$index] : $this->parameters->requests->PaymentRequest);
        }

        if( is_null($requestParameter)
            || !isset($requestParameter->CorrelationNumber)
            || is_null($requestParameter->CorrelationNumber)
            || !isset($requestParameter->UserId)
            || is_null($requestParameter->UserId)
            || !isset($requestParameter->ApprovePayment)
            || is_null($requestParameter->ApprovePayment)
            || !isset($requestParameter->PaymentId)
            || is_null($requestParameter->PaymentId)
            || !isset($requestParameter->Payment)
            || is_null($requestParameter->Payment)
        )
        {
            goto End;
        }



        if(!$this->validateLicensee())
        {
            goto End;
        }

        $lp = $requestParameter->UserId;

        $roundId = $requestParameter->PaymentId;
        $db = new GMAPIDataRepository($this->licenseeDbPrefix."_accounting");
        GMAPIDataRepository::$dbProviderPrefix = $this->provider->getProviderName()."_";
        $result = $db->fetchSessionIdFromRoundId($roundId);

        if(!is_array($result) || is_array($result) && !isset($result[0]['session_id']))
        {
            $this->error = true;
            $this->fault = $this->statusCodes[8][1]. ": ".$requestParameter->PaymentId;
            $this->faultCode = $this->statusCodes[8][0];
            ErrorObserver::notify($this, "queryresult");
            return false;
        }

        $result = $result[0];

        $sessionId = $result['session_id'];

        $methodName = "sbet";

        $operator = "0";
        $otherParameters = array("free_spin" => "0");
        //$transactionId = $this->generateTransactionId($requestParameter->Payment->Timestamp);
        $transactionId = $roundId.$requestParameter->UserId."WIN";

        $otherParameters['ai'] = $transactionId.",".$roundId.",0,0,0,1";
        $otherParameters['win'] =  $requestParameter->Payment->Amount * 100;
        $otherParameters['sesid'] = $sessionId;
        $otherParameters['uid'] = $requestParameter->UserId;
        $otherParameters['fround_id'] = "0";
        $otherParameters['lines'] = "0";
        $otherParameters['line_bet'] = "0";
        $otherParameters['fround_provider'] = "0";
        $otherParameters['desc'] = "0";
        $pp = $sessionId.",".$requestParameter->UserId.",".$requestParameter->CurrencyCode.",GB,real";

        $status = true;
        goto End;

        End:
        {
            if($status === false)
            {
                return $this->similarRequest($status);
            }
            return $this->similarRequest($status, $this->gameCodes[$this->gameCode], $this->gameCategory, $methodName, $lp, $pp, $operator, $otherParameters);
        }
    }

    public function approveRequest($index = 0)
    {
        $status = false;

        $requestParameter = null;

        if(isset($this->parameters->requests->ApproveRequest))
        {
            $requestParameter = ((is_array($this->parameters->requests->ApproveRequest)) ? $this->parameters->requests->ApproveRequest[$index] : $this->parameters->requests->ApproveRequest);
        }

        if( is_null($requestParameter)
            || !isset($requestParameter->CorrelationNumber)
            || is_null($requestParameter->CorrelationNumber)
            || !isset($requestParameter->PaymentId)
            || is_null($requestParameter->PaymentId)
        )
        {
            goto End;
        }



        if(!$this->validateLicensee())
        {
            goto End;
        }

        $roundId = $requestParameter->PaymentId;
        $db = new GMAPIDataRepository($this->licenseeDbPrefix."_accounting");
        GMAPIDataRepository::$dbProviderPrefix = $this->provider->getProviderName()."_";
        $lastWinData = $db->fetchPlayerDataAfterLatsWin($roundId);
        $sessionData = $db->fetchSessionIdFromRoundId($roundId);

       // die(var_dump($lastWinData));

        if(!is_array($sessionData))
        {
            $this->error = true;
            $this->fault = $this->statusCodes[8][1]. ": ".$requestParameter->PaymentId;
            $this->faultCode = $this->statusCodes[8][0];
            ErrorObserver::notify($this, "queryresult");
            return false;
        }

        $sessionData = $sessionData[0];
        $playerId = $sessionData['player_id'];
        $sessionId = $sessionData['session_id'];
        $skinId = $sessionData['skin_id'];
        $currencyCode = $sessionData['currency_code'];
        BetradarRequest::$currencyForFree = $currencyCode;
        $this->gameCode = $skinId;
        $lp = $playerId;
        $pp = $sessionId.",".$playerId.",".$currencyCode.",GB,real";
        $operator = "0";
        $otherParameters = array("free_spin" => "0", "sesid" => $sessionId, "uid" => $playerId);
        $methodName = "";

        if (is_array($lastWinData) && isset($lastWinData['currency_code']) && isset($lastWinData['player_balance']))
        {
            $methodName = "balance";
            return $this->similarRequest(true, $this->gameCode, $this->gameCategory, $methodName, $lp, $pp, $operator, $otherParameters);
        }

        $methodName = "sbet";

        $transactionId = $this->generateTransactionId($playerId);

        $otherParameters['ai'] = $transactionId.",".$roundId.",0,0,0,1";
        $otherParameters['win'] = 0;
        $otherParameters['fround_id'] = "0";
        $otherParameters['lines'] = "0";
        $otherParameters['line_bet'] = "0";
        $otherParameters['fround_provider'] = "0";
        $otherParameters['desc'] = '0';

        return $this->similarRequest(true, $this->gameCode, $this->gameCategory, $methodName, $lp, $pp, $operator, $otherParameters);

        End:
        {
            return $this->similarRequest($status);
        }
    }


    public function manuelPaymentRequest($index = 0, $doWin = null)
    {
        $status = false;
        if(is_array($this->parameters->requests->ManualPaymentRequest))
        {
            $requestParameter = $this->parameters->requests->ManualPaymentRequest[$index];
        }else
        {
            $requestParameter = $this->parameters->requests->ManualPaymentRequest;
        }


        if(!isset($requestParameter) || is_null($requestParameter)
            || !isset($requestParameter->CorrelationNumber)
            || is_null($requestParameter->CorrelationNumber)
            || !isset($requestParameter->PaymentId)
            || is_null($requestParameter->PaymentId)
            || !isset($requestParameter->Payment)
            || is_null($requestParameter->Payment)
            || !isset($requestParameter->CurrencyCode)
            || is_null($requestParameter->CurrencyCode)
            || !isset($requestParameter->UserId)
            || is_null($requestParameter->UserId)
        )
        {
            goto End;
        }


        if(!$this->validateLicensee())
        {
            goto End;
        }


        $roundId = $requestParameter->PaymentId;
        $db = new GMAPIDataRepository($this->licenseeDbPrefix."_accounting");
        GMAPIDataRepository::$dbProviderPrefix = $this->provider->getProviderName()."_";
        $lastWinData = $db->fetchPlayerDataAfterLatsWin($roundId);
        $sessionData = $db->fetchSessionIdFromRoundId($roundId);
        $cancelRoundId = $roundId."C";
        $cancelData = $db->fetchSessionIdFromRoundId($cancelRoundId);

        if(!is_array($lastWinData) || !isset($lastWinData['currency_code']) || !isset($lastWinData['player_balance']))
        {
            $this->error = true;
            $this->fault = $this->statusCodes[8][1]. ": ".$requestParameter->PaymentId;
            $this->faultCode = $this->statusCodes[8][0];
            ErrorObserver::notify($this, "queryresult");
            return false;
        }

        if(!is_array($sessionData))
        {
            $this->error = true;
            $this->fault = $this->statusCodes[8][1]. ": ".$requestParameter->PaymentId;
            $this->faultCode = $this->statusCodes[8][0];
            ErrorObserver::notify($this, "queryresult");
            return false;
        }

        if(is_array($cancelData) && sizeof($cancelData) > 0 && isset($cancelData[0]['session_id']))
        {
            $this->error = true;
            $this->fault = $this->statusCodes[7][1]. ": ".$requestParameter->PaymentId;
            $this->faultCode = $this->statusCodes[7][0];
            ErrorObserver::notify($this, "queryresult");
            return false;
        }

        $sessionData = $sessionData[0];
        $sessionId = $sessionData['session_id'];
        $lastWinAmount = $lastWinData['win_amount'];
        $amount = $requestParameter->Payment->Amount;
        $betAmount = 1;
        $win = 0;
        self::$currencyForFree = $sessionData['currency_code'];

        if($lastWinAmount > $amount)
        {
            $betAmount = $lastWinAmount - $amount;
            $betAmount = $betAmount * 100;
            $betAmount = $betAmount+1;
        }
        else
        {
            $win = $amount - $lastWinAmount;
            $win = $win * 100;
            $win = $win + 1;
        }

        if(!is_null($doWin))
        {
            $methodName = "sbet";
            $otherParameters['win'] = $win;
        }
        else
        {
            $methodName = "pbet";
            $bet = $betAmount;
            $otherParameters['wager'] = $bet;
        }

        $operator = "0";

        $transactionId = $this->generateTransactionId( $requestParameter->Payment->Timestamp);
        $roundId = $roundId."MP";
        $otherParameters['ai'] = $transactionId.",".$roundId.",0,0,0,1";
        $otherParameters['sesid'] = $sessionId;
        $otherParameters['uid'] = $sessionData['player_id'];
        $otherParameters['fround_id'] = "0";
        $otherParameters['lines'] = "0";
        $otherParameters['line_bet'] = "0";
        $otherParameters['fround_provider'] = "0";
        $otherParameters['desc'] = "0";
        $otherParameters['free_spin'] = '0';
        $pp = $sessionId.",".$sessionData['player_id'].",".$sessionData['currency_code'].",GB,real";
        $lp = $sessionData['player_id'];
        $status = true;
        goto End;

        End:
        {
            if($status === false)
            {
                return $this->similarRequest($status);
            }
            return $this->similarRequest($status, $this->gameCodes[$this->gameCode], $this->gameCategory, $methodName, $lp, $pp, $operator, $otherParameters);
        }

    }

    public function cancelRequest($index = 0, $useAmount = null)
    {
        $status = false;
        if(is_array($this->parameters->requests->CancelRequest))
        {
            $requestParameter = $this->parameters->requests->CancelRequest[$index];
        }else
        {
            $requestParameter = $this->parameters->requests->CancelRequest;
        }


        if(!isset($requestParameter) || is_null($requestParameter)
            || !isset($requestParameter->CorrelationNumber)
            || is_null($requestParameter->CorrelationNumber)
            || !isset($requestParameter->PaymentId)
            || is_null($requestParameter->PaymentId)
            || !isset($requestParameter->CustomValues)
            || is_null($requestParameter->CustomValues)
            || empty($requestParameter->CustomValues)

        )
        {
            goto End;
        }



        if(!$this->validateLicensee())
        {
            goto End;
        }

        $customValues = json_decode($requestParameter->CustomValues, true);

        if(is_array($customValues) && !isset($customValues['amount']) || !is_array($customValues))
        {
            goto End;
        }

        $amount = $customValues['amount'];

        if(!is_numeric($amount))
        {
            goto End;
        }

        $roundId = $requestParameter->PaymentId;
        $db = new GMAPIDataRepository($this->licenseeDbPrefix."_accounting");
        GMAPIDataRepository::$dbProviderPrefix = $this->provider->getProviderName()."_";
        $sessionData = $db->fetchSessionIdFromRoundId($roundId);
        $manualPaymentRoundId = $roundId."MP";
        $manualPaymentData = $db->fetchSessionIdFromRoundId($manualPaymentRoundId);

        if(!is_array($sessionData) || is_array($sessionData) && !isset($sessionData[0]['session_id']))
        {
            $this->error = true;
            $this->fault = $this->statusCodes[8][1]. ": ".$requestParameter->PaymentId;
            $this->faultCode = $this->statusCodes[8][0];
            ErrorObserver::notify($this, "queryresult");
            return false;
        }

        if(is_array($manualPaymentData) && sizeof($manualPaymentData) > 0 && isset($manualPaymentData[0]['session_id']))
        {
            $this->error = true;
            $this->fault = $this->statusCodes[7][1]. ": ".$requestParameter->PaymentId;
            $this->faultCode = $this->statusCodes[7][0];
            ErrorObserver::notify($this, "queryresult");
            return false;
        }

        $sessionData = $sessionData[0];
        $sessionId = $sessionData['session_id'];
        $otherParameters = array("free_spin" => "0");


        if($amount < 0 && $useAmount === false || is_null($useAmount))
        {
            $methodName = "sbet";
            $winAmount = (abs($amount)) * 100;
            $winAmount = $winAmount + 1;
            $win = (is_null($useAmount) ? 0 : $winAmount );
            $otherParameters['win'] = $win;
        }
        elseif($amount > 0 && $useAmount === false)
        {
            $methodName = "sbet";
            $win = 0;
            $otherParameters['win'] = $win;
        }
        else
        {
            $methodName = "pbet";
            $betAmount = $amount * 100;
            $bet = (( $useAmount === true && ($betAmount > 0)) ? $betAmount : 1);
            $otherParameters['wager'] = $bet;
        }

        $operator = "0";

        $transactionId = $this->generateTransactionId(rand(20000,900000));
        $roundId = $roundId."C";
        $otherParameters['ai'] = $transactionId.",".$roundId.",0,0,0,1";
        $otherParameters['sesid'] = $sessionId;
        $otherParameters['uid'] = $sessionData['player_id'];
        $otherParameters['fround_id'] = "0";
        $otherParameters['lines'] = "0";
        $otherParameters['line_bet'] = "0";
        $otherParameters['fround_provider'] = "0";
        $otherParameters['desc'] = "0";
        $pp = $sessionId.",".$sessionData['player_id'].",".$sessionData['currency_code'].",GB,real";
        $lp = $sessionData['player_id'];

        $status = true;
        goto End;

        End:
        {
            if($status === false)
            {
                return $this->similarRequest($status);
            }
            return $this->similarRequest($status, $this->gameCodes[$this->gameCode], $this->gameCategory, $methodName, $lp, $pp, $operator, $otherParameters);
        }


    }

    /**
     * @return string
     */
    public function getFault()
    {
        return $this->fault;
    }

    /**
     * @return bool
     */
    public function isFault()
    {
        return $this->error;
    }

    /**
     * @param $providerSecretKey
     * @param $skinId
     * @param $gameCategory
     * @param $methodName
     * @param $lp
     * @param $pp
     * @param $pId
     * @param $pName
     * @param string $operator
     * @param array $otherRequestParameters
     */
    public function doRequest($providerSecretKey, $skinId, $gameCategory, $methodName, $lp, $pp,$pId, $pName, $operator = "0", $otherRequestParameters = array())
    {
        $gameId = $this->gameCategories[$gameCategory];
        $this->setRequest($pId, $pName, $this->licenseeId, $this->configurationId, $this->regulationId,$gameId,
           $skinId , $this->getHashInfo($this->licenseeId, $providerSecretKey, $gameId, $skinId, $this->configurationId),$methodName, $lp,
            $pp, $operator, $otherRequestParameters);

    }

    /**
     * @return mixed
     */
    public function getFaultCode()
    {
        return $this->faultCode;
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getStatusCodes($code)
    {
        return $this->statusCodes[$code][0];
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getGameCodes($code)
    {
        return $this->gameCodes[$code];
    }


    /**
     * @param $seed
     * @return int
     */
    protected function generateTransactionId($seed)
    {
        $min = 9000000000000000000 + ((int) $seed);
        $max = 9223372036854775807;

        return mt_rand($min, $max);
    }







}