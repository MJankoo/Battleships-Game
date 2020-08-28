var old;
var id;
var oldId;
var masts = 0;
var ships = [['','','',''],['','',''],['','',''],['',''],['',''],['',''],['','']];
var putMasts = 0;
var shipDirection = 0;

    $(".ship-body").click(function(event) {

        id = parseInt(event.target.id.split("ship")[1]);

        if(typeof old !== 'undefined' && putMasts !== masts)
        {
            old.css("border-color", "#00CC66");
        }
        $(this).css("border-color", "#006632");
                

        chooseShip(id);

        old = $(this);
    });

    function chooseShip(id)
    {
        if(oldId !== undefined) {
            if(putMasts !== masts && ships[oldId][(ships[oldId].length-1)] === "") {
                for(i=0;i < ships[oldId].length;i++){
                    if(ships[oldId][i] !== "") {
                        $("#client"+ships[oldId][i]).css("background-color", "#fff");
                        ships[oldId][i] = "";
                    }
                }
            }
        }
        putMasts = 0;
        switch(id) {
            case 0:
                masts = 4;
                break;
            case 1:
            case 2:
                masts = 3;
                break;
            case 3:
            case 4:
            case 5:
            case 6:
                masts = 2;
                break;
        }
        oldId = id;
    }



    $("#board_client td").click(function(event){
        if(gameStarted === false){
        if(masts === 0) {
            alert("Choose ship that u want to put");
        }
        else {
            elementId = parseInt(event.target.id.split("client")[1]);
            if(putMasts < masts && ships[id][putMasts] === '') {
                switch(putMasts) {
                    case 0: //first mast
                        putMast(elementId, putMasts);
                        break;
                    case 1: //second mast
                        shipPosition = ships[id][0]; 
                        if(elementId === (shipPosition+10) || elementId === (shipPosition-10) || elementId === (shipPosition+1) || elementId === (shipPosition-1) && Math.floor(elementId/10) === Math.floor(ships[id][0]/10))
                            putMast(elementId, putMasts);
                        else
                            alert("You can't put ship there");
                        
                        if(ships[id][1] === (ships[id][0]+1) || ships[id][1] === (ships[id][0]-1))
                            shipDirection = "x";
                        
                        if(ships[id][1] === (ships[id][0]+10) || ships[id][1] === (ships[id][0]-10))
                            shipDirection = "y";
                        
                        break;
                    case 2: //third mast
                        if(shipDirection === "x") {
                            if(elementId === (ships[id][1]+1) || elementId === (ships[id][1]-1) || elementId === (ships[id][0]+1) || elementId === (ships[id][0]-1) && Math.floor(ships[id][0]/10) === Math.floor(elementId/10))
                                putMast(elementId, putMasts);
                            else
                                alert("You can't put mast there!");
                        }

                        if(shipDirection === "y") {
                            if(elementId === (ships[id][1]+10) || elementId === (ships[id][1]-10) || elementId === (ships[id][0]+10) || elementId === (ships[id][0]-10)) 
                                putMast(elementId, putMasts);
                            else
                                alert("You can't put mast there!");
                        }

                        break; 
                    case 3: //fourth mast
                        if(shipDirection === "x") {
                            if(elementId === (ships[id][2]+1) || elementId === (ships[id][2]-1) || elementId === (ships[id][0]+1) || elementId === (ships[id][0]-1) || elementId === (ships[id][1]+1) || elementId === (ships[id][1]-1)) 
                                putMast(elementId, putMasts);
                            else
                                alert("You can't put mast there!");
                        }

                        if(shipDirection === "y") {
                            if(elementId === (ships[id][2]+10) || elementId === (ships[id][2]-10) || elementId === (ships[id][0]+10) || elementId === (ships[id][0]-10) || elementId === (ships[id][1]+10) || elementId === (ships[id][1]-10))
                                putMast(elementId, putMasts);
                            else
                                alert("You can't put mast there!");
                        }
                        break;
                }
            }
            else
                alert("U put all masts!");
        }
    }
    });


    function putMast(elementId, numberOfMast) {
        error = false;
        for(i=0;i<ships.length; i++) {
            if(ships[i].includes(elementId)) {
                error = "Here is another mast!";
                break;
            }

            if(i !== id) {
                for(n=0;n<ships[i].length;n++) {
                    if((elementId%10) !== 9 && (elementId%10) !== 0) {
                        if(elementId === (ships[i][n]+1) || elementId === (ships[i][n]-1) || elementId === (ships[i][n]+10) || elementId === (ships[i][n]-10)) {
                            error = "Ships must be one grid apart";
                            break;
                        }
                    }
                    else {
                        if((elementId%10) === 9) {
                            if( elementId === (ships[i][n]+1) || elementId === (ships[i][n]+10) || elementId === (ships[i][n]-10)) {
                                error = "Ships must be one grid apart1";
                                break;
                            }
                        }
                        else {
                            if( elementId === (ships[i][n]-1) || elementId === (ships[i][n]+10) || elementId === (ships[i][n]-10)) {
                                error = "Ships must be one grid apart";
                                break;
                            }
                        }
                    }
                }
            }
        }

        if(error === false) {
            $("#client"+elementId).css("background-color", "#00CC66");
            ships[id][numberOfMast] = elementId;

            putMasts++;

            if(putMasts===masts)
            {
                $("#ship"+id).css("background-color", "red");
                $("#ship"+id).css("border-color", "red");
            }

        }
        else
            alert(error);

    }


    $(".delete-ship").click(function(event){
        deleteId = parseInt(event.target.dataset.id);
        
        for(i=0;i < ships[deleteId].length;i++){
            $("#client"+ships[deleteId][i]).css("background-color", "#fff");

            $("#ship"+deleteId).css("background-color", "#00CC66");
            $("#ship"+deleteId).css("border-color", "#00CC66");

            ships[deleteId][i] = "";
        }

        if(deleteId === id)
        {
            putMasts=0;
        }

    });