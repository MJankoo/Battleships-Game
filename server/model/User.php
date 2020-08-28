<?php

class User {

    public $number;
    public $position;
    public $room;
    public $id;

    public function __construct($number, $id, $position)
    {
        $this->number = $number;
        $this->id = $id;
        $this->position = $position;
        $this->isInRoom = false;
        $this->room = "-1";
    }

    public function isInRoom()
    {
        if($this->position == "room")
            return(true);
        else
            return(false);
    }

}

?>