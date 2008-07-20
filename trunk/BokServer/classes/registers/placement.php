<?php
class Placement extends Base {

	function Placement($dbi) {
		$this->db = $dbi;
		$this->table = "placement";
	}

	function search($type, $query, $limit) {
		$query = "$query%";

		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "placement where placement like ? limit ?");

		$prep->bind_params("si", $query, $limit);

		return $prep->execute();
	}

	function checkDuplicate($placement) {

		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "placement where placement=?");

		$prep->bind_params("s", $placement);

		$res = $prep->execute();

		if (count($res) == 0) {
			return;
		}

		header("HTTP/1.0 513 Validation Error");

		$fields = array (
			"duplicate"
		);
		die(json_encode($fields));
	}

	function save($data) {

		if ($data["id"] > 0) {
			$prep = $this->db->prepare("update " . AppConfig :: DB_PREFIX . "placement set placement=?,info=? where id = ?");
			$prep->bind_params("ssi", $data["placement"], $data["info"], $data["id"]);
			$prep->execute();
			return $this->get($data["id"]);
		}

		$this->checkDuplicate($data["placement"]);

		$prep = $this->db->prepare("insert into " . AppConfig :: DB_PREFIX . "placement set placement=?, info=?");
		$prep->bind_params("ss", $data["placement"], $data["info"]);
		$prep->execute();
		return $this->get($this->db->insert_id());
	}
}
?>