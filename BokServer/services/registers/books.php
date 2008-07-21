<?php
include_once ("../../conf/AppConfig.php");
include_once ("../../classes/util/DB.php");
include_once ("../../classes/auth/User.php");
include_once ("../../classes/auth/RegnSession.php");
include_once ("../../classes/registers/book.php");

$id = array_key_exists("id", $_REQUEST) ? $_REQUEST["id"] : "";
$action = array_key_exists("action", $_REQUEST) ? $_REQUEST["action"] : "nextUserNumber";
$type = array_key_exists("type", $_REQUEST) ? $_REQUEST["type"] : "";
$search = array_key_exists("search", $_REQUEST) ? $_REQUEST["search"] : "";
$limit = array_key_exists("limit", $_REQUEST) ? $_REQUEST["limit"] : "";

$db = new DB();
$regnSession = new RegnSession($db);
$loggedInUser = $regnSession->auth();
$accBook = new Book($db);

switch ($action) {
	case "search" :
		echo json_encode($accBook->search($type, $search, $limit));
		break;
	case "getfull" :
		echo json_encode($accBook->getfull($id));
		break;
	case "get" :
		echo json_encode($accBook->get($id));
		break;
	case "save" :
		echo json_encode($accBook->save($_REQUEST));
		break;
	case "nextUserNumber" :
		echo json_encode($accBook->nextUserNumber());
		break;
}
?>