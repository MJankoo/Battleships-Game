window.onload = function() {

    randomShipsPosition();

};


var positionX;
var directionX;

function Round(n, k)
{
    var factor = Math.pow(10, k);
    return Math.round(n*factor)/factor;
}

function randomShipsPosition()
{
    positionX = Round((Math.random()*10),0);
    directionX = Math.random();
    if(directionX > 0.5)
        {
            document.getElementById(positionX).style.background = "red";
        }

}