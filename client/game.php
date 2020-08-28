    <div id="goBack"><a href="index.php">GO BACK</a></div>
    <h1 id="game-title">Battleships Game</h1>
    <h2>Room #1</h2>
    <div id="game_container">
        <div class="board" id="board_client">
            <table>
                <tr>
                    <th></th><th>A</th><th>B</th><th>C</th><th>D</th><th>E</th><th>F</th><th>G</th><th>H</th><th>I</th><th>J</th>
                </tr>
                <?php
                for($i=0;$i<10;$i++)
                {
                    echo("<tr><th>".($i+1)."</th>");
                    $number = $i*10;
                    for($n=0;$n<10;$n++)
                    {
                        
                        echo("<td class='board_field' id='client".($number+$n)."'></td>");
                    }
                    echo("</tr>");
                }
                ?>
            </table>
        </div>


        <div class="board" id="board_enemy">
            <table>
                <tr>
                    <th></th><th>A</th><th>B</th><th>C</th><th>D</th><th>E</th><th>F</th><th>G</th><th>H</th><th>I</th><th>J</th>
                </tr>
                <?php
                for($i=0;$i<10;$i++)
                {
                    echo("<tr><th>".($i+1)."</th>");
                    $number = $i*10;
                    for($n=0;$n<10;$n++)
                    {
                        
                        echo("<td class='board_field td-inactive' id='enemy".($number+$n)."'></td>");
                    }
                    echo("</tr>");
                }
                ?>
            </table>
            <div id="enemy-info">NO PLAYER</div>
        </div>
    </div>

    <div id="controls"> 
        <div id="ships">
                <div class="ship four-masted"><div class="ship-body" id="ship0">4</div><div class="delete-ship" data-id="0">X</div></div>
                <div class="ship three-masted"><div class="ship-body" id="ship1">3</div><div class="delete-ship" data-id="1">X</div></div>
                <div class="ship three-masted"><div class="ship-body" id="ship2">3</div><div class="delete-ship" data-id="2">X</div></div>
                <div class="ship two-masted"><div class="ship-body" id="ship3">2</div><div class="delete-ship" data-id="3">X</div></div>
                <div class="ship two-masted"><div class="ship-body" id="ship4">2</div><div class="delete-ship" data-id="4">X</div></div>
                <div class="ship two-masted"><div class="ship-body" id="ship5">2</div><div class="delete-ship" data-id="5">X</div></div>
                <div class="ship two-masted"><div class="ship-body" id="ship6">2</div><div class="delete-ship" data-id="6">X</div></div>
        </div>
        <div id="right-side">
            <div id="ready-button">I am ready!<div>
        </div>
    </div>
        