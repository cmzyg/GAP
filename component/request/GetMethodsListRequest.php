<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 14/10/14
 * Time: 09:45
 */

namespace component\request;


class GetMethodsListRequest extends Request{

    protected $callGame = false;

	protected $required = array(
			'lid' => 'licenseeId',
            'cid' => 'configurationId',
            'rid' => 'regulationId',
            'gid' => 'gameId',
            'sid' => 'skinId',
            );

    public function __construct()
    {
        parent::__construct();
    }
} 