<?php
class Category {
	private $db;

	function Category($dbi) {
		$this->db = $dbi;
	}

	function search($type, $query, $limit) {
		$query = "$query%";

		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "category where name like ? limit ?");

		$prep->bind_params("si", $query, $limit);
		
		return $prep->execute();
	}
}

?>