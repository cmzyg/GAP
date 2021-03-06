<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/09/14
 * Time: 17:06
 */

namespace component\request;


class ProgressiveWinByCurrencyRequest extends ProgressiveRequest{

    protected $userId;
    protected $sessionId;
	protected $coinValue;
	protected $internalProgressiveId;
	protected $percentage;

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
                            'progid' => 'internalProgressiveId',
                            'cv' => 'coinValue',
                            'percentage' => 'percentage',
                            );

    public function __construct()
    {
        parent::__construct();

        $this->userId = ((isset($this->allRequest['uid'])) ? $this->allRequest['uid'] : null);
        $this->sessionId = ((isset($this->allRequest['sesid'])) ? $this->allRequest['sesid'] : null);
        $this->coinValue = ((isset($this->allRequest['cv'])) ? $this->allRequest['cv'] : null);
        $this->internalProgressiveId = ((isset($this->allRequest['progid'])) ? $this->allRequest['progid'] : null);
        $this->percentage = ((isset($this->allRequest['percentage'])) ? $this->allRequest['percentage'] : null);
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
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param null $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function getInternalProgressiveId()
    {
    	return $this->internalProgressiveId;
    }

    public function setInternalProgressiveId($internalProgressiveId)
    {
    	$this->internalProgressiveId = $internalProgressiveId;
    	return $this;
    }

    public function getPercentage()
    {
    	return $this->percentage;
    }

    public function setPercentage($percentage)
    {
    	$this->percentage = $percentage;
    	return $this;
    }
} 