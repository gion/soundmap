<?php
	include_once "constants.php";
	include_once "dbManager.php";
	include_once "server.php";


  $db = new EasyPhpMysqlDb(USERNAME, PASS, HOST, DB);
	$server = new Server($db, TABLE);
	$server->serve();
