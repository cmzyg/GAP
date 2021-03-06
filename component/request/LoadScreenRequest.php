<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/09/14
 * Time: 17:06
 */

namespace component\request;


class LoadScreenRequest extends SaveScreenRelatedRequest{

	protected $userId;

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
                        );

    public function __construct()
    {
        parent::__construct();

        $this->userId = ((isset($this->allRequest['uid'])) ? $this->allRequest['uid'] : null);
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

} 