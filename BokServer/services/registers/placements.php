<?php
include_once ("../../conf/AppConfig.php");
include_once ("../../classes/util/DB.php");
include_once ("../../classes/auth/User.php");
include_once ("../../classes/auth/RegnSession.php");
include_once ("../../classes/registers/base.php");
include_once ("../../classes/registers/placement.php");

$action = array_key_exists("action", $_REQUEST) ? $_REQUEST["action"] : "";
$id = array_key_exists("id", $_REQUEST) ? $_REQUEST["id"] : "";
$type = array_key_exists("type", $_REQUEST) ? $_REQUEST["type"] : "";
$search = array_key_exists("search", $_REQUEST) ? $_REQUEST["search"] : "";
$limit = array_key_exists("limit", $_REQUEST) ? $_REQUEST["limit"] : "";

$db = new DB();
$regnSession = new RegnSession($db);
$loggedInUser = $regnSession->auth();
$accPlacement = new Placement($db);

switch ($action) {
	case "detailedsearch" :
		$result = $accPlacement->search($type, $search, 10000);
		include("../../renders/placementsearch.php");
		break;
	case "search" :
		echo json_encode($accPlacement->search($type, $search, $limit));
		break;
	case "get" :
		echo json_encode($accPlacement->get($id));
		break;
	case "save" :
		$regnSession->checkWriteAccess();		
		echo json_encode($accPlacement->save($_REQUEST));
		break;

}
?>