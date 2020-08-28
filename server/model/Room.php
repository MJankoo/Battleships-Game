<?php

require_once 'Game.php';

class Room {
	private $player1_board;
    private $player2_board;
    public $players;
	private $player1;
	private $player2;
	private $player1ready;
	private $player2ready;
	public $game;

	public function __construct()
	{   
		$this->players = 0;
		$this->player1_board = $this->createBoard(10, 10);
		$this->player2_board = $this->createBoard(10, 10);
		$this->player1ready = false;
		$this->player2ready = false;
	}

	private function createBoard($width, $height) {	
		$board = [];

		for($i=0;$i<$height;$i++) {
			for($j=0;$j<$width;$j++) {
		 		$board[$i][$j] = '';
		 	}
		}
		return($board);
	}

	private function checkBoard($ships, $user) {
		$everything_OK = true;

		if(sizeof($ships) == 7 && sizeof($ships[0]) == 4 && sizeof($ships[1]) == 3 && sizeof($ships[2]) == 3 && sizeof($ships[3]) == 2 && sizeof($ships[4]) == 2 && sizeof($ships[5]) == 2 && sizeof($ships[6]) == 2)
		{
			foreach($ships as $ship){
				$i=0;

				foreach($ship as $shipPosition)
				{
					if($everything_OK == false)
					{
						return(false);
						break;
					}

					switch($i)
					{
						case 1: //second mast
							if($shipPosition != $ship[0]+10  && $shipPosition != ($ship[0]-10) && $shipPosition != ($ship[0]+1) && $shipPosition != ($ship[0]-1)) {
								$everything_OK = false;
							}
							else
							{
								if($ship[1] == ($ship[0]+1) || $ship[1] == ($ship[0]-1))
									$shipDirection   = "x";
							
								if($ship[1] == ($ship[0]+10) || $ship[1] == ($ship[0]-10))
									$shipDirection  = "y";
							}
							
							break;
						case 2: //third mast
							if($shipDirection  == "x") {
								if($shipPosition != ($ship[1]+1) && $shipPosition != ($ship[1]-1) && $shipPosition != ($ship[0]+1) && $shipPosition != ($ship[0]-1))
								$everything_OK = false;
							}
		
							if($shipDirection  == "y") {
								if($shipPosition != ($ship[1]+10) && $shipPosition != ($ship[1]-10) && $shipPosition != ($ship[0]+10) && $shipPosition != ($ship[0]-10))
								$everything_OK = false;
							}
		
							break; 
						case 3: //fourth mast
							if($shipDirection  == "x") {
								if($shipPosition != ($ship[2]+1) && $shipPosition != ($ship[2]-1) && $shipPosition != ($ship[0]+1) && $shipPosition != ($ship[0]-1) && $shipPosition != ($ship[1]+1) && $shipPosition != ($ship[1]-1)) 
									$everything_OK = false;
							}
		
							if($shipDirection  == "y") {
								if($shipPosition != ($ship[2]+10) && $shipPosition != ($ship[2]-10) && $shipPosition != ($ship[0]+10) && $shipPosition != ($ship[0]-10) && $shipPosition != ($ship[1]+10) && $shipPosition != ($ship[1]-10)) 
									$everything_OK = false;
							}
							break;
						default:
							$everything_OK = true;

						
					}	
				
					$i++;

				}
			}
		}
		else
		{
			$everything_OK = false;
			return(false);
		}

		if($everything_OK) {
			$board = $this->createBoard(10, 10);

			foreach($ships as $ship) {
				foreach($ship as $shipPosition) {
					$row = floor(($shipPosition/10));
					$column = $shipPosition%10;

					$board[$row][$column] = 1;
				}
			}
			if($user == $this->player1) {
				$this->player1_board = $board;
			}
			if($user == $this->player2) {
				$this->player2_board = $board;
			} 
			return(true);
		}
	}

	public function joinRoom($userId, $user) {
		if(!isset($this->player1 )) {
			$this->player1 = $user;
			$this->players += 1;
			$connected = true;

			if(isset($this->player2))	
			{
				$this->player2->id->send(json_encode(array("type"=>"server","msg"=>"players", "players"=>$this->players)));
			}
		} 
		else {
			if(!isset($this->player2) && $this->player1 != $user) {
                $this->players += 1;
				$this->player2 = $user;
				$this->player1->id->send(json_encode(array("type"=>"server","msg"=>"players", "players"=>$this->players)));
				$connected = true;
			}
			else {
				return(false);
			}
		}

		// if($this->players == 2)
		// {
		// 	$this->startGame();
		// 	$this->player1->id->send(json_encode(array("type"=>"server","msg"=>"gameStarted" )));
		// 	$this->player2->id->send(json_encode(array("type"=>"server","msg"=>"gameStarted")));
		// }

		if($connected == true) {
			return(true);
		}
		
	}

	public function leaveRoom($user) {
		$this->players -= 1;
		if($this->player1 == $user)	{
			$this->player1 = null;
			$this->player1ready = false;
			if(isset($this->player2)) {
				$this->player2->id->send(json_encode(array("type"=>'server',"msg"=>"opponentDisconnected")));
			}
		}
		if($this->player2 == $user)	{
			$this->player2 = null;
			$this->player2ready = false;
			if(isset($this->player1)) {
				$this->player1->id->send(json_encode(array("type"=>'server',"msg"=>"opponentDisconnected")));
			}
		}

		unset($this->game);

	}

	private function startGame() {
		$this->game = new Game($this->player1, $this->player2, $this->player1_board, $this->player2_board);
		$this->player1->id->send(json_encode(array("type"=>'server',"msg"=>"gameStarted", "turn"=>true)));
		$this->player2->id->send(json_encode(array("type"=>'server',"msg"=>"gameStarted", "turn"=>false)));
	}

	public function setReady($user, $ships) {
		if($this->checkBoard($ships, $user)) {
			if($user == $this->player1) {
				$this->player1ready = true;
			}
			if($user == $this->player2) {
				$this->player2ready = true;
			}
		}
		else {
			var_dump("Something went wrong, try again later!");
		}

		if($this->player1ready == true && $this->player2ready == true) {
			$this->startGame();
		}
	}

	

	public function unsetReady($user)
	{
		if($user == $this->player1)
		{
			$this->player1ready = false;
		}
		if($user == $this->player2)
		{
			$this->player2ready = false;
		}
	}
}

?>