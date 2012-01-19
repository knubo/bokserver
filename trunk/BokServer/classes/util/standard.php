<?php
class Standard {

	private $db;

	function Standard($db) {
		$this->db = $db;
	}

	function setValue($id, $value) {

		$prep = $this->db->prepare("select * from " . AppConfig :: prefix() . "standard where id=?");
		$prep->bind_params("s", $id);
		$query_array = $prep->execute();

		if (count($query_array) > 0) {
			$prep = $this->db->prepare("update " . AppConfig :: prefix() . "standard set value=? where id=?");
			$prep->bind_params("ss", $value, $id);
			$prep->execute();

			return $this->db->affected_rows();
		} else {
			$prep = $this->db->prepare("insert into " . AppConfig :: prefix() . "standard (id, value) values (?,?)");
			$prep->bind_params("ss", $id, $value);
			$prep->execute();

			return $this->db->affected_rows();
		}
	}

	function getValue($id) {
		$prep = $this->db->prepare("select value from " . AppConfig :: prefix() . "standard where id=?");
		$prep->bind_params("s", $id);

		$return_array = array ();

		$query_array = $prep->execute();
		if (count($query_array) >= 0) {
			for ($i = 0; $i < count($query_array); $i++) {
				$return_array[$i] = $query_array[$i]["value"];
			}
		}

		return $return_array;
	}

	function getOneValue($id) {
		$res = $this->getValue($id);

		if (count($res)) {
			return $res[0];
		}
	}
}
?>
