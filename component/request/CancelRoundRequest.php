<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/09/14
 * Time: 17:06
 */

namespace component\request;

class CancelRoundRequest extends WalletRequest{

	protected $userId;
	protected $betAmount;
	protected $winAmount;
	protected $freeSpin;
	protected $betTransactionId;
	protected $winTransactionId;
	protected $roundId;
	protected $progressiveBet;
	protected $progressiveId1;
	protected $progressiveId2;
	protected $jackpotWin;
	protected $description;
	protected $freeRoundId;
	protected $lines;
	protected $lineBet;
	protected $freeRoundProvider;
	protected $sessionId;
	protected $coinValue;
	protected $token;

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
                            'round_id'=>'roundId'
                            );

    public function __construct()
    {
        parent::__construct();

        $this->userId =((isset($this->allRequest['uid'])) ? $this->allRequest['uid'] : null);
		$this->betAmount =((isset($this->allRequest['wager'])) ? $this->allRequest['wager'] : null);
		$this->winAmount =((isset($this->allRequest['win'])) ? $this->allRequest['win'] : null);
		$this->freeSpin =((isset($this->allRequest['free_spin'])) ? $this->allRequest['free_spin'] : null);
		$this->betTransactionId =((isset($this->allRequest['bet_tx'])) ? $this->allRequest['bet_tx'] : null);
		$this->winTransactionId =((isset($this->allRequest['win_tx'])) ? $this->allRequest['win_tx'] : null);
		$this->roundId =((isset($this->allRequest['round_id'])) ? $this->allRequest['round_id'] : null);
		$this->progressiveBet =((isset($this->allRequest['prog_bet'])) ? $this->allRequest['prog_bet'] : null);
		$this->progressiveId1 =((isset($this->allRequest['prog_id1'])) ? $this->allRequest['prog_id1'] : null);
		$this->progressiveId2 =((isset($this->allRequest['prog_id2'])) ? $this->allRequest['prog_id2'] : null);
		$this->jackpotWin =((isset($this->allRequest['jpw'])) ? $this->allRequest['jpw'] : null);
		$this->description =((isset($this->allRequest['desc'])) ? $this->allRequest['desc'] : null);
		$this->freeRoundId =((isset($this->allRequest['fround_id'])) ? $this->allRequest['fround_id'] : null);
		$this->lines =((isset($this->allRequest['lines'])) ? $this->allRequest['lines'] : null);
		$this->lineBet =((isset($this->allRequest['line_bet'])) ? $this->allRequest['line_bet'] : null);
		$this->freeRoundProvider =((isset($this->allRequest['fround_provider'])) ? $this->allRequest['fround_provider'] : null);
		$this->sessionId =((isset($this->allRequest['sesid'])) ? $this->allRequest['sesid'] : null);
		$this->coinValue =((isset($this->allRequest['cv'])) ? $this->allRequest['cv'] : null);
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

    /**
     * @return mixed
     */
    public function getCoinValue()
    {
        return $this->coinValue;
    }

    /**
     * @param mixed $coinValue
     */
    public function setCoinValue($coinValue)
    {
        $this->coinValue = $coinValue;
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

	public function setBetAmount($betAmount)
	{
		$this->betAmount = $betAmount;
		return $this;
	}

	public function getWinAmount()
	{
		return $this->winAmount;
	}

	public function setWinAmount($winAmount)
	{
		$this->winAmount = $winAmount;
		return $this;
	}

	public function getFreeSpin()
	{
		return $this->freeSpin;
	}

	public function setFreeSpin($freeSpin)
	{
		$this->freeSpin = $freeSpin;
		return $this;
	}

	public function getBetTransactionId()
	{
		return $this->betTransactionId;
	}

	public function setBetTransactionId($betTransactionId)
	{
		$this->betTransactionId = $betTransactionId;
		return $this;
	}

	public function getWinTransactionId()
	{
		return $this->winTransactionId;
	}

	public function setWinTransactionId($winTransactionId)
	{
		$this->winTransactionId = $winTransactionId;
		return $this;
	}

	public function getRoundId()
	{
		return $this->roundId;
	}

	public function setRoundId($roundId)
	{
		$this->roundId = $roundId;
		return $this;
	}

	public function getProgressiveBet()
	{
		return $this->progressiveBet;
	}

	public function setProgressiveBet($progressiveBet)
	{
		$this->progressiveBet = $progressiveBet;
		return $this;
	}

	public function getProgressiveId1()
	{
		return $this->progressiveId1;
	}

	public function setProgressiveId1($progressiveId1)
	{
		$this->progressiveId1 = $progressiveId1;
		return $this;
	}

	public function getProgressiveId2()
	{
		return $this->progressiveId2;
	}

	public function setProgressiveId2($progressiveId2)
	{
		$this->progressiveId2 = $progressiveId2;
		return $this;
	}

	public function getJackpotWin()
	{
		return $this->jackpotWin;
	}

	public function setJackpotWin($jackpotWin)
	{
		$this->jackpotWin = $jackpotWin;
		return $this;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
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


} 