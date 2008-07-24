<?php
include_once ("../../conf/AppConfig.php");
include_once ("../../classes/util/DB.php");
include_once ("../../classes/auth/User.php");
include_once ("../../classes/auth/RegnSession.php");
include_once ("../../classes/registers/base.php");
include_once ("../../classes/registers/serie.php");

$action = array_key_exists("action", $_REQUEST) ? $_REQUEST["action"] : "";
$id = array_key_exists("id", $_REQUEST) ? $_REQUEST["id"] : "";
$type = array_key_exists("type", $_REQUEST) ? $_REQUEST["type"] : "";
$search = array_key_exists("search", $_REQUEST) ? $_REQUEST["search"] : "";
$limit = array_key_exists("limit", $_REQUEST) ? $_REQUEST["limit"] : "";

$db = new DB();
$regnSession = new RegnSession($db);
$loggedInUser = $regnSession->auth();
$accSerie = new Serie($db);

switch ($action) {
	case "detailedsearch" :
		$result = $accSerie->search($type, $search, 10000);
		include ("../../renders/seriesearch.php");
		break;

	case "search" :
		echo json_encode($accSerie->search($type, $search, $limit));
		break;
	case "get" :
		echo json_encode($accSerie->get($id));
		break;
	case "save" :
		echo json_encode($accSerie->save($_REQUEST));
		break;

}
?>