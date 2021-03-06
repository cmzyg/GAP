<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/09/14
 * Time: 17:06
 */

namespace component\request;


class SaveScreenRequest extends SaveScreenRelatedRequest{

	protected $userId;
	protected $status;
	protected $description;
	protected $ai;
	protected $gmai;

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
        'status'=> 'status',
        'desc' => 'description',
        'ai' => 'ai',
        'gmai' => 'gmai',
    );

    public function __construct()
    {
        parent::__construct();

        $this->userId = ((isset($this->allRequest['uid'])) ? $this->allRequest['uid'] : null);
        $this->status = ((isset($this->allRequest['status'])) ? $this->allRequest['status'] : null);
        $this->description = ((isset($this->allRequest['desc'])) ? $this->allRequest['desc'] : null);
        $this->ai = ((isset($this->allRequest['ai'])) ? $this->allRequest['ai'] : null);
        $this->gmai = ((isset($this->allRequest['gmai'])) ? $this->allRequest['gmai'] : null);
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

    public function getStatus()
    {
    	return $this->status;
    }

    public function setStatus($status)
    {
    	$this->status = $status;
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

    public function getAi()
    {
    	return $this->ai;
    }

    public function setAi($ai)
    {
    	$this->ai = $ai;
    	return $this;
    }

    public function getGmai()
    {
    	return $this->gmai;
    }

    public function setGmai($gmai)
    {
    	$this->gmai = $gmai;
    	return $this;
    }


} 