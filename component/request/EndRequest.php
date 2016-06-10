<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/09/14
 * Time: 17:06
 */

namespace component\request;


class EndRequest extends WalletRequest{

    protected $userId;
    
	protected $sessionId;

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
		);

    public function __construct()
    {
        parent::__construct();

        $this->userId       = ((isset($this->allRequest['uid'])) ? $this->allRequest['uid'] : null);
        $this->sessionId    = ((isset($this->allRequest['sesid'])) ? $this->allRequest['sesid'] : null);
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

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

} 