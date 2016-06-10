<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/09/14
 * Time: 17:07
 */

namespace component\request;


class PlaceBetRequest extends WalletRequest{

	protected $userId;
	protected $betAmount;
	protected $ai;
	protected $freeRoundId;
	protected $lines;
	protected $lineBet;
	protected $freeRoundProvider;
    protected $progressiveBet;
    protected $progressiveId1;
    protected $progressiveId2;
    protected $token;

    const TRANSACTION_ID = 0;
    const ROUND_ID = 1;
    const PROGRESSIVE_BET = 2;
    const PROGRESSIVE_BOTTOM_LEVEL = 3;
    const PROGRESSIVE_TOP_LEVEL = 4;
    const COIN_VALUE = 5;

	protected $required = array(
                            'lid' => 'licenseeId',
                            'cid' => 'configurationId',
                            'rid' => 'regulationId',
                            'gid' => 'gameId',
                            'sid' => 'skinId',
                            'hinfo' => 'hashInformation',
                            'method_name' => 'methodName',
                            'lp' => 'playerId',
                            'pp' => 'pp',
                            'operator' => 'operatorName',
                            'uid' => 'userId',
                            'sesid' => 'sessionId',
                            'wager' => 'betAmount',
                            'ai' => 'ai',
                            'fround_id' => 'freeRoundId',
                            'lines' => 'lines',
                            'line_bet' => 'lineBet',
                            'fround_provider' => 'freeRoundProvider',
                            );


    public function __construct()
    {
        parent::__construct();

        $this->userId =((isset($this->allRequest['uid'])) ? $this->allRequest['uid'] : null);
        $this->sessionId =((isset($this->allRequest['sesid'])) ? $this->allRequest['sesid'] : null);
        $this->betAmount =((isset($this->allRequest['wager'])) ? $this->allRequest['wager'] : null);
        $this->ai =((isset($this->allRequest['ai'])) ? $this->allRequest['ai'] : null);
        if(isset($this->allRequest['ai']))
        {
            $temp = explode(",", $this->ai);
            if(isset($temp[self::TRANSACTION_ID])) $this->transactionId = $temp[self::TRANSACTION_ID];
            if(isset($temp[self::ROUND_ID])) $this->roundId = $temp[self::ROUND_ID];
            if(isset($temp[self::PROGRESSIVE_BET]) )$this->progressiveBet = $temp[self::PROGRESSIVE_BET];
            if(isset($temp[self::PROGRESSIVE_BOTTOM_LEVEL])) $this->progressiveId1 = $temp[self::PROGRESSIVE_BOTTOM_LEVEL];
            if(isset($temp[self::PROGRESSIVE_TOP_LEVEL])) $this->progressiveId2 = $temp[self::PROGRESSIVE_TOP_LEVEL];
            if(isset($temp[self::COIN_VALUE])) $this->coinValue = $temp[self::COIN_VALUE];
        }
        $this->freeRoundId =((isset($this->allRequest['fround_id'])) ? $this->allRequest['fround_id'] : null);
        $this->lines =((isset($this->allRequest['lines'])) ? $this->allRequest['lines'] : null);
        $this->lineBet =((isset($this->allRequest['line_bet'])) ? $this->allRequest['line_bet'] : null);
        $this->freeRoundProvider =((isset($this->allRequest['fround_provider'])) ? $this->allRequest['fround_provider'] : null);
        $this->token =((isset($this->allRequest['token'])) ? $this->allRequest['token'] : null);
    }

    /**
     * @return null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param null $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

	public function getUserId()
	{
		return $this->userId;
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
		return $this;
	}

	public function getBetAmount()
	{
		return $this->betAmount;
	}

	public function setBetAmount($wager)
	{
		$this->betAmount = $wager;
		return $this;
	}    	

	public function getAi()
	{
		return $this->ai;
	}

	public function setAi($ai)
	{
		$this->ai = $ai;
		return $this;
	}    	

	public function getFreeRoundId()
	{
		return $this->freeRoundId;
	}

	public function setFreeRoundId($freeRoundId)
	{
		$this->freeRoundId = $freeRoundId;
		return $this;
	}    	

	public function getLines()
	{
		return $this->lines;
	}

	public function setLines($lines)
	{
		$this->lines = $lines;
		return $this;
	}    	

	public function getLineBet()
	{
		return $this->lineBet;
	}

	public function setLineBet($lineBet)
	{
		$this->lineBet = $lineBet;
		return $this;
	}    	

	public function getFreeRoundProvider()
	{
		return $this->freeRoundProvider;
	}

	public function setFreeRoundProvider($freeRoundProvider)
	{
		$this->freeRoundProvider = $freeRoundProvider;
		return $this;
	}

    /**
     * @param mixed $coinValue
     */
    public function setCoinValue($coinValue)
    {
        $this->coinValue = $coinValue;
    }

    /**
     * @return mixed
     */
    public function getCoinValue()
    {
        return $this->coinValue;
    }

    /**
     * @param mixed $progressiveBet
     */
    public function setProgressiveBet($progressiveBet)
    {
        $this->progressiveBet = $progressiveBet;
    }

    /**
     * @return mixed
     */
    public function getProgressiveBet()
    {
        return $this->progressiveBet;
    }

    /**
     * @param mixed $progressiveId1
     */
    public function setProgressiveId1($progressiveId1)
    {
        $this->progressiveId1 = $progressiveId1;
    }

    /**
     * @return mixed
     */
    public function getProgressiveId1()
    {
        return $this->progressiveId1;
    }

    /**
     * @param mixed $progressiveId2
     */
    public function setProgressiveId2($progressiveId2)
    {
        $this->progressiveId2 = $progressiveId2;
    }

    /**
     * @return mixed
     */
    public function getProgressiveId2()
    {
        return $this->progressiveId2;
    }

    /**
     * @param mixed $roundId
     */
    public function setRoundId($roundId)
    {
        $this->roundId = $roundId;
    }

    /**
     * @return mixed
     */
    public function getRoundId()
    {
        return $this->roundId;
    }

    /**
     * @param mixed $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }




} 