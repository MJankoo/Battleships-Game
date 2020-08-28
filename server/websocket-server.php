<?php
set_time_limit(0);

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require_once './vendor/autoload.php';
require_once 'model/Room.php';
require_once 'model/User.php';

class Connection implements MessageComponentInterface {
	protected $clients;
	protected $users = [];
	private $id = 0;
	public $rooms = [];

	public function __construct() {
		$this->clients = new \SplObjectStorage;
	}
	
	public function createRoom($user_id)
	{
		array_push($this->rooms, new Room());
		$this->sendRooms();
		$this->joinRoom($user_id, $this->id);
		$this->id = $this->id + 1;
	}

	public function joinRoom($user_id, $room_id)
	{
		if($this->users[$user_id]->isInRoom() == false) {
			if($this->rooms[$room_id]->joinRoom($user_id, $this->users[$user_id])) {
				$this->users[$user_id]->id->send(json_encode(array("type"=>"server","msg"=>"goIntoGame", "roomId"=> $room_id, "players"=>$this->rooms[$room_id]->players)));
				$this->users[$user_id]->position = "room";
				$this->users[$user_id]->room = $room_id;
				$this->sendRooms();
			}
			else
			{
				$this->users[$user_id]->id->send(json_encode(array("type"=>"error","msg"=>"Room is full")));
			}

		}
		else {
			$this->users[$user_id]->id->send(json_encode(array("type"=>"error","msg"=>"Jesteś już w pokoju!")));
			return(false);
		}

	}

	public function onOpen(ConnectionInterface $conn) {
		
		if(!isset($this->users[$conn->resourceId]))
		{
			$this->clients->attach($conn);

			$conn->send(json_encode(array("type"=>'rooms',"msg"=>$this->rooms)));
			$this->users[$conn->resourceId] = new User($conn->resourceId, $conn, "index");
		}
	}

	public function onClose(ConnectionInterface $conn) {
		$user = $this->users[$conn->resourceId];

		$this->clients->detach($conn);
		if($user->isInRoom()) {
			$this->rooms[$user->room]->leaveRoom($user);
			$this->sendRooms();
		}
		unset($this->users[$conn->resourceId]);
	}

	public function onMessage(ConnectionInterface $from,  $data) {
		$from_id = $from->resourceId;
		$user = $this->users[$from_id];
		$data = json_decode($data);
		$type = $data->type;
		switch ($type) {
			case 'createRoom':
				$this->createRoom($from_id);
				break;
			case 'joinRoom':
					$this->joinRoom($from_id, $data->roomId);
				break;
			case 'setReady':
				$roomId = $data->roomId;
				if($this->checkUsersRoom($user, $roomId))
				{
					$this->rooms[$roomId]->setReady($user, $data->ships);
				}
				break;
			case 'unsetReady':
					$roomId = $data->roomId;
					if($this->checkUsersRoom($user, $roomId))
					{
						$this->rooms[$roomId]->unsetReady($user);
					}		
				break;
			case 'doMove':
				$roomId = $data->roomId;
				$position = $data->shotPosition;
				if($this->checkUsersRoom($user, $roomId))
				{
					$this->rooms[$roomId]->game->shoot($user, $position);
				}
				break;
			default:
				echo("Something went wrong, try again later!");
		}
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		$conn->close();
	}

	private function sendRooms() {
		foreach($this->users as $user){
			if($user->position=="index"){
				$user->id->send(json_encode(array("type"=>"rooms","msg"=>$this->rooms)));
			}
		}
	}

	private function checkUsersRoom($user, $roomId) {
		if($user->room == $roomId)
		{
			return(true);
		}
		else
		{
			$user->id->send(json_encode(array("type"=>'server',"msg"=>"error", "alert"=>"You aren't in this room!")));
			return(false);
		}
	}
}
$server = IoServer::factory(
	new HttpServer(new WsServer($connection = new Connection())),
	8080
);
$server->run();
?>