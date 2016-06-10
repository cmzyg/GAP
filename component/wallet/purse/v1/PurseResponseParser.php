<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 04/11/14
 * Time: 09:45
 */

namespace component\wallet\purse\v1;


class PurseResponseParser {

    private $balance;
    private $freeRoundId;
    private $freeRoundRounds;
    private $freeRoundCoinValue;
    private $freeRoundLines;
    private $freeRoundLineBet;
    private $winBalance;
    private $betBalance;
    private $playerId;
    private $sessionId;
    private $currency;
    private $code;
    private $status;
    private $message;
    private $action;
    private $display;


    public function __construct($response)
    {
        $this->parser($response);
    }


    public function parser($response)
    {
        $ifIssetReturnValue = function($key, $array)
        {
            if(isset($array[$key]))
            {
                return $array[$key];
            }

            return null;
        };

        if(is_array($response))
        {
            $this->balance = $ifIssetReturnValue('balance', $response);
            $this->freeRoundId = ((is_null($ifIssetReturnValue('fround_id', $response))) ? 0 : $ifIssetReturnValue('fround_id', $response));
            $this->freeRoundRounds = $ifIssetReturnValue('fround_rounds', $response);
            $this->freeRoundCoinValue = $ifIssetReturnValue('fround_coin_value', $response);
            $this->freeRoundLines = $ifIssetReturnValue('fround_lines', $response);
            $this->freeRoundLineBet = $ifIssetReturnValue('fround_line_bet', $response);
            $this->winBalance = $ifIssetReturnValue('win_balance', $response);
            $this->betBalance = $ifIssetReturnValue('bet_balance', $response);
            $this->playerId = $ifIssetReturnValue('playerid', $response);
            $this->sessionId = $ifIssetReturnValue('sessionid', $response);
            $this->currency = $ifIssetReturnValue('currency', $response);
            $this->code = $ifIssetReturnValue('code', $response);
            $this->status = $ifIssetReturnValue('status', $response);
            $this->message = $ifIssetReturnValue('message', $response);
            $this->action = $ifIssetReturnValue('action', $response);
            $this->display = $ifIssetReturnValue('display', $response);
        }
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return mixed
     */
    public function getBetBalance()
    {
        return $this->betBalance;
    }

    /**
     * @return mixed
     */
    public function getFreeRoundCoinValue()
    {
        return $this->freeRoundCoinValue;
    }

    /**
     * @return mixed
     */
    public function getFreeRoundId()
    {
        return $this->freeRoundId;
    }

    /**
     * @return mixed
     */
    public function getFreeRoundLineBet()
    {
        return $this->freeRoundLineBet;
    }

    /**
     * @return mixed
     */
    public function getFreeRoundLines()
    {
        return $this->freeRoundLines;
    }

    /**
     * @return mixed
     */
    public function getFreeRoundRounds()
    {
        return $this->freeRoundRounds;
    }

    /**
     * @return mixed
     */
    public function getWinBalance()
    {
        return $this->winBalance;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function isRequestSuccessful()
    {
        if(is_null($this->code) && (is_null($this->status) || $this->status == 'success'))
        {
            return true;
        }

        return false;
    }

} 