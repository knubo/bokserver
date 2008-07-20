<?php
include_once ("../../conf/AppConfig.php");
include_once ("../../classes/util/DB.php");
include_once ("../../classes/auth/User.php");
include_once ("../../classes/auth/RegnSession.php");
include_once ("../../classes/registers/person.php");

$action = array_key_exists("action", $_REQUEST) ? $_REQUEST["action"] : "";
$id = array_key_exists("id", $_REQUEST) ? $_REQUEST["id"] : null;
$type = array_key_exists("type", $_REQUEST) ? $_REQUEST["type"] : null;
$search = array_key_exists("search", $_REQUEST) ? $_REQUEST["search"] : null;
$limit = array_key_exists("limit", $_REQUEST) ? $_REQUEST["limit"] : null;

$db = new DB();
$regnSession = new RegnSession($db);
$loggedInUser = $regnSession->auth();
$accPerson = new Person($db);

switch ($action) {
	case "search" :
		echo json_encode($accPerson->search($type, $search, $limit));
		break;
	case "get" :
		echo json_encode($accPerson->get($id));
		break;
	case "save" :
		echo json_encode($accPerson->save($_REQUEST));
		break;

}
?>