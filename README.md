# Battleships-Game

## Table of contents
* [General info](#general-info)
* [Requirements](#requirements)
* [Technologies](#technologies)
* [Setup](#setup)
  - [Starting server](#starting-server)
  - [Setting up client side](#setting-up-client-side)

## General Info
Classic ship game created using PHP, Socketo.me, JavaScript and HTML

## Requirements
* Composer
* Apache Server

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
