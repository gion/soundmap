<?php
  // enable cors
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
  header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");


	include_once "constants.php";
	include_once "dbManager.php";
	include_once "server.php";


  $db = new EasyPhpMysqlDb(USERNAME, PASS, HOST, DB);
	$server = new Server($db, TABLE);
	$server->serve();
