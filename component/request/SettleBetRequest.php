<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/09/14
 * Time: 17:06
 */

namespace component\request;


class SettleBetRequest extends WalletRequest{

    protected $userId;
	protected $winAmount;
	protected $freeSpin;
	protected $ai;
	protected $description;
	protected $freeRoundId;
	protected $lines;
	protected $lineBet;
	protected $freeRoundProvider;
    protected $gameStatus;
    protected $jackpotWin;
    protected $winDescription;
    protected $ticket;

    const TRANSACTION_ID = 0;
    const ROUND_ID = 1;
    const GAME_STATUS = 2;
    const JACKPOT_WIN = 3;
    const WIN_DESCRIPTION = 4;
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
        'sesid'=> 'sessionId',
        'win' => 'winAmount',
        'free_spin' => 'freeSpin',
        'ai' => 'ai',
        'desc' => 'description',
        'fround_id' => 'freeRoundId',
        'lines' => 'lines',
        'line_bet' => 'lineBet',
        'fround_provider' => 'freeRoundProvider',
    );

    public function __construct()
    {
        parent::__construct();

        $this->userId =((isset($this->allRequest['uid'])) ? $this->allRequest['uid'] : null);
        $this->ai =((isset($this->allRequest['ai'])) ? $this->allRequest['ai'] : null);
        if(isset($this->allRequest['ai']))
        {
            $temp = explode(",", $this->ai);
            if(isset($temp[self::TRANSACTION_ID])) $this->transactionId = $temp[self::TRANSACTION_ID];
            if(isset($temp[self::ROUND_ID])) $this->roundId = $temp[self::ROUND_ID];
            if(isset($temp[self::GAME_STATUS]) )$this->gameStatus = $temp[self::GAME_STATUS];
            if(isset($temp[self::JACKPOT_WIN])) $this->jackpotWin = $temp[self::JACKPOT_WIN];
            if(isset($temp[self::WIN_DESCRIPTION])) $this->winDescription = $temp[self::WIN_DESCRIPTION];
            if(isset($temp[self::COIN_VALUE])) $this->coinValue = $temp[self::COIN_VALUE];
            
        }
        
        $this->winAmount =((isset($this->allRequest['win'])) ? $this->allRequest['win'] : null);
        $this->sessionId =((isset($this->allRequest['sesid'])) ? $this->allRequest['sesid'] : null);
        $this->userId =((isset($this->allRequest['uid'])) ? $this->allRequest['uid'] : null);
        $this->freeSpin =((isset($this->allRequest['free_spin'])) ? $this->allRequest['free_spin'] : null);
        $this->description =((isset($this->allRequest['desc'])) ? $this->allRequest['desc'] : null);
        $this->freeRoundId =((isset($this->allRequest['fround_id'])) ? $this->allRequest['fround_id'] : null);
        $this->lines =((isset($this->allRequest['lines'])) ? $this->allRequest['lines'] : null);
        $this->lineBet =((isset($this->allRequest['line_bet'])) ? $this->allRequest['line_bet'] : null);
        $this->freeRoundProvider =((isset($this->allRequest['fround_provider'])) ? $this->allRequest['fround_provider'] : null);
    }

    /**
     * @return mixed
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param mixed $ticket
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
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
    public function getAi()
    {
        return $this->ai;
    }

    /**
     * @param mixed $ai
     */
    public function setAi($ai)
    {
        $this->ai = $ai;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getFreeRoundId()
    {
        return $this->freeRoundId;
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
    public function getFreeRoundProvider()
    {
        return $this->freeRoundProvider;
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
    public function getFreeSpin()
    {
        return $this->freeSpin;
    }

    /**
     * @param mixed $freeSpin
     */
    public function setFreeSpin($freeSpin)
    {
        $this->freeSpin = $freeSpin;
    }

    /**
     * @return mixed
     */
    public function getLineBet()
    {
        return $this->lineBet;
    }

    /**
     * @param mixed $lineBet
     */
    public function setLineBet($lineBet)
    {
        $this->lineBet = $lineBet;
    }

    /**
     * @return mixed
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param mixed $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
    }

    /**
     * @return mixed
     */
    public function getWinAmount()
    {
        return $this->winAmount;
    }

    /**
     * @param mixed $win
     */
    public function setWinAmount($win)
    {
        $this->winAmount = $win;
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
     * @param mixed $gameStatus
     */
    public function setGameStatus($gameStatus)
    {
        $this->gameStatus = $gameStatus;
    }

    /**
     * @return mixed
     */
    public function getGameStatus()
    {
        return $this->gameStatus;
    }

    /**
     * @param mixed $jackpotWin
     */
    public function setJackpotWin($jackpotWin)
    {
        $this->jackpotWin = $jackpotWin;
    }

    /**
     * @return mixed
     */
    public function getJackpotWin()
    {
        return $this->jackpotWin;
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

    /**
     * @param mixed $winDescription
     */
    public function setWinDescription($winDescription)
    {
        $this->winDescription = $winDescription;
    }

    /**
     * @return mixed
     */
    public function getWinDescription()
    {
        return $this->winDescription;
    }


} 