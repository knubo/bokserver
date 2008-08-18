<?php
include_once ("../../conf/AppConfig.php");
include_once ("../../classes/auth/User.php");
include_once ("../../classes/util/DB.php");
include_once ("../../classes/auth/RegnSession.php");
include_once ("../../classes/webscrape/bibsys.php");
include_once ("../../classes/webscrape/bokkilden.php");

$action = array_key_exists("action", $_REQUEST) ? $_REQUEST["action"] : "bokkilden";
$isbn = array_key_exists("isbn", $_REQUEST) ? $_REQUEST["isbn"] : "9788204112170";

$bibsys = new Bibsys();
$bokkilden = new Bokkilden();
$db = new DB();
$regnSession = new RegnSession($db);
$regnSession->auth();

switch ($action) {
	case "bibsys" :
		$bookInfo = $bibsys->getBookInfo($isbn);
		echo json_encode($bookInfo);
		break;
	case "bokkilden";
		$bookInfo = $bokkilden->getBookInfo($isbn);
		echo json_encode($bookInfo);
		break;
}
?>
