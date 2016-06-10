<?php
/**
 * Created by SublimeText 3.
 * User: entymon
 * Date: 19/01/15
 * Time: 16:52
 */

namespace component\validation\v1;

Class TournamentValidation 
{
	private $request;
	private $mode;
	private $tournamentId;

	public function __construct($request)
    {
        $this->request          = $request;
    }

    public function checkTournamentMode()
    {
    	$temp = explode(",",$this->request->getPp());
        if(isset($temp[0]) && $temp[0] === "tournament")
        {
        	$this->mode = "tournament";
        	$this->tournamentId = (isset($temp[1])) ? $temp[1] : -1;
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkTournamentId()
    {	
    	if($this->tournamentId === -1)
    	{
    		return false;
    	}

    	$valid = ParticularValidator::regex('numeric',$this->tournamentId);
    	if($valid === 0)
    	{
    		return false;
    	}

    	return true;
    }

    public function getTournamentId()
    {
        return $this->tournamentId;
    }
}