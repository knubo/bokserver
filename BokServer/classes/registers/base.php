<?php
class Base {
	protected $table;
	protected $db;

	function search($type, $query, $limit) {
		$query = "$query%";

		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . $this->table . " where name like ? limit ?");

		$prep->bind_params("si", $query, $limit);

		return $prep->execute();
	}
	
	function get($id) {
		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . $this->table . " where id = ?");

		$prep->bind_params("i", $id);

		return array_pop($prep->execute());	
	}

}
?>