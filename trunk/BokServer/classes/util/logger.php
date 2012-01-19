<?php

/*
 * Created on Aug 9, 2007
 *
 */

class Logger {

    private $db;

    function Logger($db) {
        $this->db = $db;
    }

    function log($category, $action, $message) {
        $prep = $this->db->prepare("insert into " . AppConfig :: prefix() . "log (occured,username,category,action,message) values (now(),?,?,?,?)");
        $prep->bind_params("ssss", $_SESSION["username"], $category, $action, $message);
        $prep->execute();
    }

    function list_entries($pos) {
    	if(!$pos) {
    		$prep = $this->db->prepare("select * from " . AppConfig :: prefix() . "log order by id desc limit 30");
    	} else {
    		$prep = $this->db->prepare("select * from " . AppConfig :: prefix() . "log where id>= ? order by id desc limit 30");
            $prep->bind_params("i", $pos);
    	}

        return $prep->execute();
    }
}
?>