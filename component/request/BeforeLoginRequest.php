<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 24/09/14
 * Time: 17:06
 */

namespace component\request;


class BeforeLoginRequest extends WalletRequest
{

	protected $freeSpin;

	protected $version;

    protected $required = array(
                            'lid' => 'licenseeId', // intiger
                            'cid' => 'configurationId', //
                            'rid' => 'regulationId',
                            'gid' => 'gameId',
                            'sid' => 'skinId',
                            'hinfo' => 'hashInformation',
                            'method_name' => 'methodName',
                            'lp' => 'playerId',
                            'pp' => 'pp',
                            'operator' => 'operatorName',
                            'free_spin' => 'freeSpin',
                            );

    public function __construct()
    {
        parent::__construct();

        $this->freeSpin = ((isset($this->allRequest['free_spin'])) ? $this->allRequest['free_spin'] : null);
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

    public function getVersion()
    {
    	return $this->version;
    }

    public function setVersion($version)
    {
    	$this->version = $version;
    	return $this;
    }

} 