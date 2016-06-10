<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 04/11/14
 * Time: 09:45
 */

namespace component\wallet\seamless\v1;


class SeamlessResponseParser {

    private $balance;
    private $freeRoundId;
    private $freeRoundRounds;
    private $freeRoundCoinValue;
    private $freeRoundLines;
    private $freeRoundLineBet;
    private $winBalance;
    private $betBalance;

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



} 