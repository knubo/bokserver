<?php
class Placement {
	private $db;

	function Placement($dbi) {
		$this->db = $dbi;
	}

	function search($type, $query, $limit) {
		$query = "$query%";

		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "placement where placement like ? limit ?");

		$prep->bind_params("si", $query, $limit);
		
		return $prep->execute();
	}
}

?>