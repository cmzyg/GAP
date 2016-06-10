<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 15/10/14
 * Time: 15:08
 */

namespace component\request;


class GetLicenseeDetailsRequest extends Request{

    protected $loginC;
    
    protected $passC;

    protected $callGame = false;

    protected $required = array(
        'loginc' => 'loginC',
        'passc' => 'passC',
    );

    const LICENSEE_ID = 0;
    const PLAYER_ID = 1;
    const OPERATOR_NAME = 2;

    public function __construct()
    {
        parent::__construct();

        $this->loginC   = ((isset($this->allRequest['loginc'])) ? $this->allRequest['loginc'] : null);
        if(isset($this->allRequest['loginc']))
        {
            $temp = explode(",", $this->loginC);
            $this->licenseeId = ((isset($temp[self::LICENSEE_ID])) ? $temp[self::LICENSEE_ID] : null);
            $this->playerId = ((isset($temp[self::PLAYER_ID])) ? $temp[self::PLAYER_ID] : null);
            $this->operatorName = ((isset($temp[self::OPERATOR_NAME])) ? $temp[self::OPERATOR_NAME] : null);
        }
        $this->passC    = ((isset($this->allRequest['passc'])) ? $this->allRequest['passc'] : null);
    }

    /**
     * @return mixed
     */
    public function getLoginC()
    {
        return $this->loginC;
    }

    /**
     * @param mixed $loginC
     */
    public function setLoginC($loginC)
    {
        $this->loginC = $loginC;
    }

    /**
     * @return mixed
     */
    public function getPassC()
    {
        return $this->passC;
    }

    /**
     * @param mixed $passC
     */
    public function setPassC($passC)
    {
        $this->passC = $passC;
    }

} 