<?php
include_once ("../conf/AppConfig.php");
include_once ("../classes/auth/User.php");
include_once ("../classes/registers/book.php");
include_once ("../classes/registers/person.php");
include_once ("../classes/util/DB.php");
include_once ("../classes/util/logger.php");
include_once ("../classes/auth/RegnSession.php");

$db = new DB();
$regnSession = new RegnSession($db);
$logger = new Logger($db);
$regnSession->auth();

$accBook = new Book($db);
$accPerson = new Person($db);
$res = array();

$res["bookCount"] = $accBook->bookCount();
$res["people"] = $accPerson->summary();

$nextUserNumber = $accBook->nextUserNumber();
$res["nextUserNumber"] = $nextUserNumber["nextUserNumber"];
echo json_encode($res);

?>