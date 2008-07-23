<?php
include_once ("../../conf/AppConfig.php");
include_once ("../../classes/util/DB.php");
include_once ("../../classes/auth/User.php");
include_once ("../../classes/auth/RegnSession.php");
include_once ("../../classes/registers/book.php");

$id = array_key_exists("id", $_REQUEST) ? $_REQUEST["id"] : null;
$userNumber = array_key_exists("userNumber", $_REQUEST) ? $_REQUEST["userNumber"] : 0;
$action = array_key_exists("action", $_REQUEST) ? $_REQUEST["action"] : "";
$type = array_key_exists("type", $_REQUEST) ? $_REQUEST["type"] : null;
$search = array_key_exists("search", $_REQUEST) ? $_REQUEST["search"] : null;
$limit = array_key_exists("limit", $_REQUEST) ? $_REQUEST["limit"] : null;

$db = new DB();
$regnSession = new RegnSession($db);
$loggedInUser = $regnSession->auth();
$accBook = new Book($db);

switch ($action) {
	case "delete":
		echo json_encode($accBook->delete($id));
		break;
	
	case "search" :
		echo json_encode($accBook->search($type, $search, $limit));
		break;
	case "detailedsearch" :
		$result = $accBook->searchDetailed($_REQUEST);
		include("../../renders/booksearch.php");
		break;
	case "getfull" :
		if($userNumber > 0) {
			echo json_encode($accBook->getfullUserNumber($userNumber));
		} else {
			echo json_encode($accBook->getfull($id));
		}
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