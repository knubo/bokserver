<?php
class Base {
   protected $table;
   protected $db;
   
   	function search($type, $query, $limit) {
		$query = "$query%";

		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . $this->table." where name like ? limit ?");

		$prep->bind_params("si", $query, $limit);
		
		return $prep->execute();
	}
   
   	
}

?>