<?php
class Base {
	protected $table;
	protected $field;
	protected $db;

	function search($type, $query, $limit) {
		$query = "$query%";

		$prep = $this->db->prepare("select * from " . AppConfig :: prefix() . $this->table . " where name like ? order by name limit ".addslashes($limit));

		$prep->bind_params("s", $query);

		return $prep->execute();
	}
	
	function get($id) {
		$prep = $this->db->prepare("select * from " . AppConfig :: prefix() . $this->table . " where id = ?");

		$prep->bind_params("i", $id);

		return array_pop($prep->execute());	
	}
	
	function checkDuplicate($name) {

		$prep = $this->db->prepare("select * from " . AppConfig :: prefix()  . $this->table . " where name=?");

		$prep->bind_params("s", $name);

		$res = $prep->execute();

		if (count($res) == 0) {
			return;
		}
		
		header("HTTP/1.0 513 Validation Error");

		$fields = array ("duplicate");
		die(json_encode($fields));
	}

	function save($data) {

		if ($data["id"] > 0) {
			$prep = $this->db->prepare("update " . AppConfig :: prefix()  . $this->table . " set name=? where id = ?");
			$prep->bind_params("si", $data["name"], $data["id"]);
			$prep->execute();
			return $this->get($data["id"]);
		}
		
		$this->checkDuplicate($data["name"]);

		$prep = $this->db->prepare("insert into " . AppConfig :: prefix() . $this->table . " set name=?");
		$prep->bind_params("s", $data["name"]);
		$prep->execute();
		return $this->get($this->db->insert_id());
	}	

}
?>