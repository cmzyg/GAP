<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/11/14
 * Time: 14:37
 */

namespace component\wallet\purse\v1;


class PurseRequestData {

    private $sessionId;
    private $gameId;
    private $playerId;
    private $currency;
    private $state;
    private $operator;
    private $action;
    private $actions;
    private $requestArray;

    public function __construct()
    {
        $this->requestArray = array();
    }

    public function initRequest($token, $gameType = false)
    {
        $action = array(
            "command" => "init",
            "parameters" => array(
                "token" => $token
            ),
        );

        if ($gameType !== false && is_string($gameType))
        {
            $action['parameters']['game_type'] = $gameType;
        }

        $this->setAction($action);
    }

    public function balanceRequest()
    {
        $action = array(
            "command" => "balance",
        );

        $this->setAction($action);
    }

    public function betRequest($transactionId, $roundId, $amount, $jackPotContribution, $freeRound = false, $multiState = false)
    {
        $action = array(
            "command" => "bet",
            "parameters" => array(
                "transactionid" => $transactionId,
                "roundid" => $roundId,
                "amount" => intval($amount),
                "jpc" => $jackPotContribution*100
            )
        );


        if ($freeRound !== false && is_array($freeRound))
        {
            $action['parameters']['froundid'] = $freeRound['id'];
            $action['parameters']['fround_coin_value'] = $freeRound['coin_value'];
            $action['parameters']['fround_lines'] = $freeRound['lines'];
            $action['parameters']['fround_line_bet'] = $freeRound['line_bet'];
        }

        if($multiState)
        {
            $this->action = $action;
        }
        else
        {
            $this->setAction($action);
        }

    }

    public function winRequest($transactionId, $roundId, $amount, $jackPotContribution, $closeRound = false, $freeRound = false, $multiState = false)
    {
        $action = array(
            "command" => "win",
            "parameters" => array(
                "transactionid" => $transactionId,
                "roundid" => $roundId,
                "amount" => intval($amount),
                "jpw" => $jackPotContribution*100,
                "closeround" => $closeRound
            )
        );

        if ($freeRound !== false)
        {
            $action['parameters']['froundid'] = $freeRound;
        }

        if($multiState)
        {
            $this->action = $action;
        }
        else
        {
            $this->setAction($action);
        }
    }

    public function cancelRequest($transactionId, $roundId, $amount, $jackPotContribution, $freeRound = false, $multiState = false)
    {
        $action = array(
            "command" => "cancel",
            "parameters" => array(
                "transactionid" => $transactionId,
                "roundid" => $roundId,
                "amount" => intval($amount),
                "jpc" => $jackPotContribution
            )
        );

        if ($freeRound !== false && is_array($freeRound))
        {
            $action['parameters']['froundid'] = $freeRound['id'];
            $action['parameters']['fround_coin_value'] = $freeRound['coin_value'];
            $action['parameters']['fround_lines'] = $freeRound['lines'];
            $action['parameters']['fround_line_bet'] = $freeRound['line_bet'];
        }

        if($multiState)
        {
            $this->action = $action;
        }
        else
        {
            $this->setAction($action);
        }
    }

    public function cancelUseBetParameters()
    {
        if (isset($this->requestArray['action']['command']))
        {
            $this->requestArray['action']['command'] = 'cancel';
            return true;
        }

        return false;
    }

    public function endRequest($status = null, $multiState = false)
    {
        $action = array(
            "command" => "end",
            "parameters" => array(
                "sessionstatus" => ((is_null($status)) ? "INACTIVE" : $status),
            )
        );

        if($multiState)
        {
            $this->action = $action;
        }
        else
        {
            $this->setAction($action);
        }
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
        $this->requestArray['action'] = $action;
        $this->setState("single");
    }

    /**
     * @param mixed $actions
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
        $this->requestArray['actions'] = $actions;
        $this->setState("multi");
    }

    /**
     * @param mixed $jackPotContribution
     */
    public function setJackPotContribution($jackPotContribution)
    {
        $this->jackPotContribution = $jackPotContribution;
        $this->requestArray['jpc'] = $jackPotContribution;
    }

    /**
     * @param mixed $jackPotWin
     */
    public function setJackPotWin($jackPotWin)
    {
        $this->jackPotWin = $jackPotWin;
        $this->requestArray['jpw'] = $jackPotWin;
    }

    /**
     * @param mixed $credit
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
        $this->requestArray['credit'] = $credit;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        $this->requestArray['currency'] = $currency;
    }

    /**
     * @param mixed $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
        $this->requestArray['gameid'] = $gameId;
    }

    /**
     * @param mixed $roundId
     */
    public function setRoundId($roundId)
    {
        $this->roundId = $roundId;
        $this->requestArray['roundid'] = $roundId;
    }

    /**
     * @param mixed $finish
     */
    public function setFinish($finish)
    {
        $this->finish = $finish;
        $this->requestArray['finishedround'] = $finish;
    }

    /**
     * @param mixed $closeRound
     */
    public function setCloseRound($closeRound)
    {
        $this->closeRound = $closeRound;
        $this->requestArray['closeround'] = $closeRound;
    }

    /**
     * @param mixed $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
        $this->requestArray['operator'] = $operator;
    }

    /**
     * @param mixed $playerId
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
        $this->requestArray['playerid'] = $playerId;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->requestArray['sessionid'] = $sessionId;
    }

    /**
     * @param mixed $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        $this->requestArray['transactionid'] = $transactionId;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
        $this->requestArray['state'] = $state;
    }

    /**
     * @return array
     */
    public function getRequestArray()
    {
        return $this->requestArray;
    }
} 