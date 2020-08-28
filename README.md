# Battleships-Game

## Table of contents
* [General info](#general-info)
* [Technologies](#technologies)
* [Setup](#setup)

## General Info
Classic ship game created using PHP, Socketo.me, JavaScript and HTML

## Technologies
* PHP 7.4
* Socketo.me
* HTML
* jQuery

## Setup
```
$ git clone git@github.com:MJankoo/Battleships-Game.git
```

### Starting server
```
$ composer install
$ php server/websocket-server.php
```

### Setting up client side
You need to set the server's IP in client/config.php and run apache with all files from client folder
