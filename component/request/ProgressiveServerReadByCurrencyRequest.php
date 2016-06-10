<?php
/**
 * Created by PhpStorm.
 * User: entymon
 * Date: 20/10/14
 * Time: 12:50
 */

namespace component\request;


class ProgressiveServerReadByCurrencyRequest extends ProgressiveRequest {

    protected $internalProgressiveId;

    protected $coinValue;

    protected $pp;

    protected $playerId;

    protected $currency;

    protected $sessionId;

    protected $callGame = false;

    const SESSION_ID = 0;

    const PLAYER_ID = 1;

    const CURRENCY = 2;

    protected $required = array(
        'lid' => 'licenseeId',
        'cid' => 'configurationId',
        'rid' => 'regulationId',
        'operator' => 'operatorName',
        'pp' => 'pp',
        'progid' => 'internalProgressiveId',
        'cv' => 'coinValue',
    );

    public function __construct()
    {
        parent::__construct();

        $this->internalProgressiveId = ((isset($this->allRequest['progid'])) ? $this->allRequest['progid'] : null);
        $this->coinValue = ((isset($this->allRequest['cv'])) ? $this->allRequest['cv'] : null);
        $this->pp = ((isset($this->allRequest['pp'])) ? $this->allRequest['pp'] : null);

        if(!is_null($this->pp))
        {
            $temp = explode(",", $this->pp);
            if(isset($temp[self::SESSION_ID])) $this->sessionId = $temp[self::SESSION_ID];
            if(isset($temp[self::PLAYER_ID])) $this->playerId = $temp[self::PLAYER_ID];
            if(isset($temp[self::CURRENCY])) $this->currency = $temp[self::CURRENCY];
        }
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param mixed $playerId
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
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
     * @return null
     */
    public function getPp()
    {
        return $this->pp;
    }

    /**
     * @param null $pp
     */
    public function setPp($pp)
    {
        $this->pp = $pp;
    }

    /**
     * @return null
     */
    public function getCoinValue()
    {
        return $this->coinValue;
    }

    /**
     * @param null $coinValue
     */
    public function setCoinValue($coinValue)
    {
        $this->coinValue = $coinValue;
    }

    /**
     * @return null
     */
    public function getInternalProgressiveId()
    {
        return $this->internalProgressiveId;
    }

    /**
     * @param null $internalProgressiveId
     */
    public function setInternalProgressiveId($internalProgressiveId)
    {
        $this->internalProgressiveId = $internalProgressiveId;
    }

} 