<?php
include_once ("../../conf/AppConfig.php");
include_once ("../../classes/auth/User.php");
include_once ("../../classes/util/DB.php");
include_once ("../../classes/auth/RegnSession.php");
include_once ("../../classes/webscrape/bibsys.php");

$action = array_key_exists("action", $_REQUEST) ? $_REQUEST["action"] : "bibsys";
$isbn = array_key_exists("isbn", $_REQUEST) ? $_REQUEST["isbn"] : "8270342327";

$bibsys = new Bibsys();

$db = new DB();
$regnSession = new RegnSession($db);
$regnSession->auth();

switch ($action) {
	case "bibsys" :
		$bookInfo = $bibsys->getBookInfo($isbn);
		echo json_encode($bookInfo);
		break;
}
?>
