<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 30/10/14
 * Time: 10:32
 */

namespace component\wallet;


class WalletResponse {

    private $userId;
    private $userBalance;
    private $cashBalance;
    private $progressiveWinAmount;
    private $sessionId;
    private $freeRoundProvider;
    private $freeRoundId;
    private $token;
    private $betWinSupport;
    private $coinValueList;
    private $defaultCoinValue;
    private $currencyCode;
    private $currencyDecimal;
    private $currencyThousand;
    private $currencyDecimalDigits;
    private $currencyPrefix;
    private $currencySuffix;
    private $promotion = 0;
    private $freeBalance = 0;
    private $version = "4.0";
    private $response = array();
    private $fun = false;
    private $tournament = false;


    protected function commonThreeResponse()
    {
        $this->response['status'] = "success";
        $this->response['user_balance'] = $this->userBalance;
        $this->response['cash_balance'] = $this->cashBalance;
        $this->response['free_balance'] = $this->freeBalance;
        $this->response['read_balance'] = 1;
        $this->response['promotion'] = $this->promotion;
        $this->response['token'] = $this->token;

    }

    /**
     * @param $userBalance
     * @param $cashBalance
     * @param $token
     * @param int $freeBalance
     * @param int $promotion
     * @param int $promotionWin
     * @return array
     */
    public function settleBetResponse($userBalance, $cashBalance, $token, $freeBalance = 0, $promotion = 0, $promotionWin = 0)
    {
        $this->userBalance = $userBalance;
        $this->cashBalance = $cashBalance;
        $this->freeBalance = $freeBalance;
        $this->token = $token;
        $this->promotion = $promotion;

        $this->commonThreeResponse();
        if($this->fun)
        {
            $this->response['read_balance'] = 0;
        }
        else
        {
            $this->response['promotion_win'] = $promotionWin;
        }

        return $this->response;
    }

    /**
     * @param $progressiveWinAmount
     * @return array
     */
    public function progressiveWinByResponse($progressiveWinAmount)
    {
        $this->progressiveWinAmount = $progressiveWinAmount;

        $this->response['status'] = "success";
        $this->response['progressive_win'] = $this->progressiveWinAmount;

        return $this->response;
    }

    /**
     * @param $userBalance
     * @param $cashBalance
     * @param $freeRoundId
     * @param $token
     * @param int $freeBalance
     * @param int $promotion
     * @return array
     */
    public function placeBetResponse($userBalance, $cashBalance, $freeRoundId, $token, $freeBalance = 0, $promotion = 0)
    {
        $this->userBalance = $userBalance;
        $this->cashBalance = $cashBalance;
        $this->freeBalance = $freeBalance;
        $this->token = $token;
        $this->promotion = $promotion;
        $this->freeRoundId = $freeRoundId;

        $this->commonThreeResponse();
        if($this->fun)
        {
            $this->response['read_balance'] = 0;
        }
        $this->response['fround_id'] = $this->freeRoundId;

        return $this->response;
    }

    /**
     * @param $userId
     * @param $userBalance
     * @param $cashBalance
     * @param $sessionId
     * @param $freeRoundProvider
     * @param $freeRoundId
     * @param $token
     * @param $betWinSupport
     * @param $coinValueList
     * @param $defaultCoinValue
     * @param $currencyCode
     * @param $currencyDecimal
     * @param $currencyThousand
     * @param $currencyDecimalDigits
     * @param $currencyPrefix
     * @param $currencySuffix
     * @param int $promotion
     * @param int $freeBalance
     * @return array
     */
    public function beforeLoginResponse($userId,$userBalance, $cashBalance, $sessionId, $freeRoundProvider, $freeRoundId, $token, $betWinSupport, $coinValueList, $defaultCoinValue, $currencyCode, $currencyDecimal, $currencyThousand, $currencyDecimalDigits, $currencyPrefix, $currencySuffix, $promotion = 0, $freeBalance = 0)
    {
        $this->userBalance = $userBalance;
        $this->cashBalance = $cashBalance;
        $this->freeBalance = $freeBalance;
        $this->token = $token;
        $this->promotion = $promotion;
        $this->freeRoundId = $freeRoundId;
        $this->userId = $userId;
        $this->sessionId = $sessionId;
        $this->freeRoundProvider = $freeRoundProvider;
        $this->betWinSupport = $betWinSupport;
        $this->coinValueList = $coinValueList;
        $this->defaultCoinValue = $defaultCoinValue;
        $this->currencyCode = $currencyCode;
        $this->currencyDecimal = $currencyDecimal;
        $this->currencyDecimalDigits = $currencyDecimalDigits;
        $this->currencyThousand = $currencyThousand;
        $this->currencyPrefix = $currencyPrefix;
        $this->currencySuffix = $currencySuffix;

        $this->commonThreeResponse();
        if($this->fun)
        {
            $this->response['read_balance'] = 0;
        }

        $this->response['uid'] = $userId;
        $this->response['sesid'] = $sessionId;
        $this->response['cv_list'] = $coinValueList;
        $this->response['cv_default'] = $defaultCoinValue;
        $this->response['ccy_code'] = $currencyCode;
        $this->response['ccy_decimal'] = $currencyDecimal;
        $this->response['ccy_thousand'] = $currencyThousand;
        $this->response['ccy_decimal_digits'] = $currencyDecimalDigits;
        $this->response['ccy_prefix'] = $currencyPrefix;
        $this->response['ccy_suffix'] = $currencySuffix;
        $this->response['betwin'] = $betWinSupport;
        $this->response['fround_provider'] = $freeRoundProvider;
        $this->response['fround_id'] = $freeRoundId;
        $this->response['version'] = $this->version;

        return $this->response;

    }

    public function curlErrorResponse($curlCode, $thirdPartyResponse)
    {
        $this->response['seamless_error'] = $thirdPartyResponse;
        $this->response['curl_code'] = $curlCode;

        return $this->response;
    }

    public function httpErrorResponse($httpCode, $thirdPartyResponse)
    {
        $this->response['seamless_error'] = $httpCode;
        $this->response['Description'] = $thirdPartyResponse;

        return $this->response;
    }

    public function errorResponse($httpCode, $thirdPartyResponse)
    {
        $this->response['seamless_error'] = $httpCode;
        $this->response['Incorrect Wallet Answer'] = $thirdPartyResponse;

        return $this->response;
    }

    /**
     * @param mixed $betWinSupport
     */
    public function setBetWinSupport($betWinSupport)
    {
        $this->betWinSupport = $betWinSupport;
    }

    /**
     * @return mixed
     */
    public function getBetWinSupport()
    {
        return $this->betWinSupport;
    }

    /**
     * @param mixed $cashBalance
     */
    public function setCashBalance($cashBalance)
    {
        $this->cashBalance = $cashBalance;
    }

    /**
     * @return mixed
     */
    public function getCashBalance()
    {
        return $this->cashBalance;
    }

    /**
     * @param mixed $coinValueList
     */
    public function setCoinValueList($coinValueList)
    {
        $this->coinValueList = $coinValueList;
    }

    /**
     * @return mixed
     */
    public function getCoinValueList()
    {
        return $this->coinValueList;
    }

    /**
     * @param mixed $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return mixed
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param mixed $currencyDecimal
     */
    public function setCurrencyDecimal($currencyDecimal)
    {
        $this->currencyDecimal = $currencyDecimal;
    }

    /**
     * @return mixed
     */
    public function getCurrencyDecimal()
    {
        return $this->currencyDecimal;
    }

    /**
     * @param mixed $currencyDecimalDigits
     */
    public function setCurrencyDecimalDigits($currencyDecimalDigits)
    {
        $this->currencyDecimalDigits = $currencyDecimalDigits;
    }

    /**
     * @return mixed
     */
    public function getCurrencyDecimalDigits()
    {
        return $this->currencyDecimalDigits;
    }

    /**
     * @param mixed $currencyPrefix
     */
    public function setCurrencyPrefix($currencyPrefix)
    {
        $this->currencyPrefix = $currencyPrefix;
    }

    /**
     * @return mixed
     */
    public function getCurrencyPrefix()
    {
        return $this->currencyPrefix;
    }

    /**
     * @param mixed $currencySuffix
     */
    public function setCurrencySuffix($currencySuffix)
    {
        $this->currencySuffix = $currencySuffix;
    }

    /**
     * @return mixed
     */
    public function getCurrencySuffix()
    {
        return $this->currencySuffix;
    }

    /**
     * @param mixed $currencyThousand
     */
    public function setCurrencyThousand($currencyThousand)
    {
        $this->currencyThousand = $currencyThousand;
    }

    /**
     * @return mixed
     */
    public function getCurrencyThousand()
    {
        return $this->currencyThousand;
    }

    /**
     * @param mixed $defaultCoinValue
     */
    public function setDefaultCoinValue($defaultCoinValue)
    {
        $this->defaultCoinValue = $defaultCoinValue;
    }

    /**
     * @return mixed
     */
    public function getDefaultCoinValue()
    {
        return $this->defaultCoinValue;
    }

    /**
     * @param int $freeBalance
     */
    public function setFreeBalance($freeBalance)
    {
        $this->freeBalance = $freeBalance;
    }

    /**
     * @return int
     */
    public function getFreeBalance()
    {
        return $this->freeBalance;
    }

    /**
     * @param mixed $freeRoundId
     */
    public function setFreeRoundId($freeRoundId)
    {
        $this->freeRoundId = $freeRoundId;
    }

    /**
     * @return mixed
     */
    public function getFreeRoundId()
    {
        return $this->freeRoundId;
    }

    /**
     * @param mixed $freeRoundProvider
     */
    public function setFreeRoundProvider($freeRoundProvider)
    {
        $this->freeRoundProvider = $freeRoundProvider;
    }

    /**
     * @return mixed
     */
    public function getFreeRoundProvider()
    {
        return $this->freeRoundProvider;
    }

    /**
     * @param int $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return int
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $userBalance
     */
    public function setUserBalance($userBalance)
    {
        $this->userBalance = $userBalance;
    }

    /**
     * @return mixed
     */
    public function getUserBalance()
    {
        return $this->userBalance;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param null $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param boolean $fun
     */
    public function setFun($fun)
    {
        $this->fun = $fun;
    }

    /**
     * @param boolean $tournament
     */
    public function setTournament($tournament)
    {
        $this->tournament = $tournament;
    }









} 