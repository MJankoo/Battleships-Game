<?php
    require_once("config.php");
?>

<!DOCTYPE html>
<html>
<head>
	<title>Battleships</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
    <script src="js/jquery.js" type="text/javascript"></script>
    <link rel="shortcut icon" href="img/favicon.png">
    <link rel="stylesheet" href="css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500&display=swap" rel="stylesheet">
</head>
<body>
	<div id="container">
        <div id="container_lobby">
            <h1>Battleships Game</h1>
            <div id="rooms_list"></div>

            <div id="createRoom">Create new room!</div>
        </div>
    </div>


        <script type="text/javascript">
        var gameStarted = false;
        var userId;
        var roomId;
        var turn;
        var ready = false;
        jQuery(function($){
			// Connecting to Websocket
			var websocket_server = new WebSocket("ws:// <?php echo($config['serverIP']) ?> /");
			websocket_server.onopen = function(e) {
                console.log("Connected to server!");
			}
			websocket_server.onerror = function(e) {
				// Errorhandling
                alert("Błąd połączenia z serwerem!");
			}
			websocket_server.onmessage = function(e)
			{
                var json = JSON.parse(e.data);
				switch(json.type) {
					case 'rooms':
                        console.log(json.msg);
                        $("#rooms_list").html("");
                        $("#rooms_list").append("<ul>");
                        for(i=0;i<json.msg.length;i++) {
                            if(i%2===0)
                                $("#rooms_list").append("<li><p>Room #"+i+"</p><div class='joinRoom' id='button"+i+"'>Join!</div></li>");
                            else
                            $("#rooms_list").append("<li class='li_color'><p>Room #"+i+"</p><div id='button"+i+"' class='joinRoom'>Join!</div></li>");
                            if(json.msg[i].players === 2)
                                {
                                    $("#button"+i).addClass("inactive");
                                }
                        }
                        $("#rooms_list").append("</ul>");
					break;
                    case 'error':
                        alert(json.msg);
                    break;
                    case 'server':
                        switch(json.msg) {
                            case 'goIntoGame':
                                $( "#container" ).load( "game.php", function() {
                                        $("h2").html("Room #"+json.roomId);
                                        roomId = json.roomId;
                                        alert(json.players);
                                        if(json.players === 2) {
                                            $( "#enemy-info" ).html("");
                                        }
                                    }
                                );
                                $.getScript( "js/game.js" )
                                .done(function( script, textStatus ) {
                                    console.log( textStatus );
                                })
                                .fail(function( jqxhr, settings, exception ) {
                                    $( "div.log" ).text( "Triggered ajaxError handler." );
                                });

                            break;
                            case 'players':
                                if(json.players === 2) {
                                    $( "#enemy-info" ).html("");
                                }
                            break;
                            case 'gameStarted':
                                $("#board_enemy").css("opacity", "1");
                                $("#enemy-info").css("display", "none");
                                $("#controls").css("display", "none");

                                $("#board_client td").addClass("td-inactive");
                                $("#board_enemy td").removeClass("td-inactive");
                                $("#board_enemy td").addClass("gameClass");
                                turn = json.turn;
                                gameStarted = true;
                                break;
                            case 'opponentDisconnected':
                                $("#board_enemy").css("opacity", "0.4");
                                $("#enemy-info").css("display", "block");
                                $( "#enemy-info" ).html("NO PLAYER");
                                $("#controls").css("display", "flex");

                                $("#board_client td").removeClass("td-inactive");
                                $("#board_enemy td").addClass("td-inactive");
                                $("#board_enemy td").removeClass("gameClass");

                                $("#board_enemy td").removeClass("missed");
                                $("#board_enemy td").removeClass("hit");
                                $("#board_client td").removeClass("missed");
                                $("#board_client td").removeClass("hit");

                                gameStarted = false;
                                break;
                        }
                    break;
                    case 'game':
                        switch(json.msg)
                        {
                            case "duplication":
                                alert("You shot here already");
                            break;
                            case "hit":
                                $("#"+json.who+json.position).addClass("hit");
                            break;
                            case "missed":
                                $("#"+json.who+json.position).addClass("missed");
                            break;
                            case "turn":
                                turn = json.turn;
                            break;
                            case "win":
                                if(json.who === "client")
                                {
                                    alert("U WIN!");
                                }
                                if(json.who === "enemy")
                                {
                                    alert("U LOST!");
                                }
                            break;
                        }
                        break;
				}
            }
            
            // Events
            $("#createRoom").click(function (){
                websocket_server.send(
						JSON.stringify({
							'type':'createRoom',
							'chat_msg': 'true'
						})
					);
            });

            var id;
            $(document).on ("click", ".joinRoom", function () {
                id = this.id.split("button")[1];
                websocket_server.send(
						JSON.stringify({
							'type':'joinRoom',
							'roomId': id
						})
					);
            });

            $(document).on ("click", "#ready-button", function () {
                
                error = false;
                console.log(ships);

                if(ready === false) {
                    for(i=0;i<ships.length; i++) {
                        if(ships[i].includes("")) {
                            error = "You haven't placed all ships!";
                            break;
                        }
                    }

                    if(error === false) {
                        $(this).css("background-color", "red");
                        $(this).html("I am unready!");
                        
                        websocket_server.send(
                            JSON.stringify({
                                'type':'setReady',
                                'roomId': roomId,  
                                'ships': ships
                            })
                        );
                        ready = true;
                    } else
                        alert(error);
                }
                else
                {
                    websocket_server.send(
                        JSON.stringify({
                            'type':'unsetReady',
                            'roomId': roomId 
                            })
                        );
                    ready = false;
                }
            });

            $(document).on ("click", "#board_enemy td", function (event) {
                if(turn === true)
                {
                    id = parseInt(event.target.id.split("enemy")[1]);

                    websocket_server.send(
                            JSON.stringify({
                                'type':'doMove',
                                'roomId': roomId,
                                'shotPosition': id
                                })
                            );
                }
            });
		});
		</script>
</body>
</html>
