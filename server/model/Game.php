<?php

class Game {

    private $player1_board;
    private $player2_board;
    private $currentBoard;
    private $player1;
    private $player2;
    private $turn;
    private $winned;

    public function __construct($player1, $player2, $player1_board, $player2_board) {

        $this->turn = 0;
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->player1_board = $player1_board;
        $this->player2_board = $player2_board;
        $this->winned = false;
    }

    public function shoot($player, $position) {
        if($this->winned == true)
        {
            var_dump("winned");
            return(false);
        }
        $board;
        $row = floor($position/10);
        $column = $position%10;
        if($player == $this->whoseTurn() )
        {
            if($player == $this->player1)
            {
                $board = $this->player2_board;
            }

            if($player == $this->player2)
            {
                $board = $this->player1_board;
            }
        }
        else
        {
            $player->id->send(json_encode(array("type"=>"server","msg"=>"error", "alert"=>"It's not your turn!")));
            return(false);
        }

                
        if($board[$row][$column] == 1)
        {
            if($player == $this->player1)
            {
                $this->player2_board[$row][$column] = 2;
                $this->player1->id->send(json_encode(array("type"=>"game","msg"=>"hit", "position"=>$position, "who"=>"enemy")));
                $this->player2->id->send(json_encode(array("type"=>"game","msg"=>"hit", "position"=>$position, "who"=>"client")));
            }
            if($player == $this->player2)
            {
                $this->player1_board[$row][$column] = 2;
                $this->player2->id->send(json_encode(array("type"=>"game","msg"=>"hit", "position"=>$position, "who"=>"enemy")));
                $this->player1->id->send(json_encode(array("type"=>"game","msg"=>"hit", "position"=>$position, "who"=>"client")));
            }
            $this->turn++;
            $this->changeTurn();
        }
        else
        {
            if($board[$row][$column] == 2)
            {
                $player->id->send(json_encode(array("type"=>"game","msg"=>"duplication")));
            }
            else
            {
                if($player == $this->player1)
                {
                    $this->player2_board[$row][$column] = 2;
                    $this->player1->id->send(json_encode(array("type"=>"game","msg"=>"missed", "position"=>$position, "who"=>"enemy")));
                    $this->player2->id->send(json_encode(array("type"=>"game","msg"=>"missed", "position"=>$position, "who"=>"client")));
                }
                if($player == $this->player2)
                {
                    $this->player1_board[$row][$column] = 2;
                    $this->player2->id->send(json_encode(array("type"=>"game","msg"=>"missed", "position"=>$position, "who"=>"enemy")));
                    $this->player1->id->send(json_encode(array("type"=>"game","msg"=>"missed", "position"=>$position, "who"=>"client")));
                }
                $this->turn++;
                $this->changeTurn();
            }
        }


        if($this->checkWin($board))
        {
            if($player == $this->player1)
            {
                $this->player2->id->send(json_encode(array("type"=>"game","msg"=>"win", "who"=>"enemy")));
                $this->player1->id->send(json_encode(array("type"=>"game","msg"=>"win", "who"=>"client")));
            }
            else
            {
                $this->player2->id->send(json_encode(array("type"=>"game","msg"=>"win", "who"=>"client")));
                $this->player1->id->send(json_encode(array("type"=>"game","msg"=>"win", "who"=>"enemy")));
            }
        }
    }
    
    private function whoseTurn() {
        if($this->turn%2 === 0)
            return($this->player1);
        if($this->turn%2 === 1)
            return($this->player2);
    }

    private function changeTurn() {
        if($this->turn%2 === 0)
        {
            $this->player1->id->send(json_encode(array("type"=>"game","msg"=>"turn", "turn"=>true)));
            $this->player2->id->send(json_encode(array("type"=>"game","msg"=>"turn", "turn"=>false)));
        }
        if($this->turn%2 === 1)
        {
            $this->player1->id->send(json_encode(array("type"=>"game","msg"=>"turn", "turn"=>false)));
            $this->player2->id->send(json_encode(array("type"=>"game","msg"=>"turn", "turn"=>true)));
        }
    }

    private function checkWin($board) {
        $winned = true;
        if($this->turn > 35 ) {
            for($i=0;$i < sizeof($board); $i++)
            {
                for($n=0;$n < sizeof($board[$i]);$n++)
                {
                    if($board[$i][$n] == 1)
                        {
                            $winned = false;
                            break;
                        }
                }
            }

            if($winned == true)
            {
                $this->winned = true;
                return(true);
            }
            else
            {
                return(false);
            }
        }
    }
}

?>