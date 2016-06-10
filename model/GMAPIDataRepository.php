<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 17/10/14
 * Time: 14:26
 */

namespace model;

use application\BaseComponent;
use exceptions\DatabaseException;
use exceptions\AccountingException;

class GMAPIDataRepository {

    /**
     * @var \mysqli|null|\PDO
     */
    protected $conn;
    public static $dbProviderPrefix = "";

    public function __construct($databaseName)
    {
        $config = new DatabaseConfiguration();

        $db = new Database("PDO", $databaseName, $config->getDbPassword(), $config->getDbUsername(), $config->getHost(), $config->getPort());
        $this->conn = DatabaseFactory::createDatabase($db, true);
        if($this->conn == NULL)
        {
            BaseComponent::getLogger()->addException(new AccountingException('test',2,2));
        }
    }

    /**
     * This is fetching free round..
     * @param type $currency
     * @param type $userId
     * @param type $skinId
     * @param type $operatorId
     * @return type
     */
    public function fetchFreeRound($currency, $userId, $skinId, $operatorId)
    {

        $date = date('Y-m-d H:i:s');

        $query = "

          SELECT
          freeround.id,
          freeround.lines,
          freeround.line_bet,
          freeround_coins.coin_value,
          freeround.limit_per_player,
          freeround_players.amount_spent,
          freeround.start_date,
          freeround.end_date,
          freeround.limit_total,
          freeround.total_spent,
          freeround_state.state,
          freeround_players.player_id

          FROM
             (
              SELECT freeround_players.* FROM freeround_players JOIN freeround ON freeround.id = freeround_players.freeround_id
              WHERE player_id = '" . $userId . "' AND flag = 0 AND freeround.operator_id = '" . $operatorId . "' AND freeround.state_id = 1
             )  AS freeround_players
          INNER JOIN freeround
            ON freeround.id = freeround_players.freeround_id
         INNER JOIN freeround_coins
            ON freeround_coins.freeround_id = freeround.id
          INNER JOIN freeround_state
            ON freeround_state.id = freeround.state_id
          AND freeround_coins.currency = '" . $currency . "'
          AND freeround.game_id = '" . $skinId . "'
          AND  freeround.start_date <= STR_TO_DATE('" . $date . "', '%Y-%m-%d %H:%i:%s')
          ";

        $result = $this->conn->query($query);

        return $result;
    }

    public function fetchSessionIdFromRoundId($roundId)
    {
        $sql = "SELECT * FROM " . GMAPIDataRepository::$dbProviderPrefix . "accounting_players WHERE id IN (SELECT accounting_players_id FROM
        `" . GMAPIDataRepository::$dbProviderPrefix . "accounting_wagers` WHERE round_id = ? ) ORDER BY session_start_date ASC";
        try
        {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $roundId);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }

    public function fetchOperatorCoinValue($operatorId)
    {
        $sql = "SELECT * FROM game_coin_values WHERE operator_id = ?";
        try
        {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $operatorId);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }

    public function fetchAccountingWagers($roundId, $transactionId = null)
    {
        if(is_null($transactionId))
        {
            $sql = "SELECT * FROM " . GMAPIDataRepository::$dbProviderPrefix . "accounting_wagers WHERE round_id = ?";
        }
        else
        {
            $sql = "SELECT * FROM " . GMAPIDataRepository::$dbProviderPrefix . "accounting_wagers WHERE transaction_id = ?";
        }

        try
        {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, ((is_null($transactionId)) ? $roundId : $transactionId));
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }

    public function updatePlaceBetProgressiveSessionForPlayer($jackpotContribution, $sessionId, $playerId)
    {
        $sql = "UPDATE ".GMAPIDataRepository::$dbProviderPrefix."accounting_players SET jackpot_contribution_sum = jackpot_contribution_sum + ?
        WHERE session_id = ? AND player_id  = ? ";

        try
        {
            $jackpotContribution = doubleval($jackpotContribution);
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $jackpotContribution);
            $stmt->bindValue(2, $sessionId);
            $stmt->bindValue(3, $playerId);
            $stmt->execute();
            return true;
        }catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }

    public function updateSettleBetProgressiveSessionForPlayer($jackpotValue, $sessionId, $playerId)
    {
        $sql = "UPDATE ".GMAPIDataRepository::$dbProviderPrefix."accounting_players SET jackpot_win_sum = jackpot_win_sum + ?
        WHERE session_id = ? AND player_id  = ? ";

        try
        {
            $jackpotValue = doubleval($jackpotValue);
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $jackpotValue);
            $stmt->bindValue(2, $sessionId);
            $stmt->bindValue(3, $playerId);
            $stmt->execute();
            return true;
        }catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }

    public function updateRoundStatus($roundId, $status)
    {
        $sql = "UPDATE ".GMAPIDataRepository::$dbProviderPrefix."accounting_wagers SET round_status = ?
        WHERE round_id = ? ";

        try
        {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $status);
            $stmt->bindValue(2, $roundId);
            $stmt->execute();
            return true;
        }catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }

    public function getLastPlaceBet($sessionId, $skinId)
    {
        $sql = "SELECT max(w.id) as lastID FROM ".GMAPIDataRepository::$dbProviderPrefix."accounting_players AS p LEFT JOIN accounting_wagers AS w ON p.id = w.accounting_players_id
                WHERE p.session_id = ? AND p.skin_id = ? LIMIT 1";

        try
        {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $sessionId);
            $stmt->bindValue(2, $skinId);
            $stmt->execute();
            return $stmt->fetchAll();
        }catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }

    public function getCoinValueBaseOnCurrencyAndOperatorId($operatorId, $currency, $skinID)
    {
        $sql = "SELECT coin_values FROM " . GMAPIDataRepository::$dbProviderPrefix . "game_coin_values WHERE operator_id = ? AND currency = ? AND skin_id = ?";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $operatorId);
            $stmt->bindValue(2, $currency);
            $stmt->bindValue(3, $skinID);
            $stmt->execute();
            $result = $stmt->fetchAll();

            // BaseComponent::getLogger()->log('newsql', json_encode($result));

            return $result;

        }catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }


    public function insertAWagerToAccountingWagers($skinId, $sessionId, $coinValue)
    {
        $sql = "SELECT id AS AP FROM ".GMAPIDataRepository::$dbProviderPrefix."accounting_players WHERE session_id = ? AND skin_id = ? ";

        try
        {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $sessionId);
            $stmt->bindValue(2, $skinId);
            $stmt->execute();
            $result =  $stmt->fetchAll();

            if(is_null($result) || (is_array($result) && sizeof($result) < 1) || (is_array($result) && !isset($result[0]['AP'])))
            {
                throw new \Exception("Failed get accounting players id to use for fake bet");
            }

            $accountingPlayersId = $result[0]['AP'];
            $sql = "INSERT INTO ".GMAPIDataRepository::$dbProviderPrefix."accounting_wagers (accounting_players_id, bet_amount, win_amount,
            wager_type, wager_date, player_balance, coin_value, round_id, transaction_id, free_spin) VALUES (?,0,0,'BET',?, 0,?,0,'000',0)";

            $now = $this->getGMTNow();

            $stmt = null;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $accountingPlayersId);
            $stmt->bindValue(2, $now);
            $stmt->bindValue(3, $coinValue);
            $stmt->execute();

            $query = "SELECT LAST_INSERT_ID() as LAST_ID";

            foreach($this->conn->query($query) as $q)
            {
                return $q['LAST_ID'];
            }


            return false;
        }catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }

    public function insertPlaceBetPlayerProgressive($lastId, $coinValue, $jackpotContribution, $jackpotJson)
    {
        $sql = " INSERT INTO ".GMAPIDataRepository::$dbProviderPrefix."accounting_progressive (id, accounting_wager_id, jackpot_contribiution_amount,
        jackpot_win_amount, jackpot_values, jackpot_type, jackpot_date, coin_value) VALUES(NULL,?, ?,'0',?,'JPC', ?, ?)";

        try
        {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $lastId);
            $stmt->bindValue(2, $jackpotContribution);
            $stmt->bindValue(3, stripslashes($jackpotJson));
            $stmt->bindValue(4, $this->getGMTNow());
            $stmt->bindValue(5, $coinValue);
            $stmt->execute();
            return true;
        }catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }

    }


    public function insertSettleBetPlayerProgressive($lastId, $coinValue, $jackpotWin, $jackpotJson)
    {
        $sql = "INSERT INTO ".GMAPIDataRepository::$dbProviderPrefix."accounting_progressive (id, accounting_wager_id, jackpot_contribiution_amount,
        jackpot_win_amount, jackpot_values, jackpot_type, jackpot_date, coin_value) VALUES(NULL,?,'0', ?,?,'JPW',?, ?)";

        try
        {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $lastId);
            $stmt->bindValue(2, $jackpotWin);
            $stmt->bindValue(3, stripslashes($jackpotJson));
            $stmt->bindValue(4, $this->getGMTNow());
            $stmt->bindValue(5, $coinValue);
            $stmt->execute();
            return true;
        }catch (\Exception $ex)
        {
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }


    public function getGMTNow()
    {
        $currentDate = new \DateTime();
        $GMT = new \DateTimeZone("GMT");
        $timeZone = new \DateTimeZone("Etc/GMT+0");
        $newDate = \DateTime::createFromFormat($format = 'Y-m-d H:i:s',$currentDate->format("Y-m-d H:i:s"), $GMT);
        $newDate->setTimezone($timeZone);
        $now = $newDate->format('Y-m-d H:i:s');

        return $now;
    }


    /**
     * Call to irsbo.B2B_games table to fetch gameID and skinID
     * @param $gameId
     * @param $skinId
     * @return array|bool
     */
    public function fetchGameAndSkinId($gameId, $skinId)
    {
        $sql = "SELECT * FROM b2b_games WHERE id = :skinId AND mid = :gameId";
        $params = array(
            ':skinId' => $skinId,
            ':gameId' => $gameId,
        );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchOperatorIdByLicenseeAndOperatorName($licenseeId, $operatorName)
    {
        $sql = "SELECT operator_id, status FROM bo_operators_per_licensee WHERE licensee_id = :licenseeId AND operator = :operatorName LIMIT 1";
        $params = array(
            ':licenseeId' => $licenseeId,
            ':operatorName' => $operatorName,
        );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchCurrencyPerCoinValue($skinId, $operatorId, $currency)
    {
        $sql = "SELECT operator_id , currency, flag FROM game_coin_values WHERE operator_id = :opid AND skin_id = :skinid AND currency = :curr LIMIT 1";
        $params = array(
            ':skinid' => $skinId,
            ':opid' => $operatorId,
            ':curr' => $currency,
        );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchCurrencyPerOperatorId($licenseeId, $operatorId, $currency)
    {
        $sql = "SELECT operator_id , currency, status FROM bo_currency_per_operator WHERE licensee_id = :lid AND operator_id = :opid AND currency = :curr LIMIT 1";
        $params = array(
            ':lid' => $licenseeId,
            ':opid' => $operatorId,
            ':curr' => $currency,
        );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchCurrencyPerPlayerCheckPlayer($playerId)
    {
        $sql = "SELECT customer_id FROM currencies_per_players WHERE customer_id = :customer_id LIMIT 1";
        $params = array(
            ':customer_id' => $playerId,
        );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchCurrencyPerPlayerGetCurrency($operatorId, $userId)
    {
        $sql = "SELECT currency FROM currencies_per_players WHERE customer_id = :customer_id AND operator_id = :operator_id LIMIT 1";
        $params = array(
            ':operator_id' => $operatorId,
            ':customer_id' => $userId,
        );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchSaveScreenData($userId, $skinId)
    {
        $sql = "SELECT * FROM backoffice_savescreen_data WHERE uid = :uid AND skinid = :skinid LIMIT 1 ";
        $params = array(
            ':skinid' => $skinId,
            ':uid' => $userId,
        );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchRows()
    {
        $sql = "SELECT ROW_COUNT()";

        return $this->fetchExecute($sql);
    }

    public function fetchBackofficeLicenseesToGetMethods($licenseeId, $configurationId)
    {
        $sql = "SELECT * FROM backoffice_licensees AS bl LEFT JOIN backoffice_configurations AS bc
						ON bl.id_licensee = bc.id_licensee WHERE bl.id_licensee = :lid AND bc.configuration_id = :cid";
        $params = array(
            ':cid' => $configurationId,
            ':lid' => $licenseeId,
        );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchCurrencyPerPlayerCheckCurrency($payerId, $currency)
    {
        $sql = "SELECT customer_id , currency FROM currencies_per_players WHERE customer_id = :customer_id AND currency = :curr LIMIT 1";
        $params = array(
            ':customer_id' => $payerId,
            ':curr' => $currency,
        );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchBackOfficeOperatorsPerLicensee($operatorName)
    {
        $sql = "SELECT licensee_id FROM bo_operators_per_licensee WHERE operator = :operator LIMIT 1";
        $params = array(
            ':operator' => $operatorName,
        );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchBackOfficeLicenseesAndBackOfficeConfigurations($licenseeId)
    {
        $sql = "SELECT
						bc.configuration_id AS cid,
						bl.regulation_number AS rid,
						bl.secret_key AS secret_key,
						bl.licnesee_dbprefix AS db_prefix,
						bl.transaction_id_length AS transaction_id_length,
						bl.round_id_length AS round_id_length, bl.encode AS encode
						FROM backoffice_licensees AS bl LEFT JOIN backoffice_configurations AS bc ON bl.id_licensee = bc.id_licensee WHERE bl.id_licensee = :lid";
        $params = array(
            ':lid' => $licenseeId,
        );

        return $this->fetchExecute($sql, $params);
    }

    /**
     * @param $sessionId
     * @return bool|mixed|null
     */
    public function fetchAccountingPlayersBySessionId($sessionId)
    {
        $sql = "SELECT * FROM " . GMAPIDataRepository::$dbProviderPrefix . "accounting_players WHERE session_id = :session_id LIMIT 1";
        $params = array(
            ':session_id' => $sessionId,
        );
        return $this->fetchExecute($sql, $params);
    }

    /**
     * Stored Procedure
     *
     * @param $operatorId
     * @param $progressiveId
     * @param $currency
     * @param $coinValue
     * @return bool|mixed
     */
    public function fetchProgressiveLevel($operatorId, $progressiveId, $currency, $coinValue)
    {
        $sql = "Call progressive_read(:operator_id,:progressive_id,:currency,:coin_value)";
        $params = array(
            ':operator_id' => $operatorId,
            ':progressive_id' => $progressiveId,
            ':currency' => $currency,
            ':coin_value' => $coinValue,
        );
        return $this->fetchExecute($sql, $params);
    }

    public function fetchProgressiveWin($operatorId, $progressiveId, $currency, $coinValue, $userId, $type, $percentage, $flag)
    {
        $sql = "Call progressive_win(:operator_id,:progressive_id,:currency,:coin_value,:user_id,:type,:percentage,:flag)";
        $params = array(
            ':operator_id' => $operatorId,
            ':progressive_id' => $progressiveId,
            ':currency' => $currency,
            ':coin_value' => $coinValue,
            ':user_id' => $userId,
            ':type' => $type,
            ':percentage' => $percentage,
            ':flag' => $flag,
        );
        return $this->fetchExecute($sql, $params);
    }

    public function fetchProgressiveLevelAfterIncrementAll($operatorId, $progressiveId, $currency, $betAmount, $flag)
    {
        $sql = "Call progressive_increment(:operator_id,:progressive_id,:currency,:bet_amount,:flag)";
        $params = array(
            ':operator_id' => $operatorId,
            ':progressive_id' => $progressiveId,
            ':currency' => $currency,
            ':bet_amount' => $betAmount,
            ':flag' => $flag,
        );
        return $this->fetchExecute($sql, $params);
    }

    /**
     * INSERTIONS
     */
    public function insertCurrencyPerPlayer($payerId, $operatorId, $currency, $userType = 'U')
    {
        $sql = "INSERT INTO currencies_per_players (id,user_type,operator_id,customer_id,currency) VALUES (null,:user_type,:operator_id,:customer_id,:curr)";
        $params = array(
            ':customer_id' => $payerId,
            ':operator_id' => $operatorId,
            ':curr' => $currency,
            ':user_type' => $userType,
        );

        $this->actionExecute($sql, $params);
    }

    public function insertSaveScreenData($userId, $skinId)
    {
        $sql = "INSERT INTO backoffice_savescreen_data ( uid,  skinid, state, connected, ssstring, gmstring ) VALUES ( :uid, :skinid,0,1,'-','-')";
        $params = array(
            ':skinid' => $skinId,
            ':uid' => $userId,
        );

        return $this->actionExecute($sql, $params);
    }

    /**
     * UPDATES
     */
    public function updateSaveScreenData($userId, $skinId)
    {
        $sql = "UPDATE backoffice_savescreen_data SET connected = 1 WHERE uid = :uid AND skinid = :skinid";
        $params = array(
            ':skinid' => $skinId,
            ':uid' => $userId,
        );

        return $this->actionExecute($sql, $params);
    }

    public function updateSaveScreenDataWithData($state, $connection, $ssstring, $gmai, $userId, $skinId)
    {
        $sql = "UPDATE backoffice_savescreen_data SET state = :state, connected = :connected, ssstring = :ssstring, gmstring = :gmstring WHERE uid = :uid AND skinid = :skinid";
        $params = array(
            ':skinid' => $skinId,
            ':uid' => $userId,
            ':state' => $state,
            ':connected' => $connection,
            ':ssstring' => $ssstring,
            ':gmstring' => $gmai,
        );

        return $this->actionExecute($sql, $params);
    }

    protected function actionExecute($sql, $params = array())
    {
        try
        {
            $stmt = $this->conn->prepare($sql);
            if (!empty($params))
            {
                $stmt->execute($params);
            } else
            {
                $stmt->execute();
            }
        } catch (\Exception $ex)
        {
            $sql = $this->helperGetPreparedQuery($sql, $params);
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }

    protected function fetchExecute($sql, $params = array())
    {
        try
        {
            if (($stmt = $this->conn->prepare($sql)) === false)
            {
                throw new DatabaseException("failed to prepare statement " . $sql);
            }
            if (!empty($params))
            {
                if ($stmt->execute($params) === false)
                {
                    throw new DatabaseException("failed to execute statement " . $sql . " with parameters " . json_encode($params));
                }
            } else
            {
                if ($stmt->execute() === false)
                {
                    throw new DatabaseException("failed to execute statement " . $sql . " without parameters");
                }
            }

            $res = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($res === false)
            {
                $error = $stmt->errorInfo();
                if ($error[0] == "00000")
                {
                    return null;
                }
                throw new DatabaseException($error[2]);
            }

            return $res;
        } catch (\Exception $ex)
        {
            $sql = $this->helperGetPreparedQuery($sql, $params);
            BaseComponent::getLogger()->addMySQLError($ex->getMessage(), $sql);
            return false;
        }
    }

    /**
     * Method to get prepared SQL 
     * @param $query
     * @param $params
     * @return mixed
     */
    public function helperGetPreparedQuery($query, $params)
    {

        # build a regular expression for each parameter
        foreach ($params as $key => $value)
        {
            if (is_string($key))
            {
                if (strpos($key, ':') === false)
                {
                    $keys[] = '/:' . $key . '/';
                } else
                {
                    $keys[] = '/' . $key . '/';
                }
            } else
            {
                $keys[] = '/[?]/';
            }

            if (is_numeric($value))
            {
                $values[] = $value;
            } else
            {
                $values[] = '"' . $value . '"';
            }
        }

        $query = preg_replace($keys, $values, $query, 1, $count);
        return $query;
    }

    //ACCOUNTINGS

    /**
     * 
     * @param type $table
     * @param type $id
     * @return type
     */
    public function fetchAccountings($table, $id)
    {

        $sql = "SELECT * FROM `$table` WHERE id = :id LIMIT 1";
        $params = array(
            ':id' => $id,
        );
        return $this->fetchExecute($sql, $params);
    }

    /**
     * 
     * @param type $table
     * @param array $conditions
     * @return type
     */
    public function fetchAccountingsBy($table, array $conditions)
    {
        $values = array();
        $arrConditions = array();
        foreach ($conditions as $k => $v)
        {
            $values[':c' . $k] = $v;
            $arrConditions[] = "$k=:c$k";
        }

        $strConditions = join(' AND ', $arrConditions);

        $sql = "SELECT * FROM `$table` WHERE $strConditions LIMIT 1";

        BaseComponent::getLogger()->log('SQL', str_replace(array_keys($values),$values,$sql));

        return $this->fetchExecute($sql, $values);
    }

    public function fetchAccountingWagersIdsForRound($roundId)
    {

        $sql = "SELECT * FROM " . GMAPIDataRepository::$dbProviderPrefix . "accounting_wagers WHERE round_id = :roundId";

        //BaseComponent::getLogger()->log('SQL', str_replace(':roundId',$roundId,$sql));

        return $this->fetchExecute($sql, array(':roundId' => $roundId));
    }

    /**
     * 
     * @param type $table
     * @param type $fields
     * @return type
     */
    public function insertAccountings($table, $fields = array())
    {
        $strNames = '`' . join('`, `', array_keys($fields)) . '`';

        $values = array();
        foreach ($fields as $k => $v)
        {
            $values[':' . $k] = $v;
        }
        $strValues = join(',
            ', array_keys($values));

        $sql = "INSERT INTO `$table`
        ($strNames) 
        VALUES (
        " . $strValues . "
         )";
        BaseComponent::getLogger()->log('SQL', str_replace(array_keys($values), $values, $sql));
        return $this->actionExecute($sql, $values);
    }

    /**
     * 
     * @param type $table
     * @param type $fields
     * @return type
     */
    public function updateAccountings($table, $fields = array(), $conditions = array())
    {
        $strNames = '`' . join('`, `', array_keys($fields)) . '`';

        $values = array();
        $arrValues = array();
        foreach ($fields as $k => $v)
        {
            $values[':' . $k] = $v;
            $arrValues[] = "$k=:$k";
        }
        $strValues = join(',
', $arrValues);

        $arrConditions = array();
        foreach ($conditions as $k => $v)
        {
            $values[':c' . $k] = $v;
            $arrConditions[] = "$k=:c$k";
        }

        $strConditions = join(' AND ', $arrConditions);

        $sql = "UPDATE `$table`
        SET
        " . $strValues . "
        WHERE    
        " . $strConditions . "
        ";
        
        BaseComponent::getLogger()->log('SQL', str_replace(array_keys($values), $values, $sql));

        return $this->actionExecute($sql, $values);
    }

    // betradar

    /**
     *
     * @param type $roundid
     * @return array
     */
    public function fetchPlayerDataAfterLatsWin($roundid)
    {
        $sql = "SELECT ap.currency_code, aw.player_balance, aw.win_amount
                FROM ".GMAPIDataRepository::$dbProviderPrefix."accounting_players ap LEFT JOIN ".GMAPIDataRepository::$dbProviderPrefix."accounting_wagers aw ON ap.id = aw.accounting_players_id
                WHERE round_id = '$roundid' AND wager_type = 'WIN' order by aw.id DESC LIMIT 1 ";

        return $this->fetchExecute($sql);
    }


    /**
     * @param type $command
     * @param type $request
     * @param type $identifier
     * @return array
     */
    public function saveRequestToReconciliation($command, $request, $identifier, $type = "INTERNAL")
    {
        $request = serialize($request);
        $query   = "INSERT INTO reconciliation (command,request,status,type,identifier) VALUES (:command,:request,:status,:type,:identifier)";
        $params  = array(
            ':command'    => $command,
            ':request'    => base64_encode($request),
            ':status'     => 0,
            ':type'       => $type,
            ':identifier' => $identifier
        );

        return $this->actionExecute($query, $params);
    }


    /**
     * @param type $command
     * @param type $request
     * @param type $identifier
     * @return array
     */
    public function deleteReconciliation($command, $request, $identifier)
    {
        $request = serialize($request);
        $query   = "DELETE FROM reconciliation WHERE command = :command AND request = :request AND identifier = :identifier";
        $params  = array(
            ':command'    => $command,
            ':request'    => base64_encode($request),
            ':identifier' => $identifier
        );

        return $this->actionExecute($query, $params);
    }


    /**
     * @param type $sessionId
     * @return array
     */
    public function findErrorsForSession($sessionId)
    {
        $query   = "SELECT * FROM reconciliation WHERE identifier = :sessionId";
        $params  = array(
            ':identifier' => $sessionId
        );

        return $this->fetchExecute($query, $params);
    }


     /**
     * @return \mysqli|null|\PDO
     */
    public function getConn()
    {
        return $this->conn;
    }




}
