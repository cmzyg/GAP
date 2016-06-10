<?php

/**
 * Created by PhpStorm.
 * User: samuel
 * Date: 17/10/14
 * Time: 14:26
 */

namespace model;

use application\BaseComponent;
use component\logger\v1\Logger;
use exceptions\DatabaseException;
use model\GMAPIDataRepository;

class TournamentDataRepository extends GMAPIDataRepository {

    public function __construct($databaseName)
    {
    	parent::__construct($databaseName);
    }

    public function fetchTournamentIdFromTournament($operatorName,$skinId,$tournamentId)
    {
    	$sql = "SELECT tour.tournament_id FROM tournament tour 
                        LEFT JOIN tournament_operators toperator ON tour.tournament_group_id = toperator.tournament_group_id 
                        LEFT JOIN tournament_games games ON tour.tournament_group_id = games.tournament_group_id
                        WHERE tour.tournament_id = :tournament_id 
                        AND games.skin_id = :game_skin
                        AND toperator.operator_name = :operator_name";
        $params = array(
                    ':tournament_id'    => $tournamentId,
                    ':operator_name'    => $operatorName,
                    ':game_skin'        => $skinId,
            );

        return $this->fetchExecute($sql, $params);
    }

    public function fetchPlayerIdFromActiveTournamentWallet($tournamentId,$playerId)
    {
    	$sql = "SELECT player_id FROM tournament_wallet WHERE player_id = :player_id AND tournament_id = :tournament_id AND active = 1";
        $params = array(
					':player_id' 		=> $playerId,
					':tournament_id' 	=> $tournamentId,
			);

        return $this->fetchExecute($sql, $params);
    }

    public function fetchFeeDataFromTournamentGroup($tournamentId)
	{
		$sql = "SELECT tgroup.fee_price AS fee_price , tgroup.fee_points AS fee_points FROM tournament_group AS tgroup LEFT JOIN tournament AS tour ON tour.tournament_group_id = tgroup.id WHERE tour.tournament_id = :tournament_id";
		$params = array(
					':tournament_id'	=> $tournamentId,
				);

		return $this->fetchExecute($sql, $params);
	}

	public function fetchTournamentWalletByPlayerAndTournamentId($tournamentId,$playerId)
    {
    	$sql = "SELECT * FROM tournament_wallet WHERE player_id = :player_id AND tournament_id = :tournament_id AND active = 1";
        $params = array(
					':player_id' 		=> $playerId,
					':tournament_id' 	=> $tournamentId,
			);

        return $this->fetchExecute($sql, $params);
    }

	public function insertNewTournamentWallet($tournamentId, $operatorName, $playerId, $playerNick, $feePoints, $skinId)
    {
        $sql = "INSERT INTO `tournament_wallet` ( tournament_id, operator_name, player_id, player_nick, points, skin_id) 
		                                 VALUES (:tournament_id,:operator_name,:player_id,:player_nick,:points,:skin_id)";
		$params = array(
				':tournament_id'	=> $tournamentId,
				':operator_name'	=> $operatorName,
				':player_id'		=> $playerId,
				':player_nick'		=> $playerNick,
				':points'			=> $feePoints,
				':skin_id'			=> $skinId,
			);
		$this->actionExecute($sql,$params);
    }

    public function updateTournamentWalletPointsForPlaceBet($tournamentId, $playerId, $points)
    {
    	$sql = "UPDATE `tournament_wallet` 
    				SET points = points - :points, 
    					total_bets = total_bets + :points  
    			WHERE tournament_id = :tournament_id AND player_id = :player_id";
		$params = array(
				':tournament_id'	=> $tournamentId,
				':player_id'	=> $playerId,
				':points'	=> $points,
			);
		$this->actionExecute($sql,$params);
    }

    public function updateTournamentWalletPointsForSettleBet($tournamentId, $playerId, $points)
    {
    	$sql = "UPDATE `tournament_wallet` 
    				SET total_wins = total_bets + :points  
    			WHERE tournament_id = :tournament_id AND player_id = :player_id";
		$params = array(
				':tournament_id'	=> $tournamentId,
				':player_id'	=> $playerId,
				':points'	=> $points,
			);
		$this->actionExecute($sql,$params);
    }

	public function updateTournamentTotalPlayers($tournamentId)
	{
		$sql = "UPDATE tournament SET total_players = total_players + 1 WHERE tournament_id = :tournament_id";
		$params = array(
				':tournament_id'	=> $tournamentId,
			);
		$this->actionExecute($sql,$params);
	}

	public function updateTournamentAccounting($tournamentId,$opName,$playerId,$feeAmount,$skinId)
	{
		$registerDate = date('Y-m-d H-i-s');
		$sql = "INSERT INTO `tournament_accounting` (tournament_id,operator_name,player_id,skin_id,type,amount,`date`) 
		                                            VALUES (:tournament_id,:operator_name,:player_id,:skin_id,:type,:amount,:date)";
		$params = array(
				':tournament_id'	=> $tournamentId,
				':operator_name'	=> $opName,
				':player_id'		=> $playerId,
				':skin_id'			=> $skinId,
				':type'				=> 'fee',
				':amount'			=> $feeAmount,
				':date'				=> $registerDate,
			);
		$this->actionExecute($sql,$params);
	}
}
