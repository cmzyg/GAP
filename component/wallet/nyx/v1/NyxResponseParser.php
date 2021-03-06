<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 28/10/14
 * Time: 10:16
 */

namespace component\wallet\nyx\v1;


class NyxResponseParser {

    private $accountId;
    private $country;
    private $currency;
    private $sex;
    private $city;
    private $firstName;
    private $lastName;
    private $balance;
    private $realBalance;
    private $bonusBalance;
    private $walletTransactionId;
    private $alreadyProcessed;
    private $bonusMoneyBet;
    private $realMoneyBet;
    private $baseCurrencyRate;
    private $baseCurrency;
    private $transactionId;
    private $returnCode;
    private $request;
    private $error = null;
    private $sessionId;
    private $nyxError = array(1 => "A technical error occurred when processing the request",
        3   => "The game is not configured for the operator.",
        12  => "The request contains an unexected parameter value",
        108 => "An error occured during cancelwager request. Probable causes: Wager was already cancelled, cancelwager was attempted for a result call or round
                    is already closed.",
        1000 => "The Session Id is invalid",
        1003 => "Unable to authenticate user identity",
        1006 => "The player does not have sufficient funds for the bet",
        1008 => "A required parameter is missing. The message text shall state which parameter that is missing",
        1019 => "The player is not allowed to perform the bet due to gaming limits in the wallet platform.",
        1035 => "The account associated with a given session ID is blocked and no bets can be performed.");


    public function __construct($response)
    {
        $this->parser($response);
       //echo '<pre>'.print_r($response, true).'</pre>';
    }

    public function parser($response)
    {
        $this->returnCode = intval($response['attributes']['rc']);
        $this->request = $response['attributes']['request'];

        if($this->returnCode > 0)
        {
           $this->error = ((isset($this->nyxError[$this->returnCode])) ? $this->nyxError[$this->returnCode] : "Unknown error code ".$this->returnCode." returned");
        }
        else
        {
            $this->sessionId = $response['SESSIONID'];

            if($this->request == 'getaccount')
            {
                $this->accountId = $response['ACCOUNTID'];
                $this->currency = $response['CURRENCY'];
                $this->country = $response['COUNTRY'];
                $this->firstName = $response['DETAILS']['FIRSTNAME'];
                $this->lastName = $response['DETAILS']['LASTNAME'];
                $this->city = $response['DETAILS']['CITY'];
                $this->sex = $response['DETAILS']['SEX'];
            }

            if($this->request == 'getbalance')
            {
                $this->balance      = $response['BALANCE'];
                $this->realBalance  = $response['DETAILS']['REALBALANCE'];
                $this->bonusBalance = $response['DETAILS']['BONUSBALANCE'];
            }

            if($this->request == 'wager')
            {
                $this->balance = $response['BALANCE'];
                $this->alreadyProcessed = $response['ALREADYPROCESSED'];
                $this->transactionId = $response['WALLETTRANSACTIONID'];
                $this->realBalance = $response['DETAILS']['REALBALANCE'];
                $this->bonusBalance = $response['DETAILS']['BONUSBALANCE'];
                $this->baseCurrency = $response['DETAILS']['BASECURRENCY'];
                $this->baseCurrencyRate = $response['DETAILS']['BASECURRENCYRATE'];
                $this->realMoneyBet = $response['DETAILS']['REALMONEYBET'];
                $this->bonusMoneyBet = $response['DETAILS']['BONUSMONEYBET'];
            }

            if($this->request == 'result')
            {
                $this->balance = $response['BALANCE'];
                $this->alreadyProcessed = $response['ALREADYPROCESSED'];
                $this->transactionId = $response['WALLETTRANSACTIONID'];
                $this->realBalance = $response['DETAILS']['REALBALANCE'];
                $this->bonusBalance = $response['DETAILS']['BONUSBALANCE'];
                $this->baseCurrency = $response['DETAILS']['BASECURRENCY'];
                $this->baseCurrencyRate = $response['DETAILS']['BASECURRENCYRATE'];
            }

            if($this->request == 'cancelwager')
            {
                $this->transactionId = $response['WALLETTRANSACTIONID'];
                $this->realBalance = $response['DETAILS']['REALBALANCE'];
                $this->bonusBalance = $response['DETAILS']['BONUSBALANCE'];
            }
        }


    }


    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @return mixed
     */
    public function getAlreadyProcessed()
    {
        return $this->alreadyProcessed;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return mixed
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * @return mixed
     */
    public function getBaseCurrencyRate()
    {
        return $this->baseCurrencyRate;
    }

    /**
     * @return mixed
     */
    public function getBonusBalance()
    {
        return $this->bonusBalance;
    }

    /**
     * @return mixed
     */
    public function getBonusMoneyBet()
    {
        return $this->bonusMoneyBet;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return mixed
     */
    public function getRealBalance()
    {
        return $this->realBalance;
    }

    /**
     * @return mixed
     */
    public function getRealMoneyBet()
    {
        return $this->realMoneyBet;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return mixed
     */
    public function getWalletTransactionId()
    {
        return $this->walletTransactionId;
    }

    /**
     * @return mixed
     */
    public function getReturnCode()
    {
        return $this->returnCode;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
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
    }



} 