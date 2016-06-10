<?php
/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 03/11/14
 * Time: 14:37
 */

namespace component\wallet\seamless\v1;


class SeamlessRequestData {

    private $sessionId;
    private $operator;
    private $secretKey;
    private $command;
    private $player;
    private $credit;
    private $gameId;
    private $roundId;
    private $transactionId;
    private $jackPotContribution;
    private $jackPotWin;
    private $finish;
    private $closeRound;
    private $betCredit;
    private $winCredit;
    private $betTransactionId;
    private $winTransactionId;
    private $freeRoundId;
    private $freeRoundCoinValue;
    private $freeRoundLines;
    private $freeRoundLineBet;
    private $requestData;

    public function __construct($command, $secretKey)
    {
        $this->command = $command;
        $this->secretKey = $secretKey;
        $this->requestData = array();

        $this->setSecretKey($this->secretKey);
        $this->setCommand($this->command);
    }


    /**
     * @param mixed $betCredit
     */
    public function setBetCredit($betCredit)
    {
        $this->betCredit = $betCredit;
        $this->requestData['betcredit'] = $this->betCredit;
    }

    /**
     * @param mixed $betTransactionId
     */
    public function setBetTransactionId($betTransactionId)
    {
        $this->betTransactionId = $betTransactionId;
        $this->requestData['btransactionid'] = $this->betTransactionId;
    }

    /**
     * @param mixed $closeRound
     */
    public function setCloseRound($closeRound)
    {
        $this->closeRound = $closeRound;
        $this->requestData['closeround'] = $this->closeRound;
    }

    /**
     * @param mixed $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
        $this->requestData['command'] = $this->command;
    }

    /**
     * @param mixed $credit
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
        $this->requestData['credit'] = $this->credit;
    }

    /**
     * @param mixed $finish
     */
    public function setFinish($finish)
    {
        $this->finish = $finish;
        $this->requestData['finishedround'] = $this->finish;
    }

    /**
     * @param mixed $freeRound
     */
    public function setFreeRoundId($freeRound)
    {
        $this->freeRoundId = $freeRound;
        $this->requestData['fround_id'] = $this->freeRoundId;
    }

    /**
     * @param mixed $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
        $this->requestData['gameid'] = $this->gameId;
    }

    /**
     * @param mixed $jackPotContribution
     */
    public function setJackPotContribution($jackPotContribution)
    {
        $this->jackPotContribution = $jackPotContribution;
        $this->requestData['jpc'] = $this->jackPotContribution;
    }

    /**
     * @param mixed $jackPotWin
     */
    public function setJackPotWin($jackPotWin)
    {
        $this->jackPotWin = $jackPotWin;
        $this->requestData['jpw'] = $this->jackPotWin;
    }

    /**
     * @param mixed $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
        $this->requestData['operator'] = $this->operator;
    }

    /**
     * @param mixed $player
     */
    public function setPlayer($player)
    {
        $this->player = $player;
        $this->requestData['playerid'] = $this->player;
    }

    /**
     * @param mixed $roundId
     */
    public function setRoundId($roundId)
    {
        $this->roundId = $roundId;
        $this->requestData['roundid'] = $this->roundId;
    }

    /**
     * @param mixed $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
        $this->requestData['secretkey'] = $this->createSecretKey();
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->requestData['sessionid'] = $this->sessionId;
    }

    /**
     * @param mixed $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        $this->requestData['transactionid'] = $this->transactionId;
    }

    /**
     * @param mixed $winCredit
     */
    public function setWinCredit($winCredit)
    {
        $this->winCredit = $winCredit;
        $this->requestData['wincredit'] = $this->winCredit;
    }

    /**
     * @param mixed $winTransactionId
     */
    public function setWinTransactionId($winTransactionId)
    {
        $this->winTransactionId = $winTransactionId;
        $this->requestData['wtransactionid'] = $this->winTransactionId;
    }

    /**
     * @param mixed $freeRoundCoinValue
     */
    public function setFreeRoundCoinValue($freeRoundCoinValue)
    {
        $this->freeRoundCoinValue = $freeRoundCoinValue;
        $this->requestData['fround_coin_value'] = $this->freeRoundCoinValue;
    }

    /**
     * @param mixed $freeRoundLineBet
     */
    public function setFreeRoundLineBet($freeRoundLineBet)
    {
        $this->freeRoundLineBet = $freeRoundLineBet;
        $this->requestData['fround_lines'] = $this->freeRoundLineBet;
    }

    /**
     * @param mixed $freeRoundLines
     */
    public function setFreeRoundLines($freeRoundLines)
    {
        $this->freeRoundLines = $freeRoundLines;
        $this->requestData['fround_line_bet'] = $this->freeRoundLines;
    }

    private function createSecretKey()
    {
        $command = $this->command;

        $date = new \DateTime("now");
        $time = $date->getTimestamp();
        $year = date("Y", $time);
        $month = date("m", $time);
        $hour = (string) date("H", $time);
        $day = date("d", $time);

        $hash = md5($this->secretKey . "." . $year . "." . $month . "." . $day . "." . $hour . "." . $command);

        return $hash;
    }

    /**
     * @return mixed
     */
    public function getBetCredit()
    {
        return $this->betCredit;
    }

    /**
     * @return mixed
     */
    public function getBetTransactionId()
    {
        return $this->betTransactionId;
    }

    /**
     * @return mixed
     */
    public function getCloseRound()
    {
        return $this->closeRound;
    }

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return mixed
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @return mixed
     */
    public function getFinish()
    {
        return $this->finish;
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
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @return mixed
     */
    public function getJackPotContribution()
    {
        return $this->jackPotContribution;
    }

    /**
     * @return mixed
     */
    public function getJackPotWin()
    {
        return $this->jackPotWin;
    }

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @return array
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * @return mixed
     */
    public function getRoundId()
    {
        return $this->roundId;
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->secretKey;
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
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return mixed
     */
    public function getWinCredit()
    {
        return $this->winCredit;
    }

    /**
     * @return mixed
     */
    public function getWinTransactionId()
    {
        return $this->winTransactionId;
    }


} 