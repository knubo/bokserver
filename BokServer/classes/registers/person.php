<?php
class Person {
	private $db;

	function Person($dbi) {
		$this->db = $dbi;
	}

	function searchDetailed($data) {
		$searchWrap = $this->db->search("select * from " . AppConfig :: DB_PREFIX . "person B", "order by lastname");
		$searchWrap->addAndParam("s", "firstname", $data["firstname"]."%");
		$searchWrap->addAndParam("s", "lastname", $data["lastname"]."%");
		
		return $searchWrap->execute();
	}

	function search($type, $query, $limit) {
		$query = implode("%", explode(" ", $query)) . "%";

		if (strlen($type) > 1) {
			$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "person where " .
			"(search1 like ? or " .
			" search2 like ?) limit ".addslashes($limit));
			$prep->bind_params("ss", $query, $query);
			return $prep->execute();
		}

		$sql = "select * from " . AppConfig :: DB_PREFIX . "person where " .
		"(search1 like ? or " .
		" search2 like ?) and ";

		switch ($type) {
			case "I" :
				$sql .= "illustrator = 1";
				break;
			case "A" :
				$sql .= "author = 1";
				break;
			case "T" :
				$sql .= "translator = 1";
				break;
			case "E" :
				$sql .= "editor = 1";
				break;
			case "R":
				$sql .= "reader = 1";
				break;
				
		}
		$prep = $this->db->prepare("$sql limit ".addslashes($limit));

		$prep->bind_params("ss", $query, $query);

		return $prep->execute();
	}

	function get($id) {
		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "person where id = ?");

		$prep->bind_params("i", $id);

		return array_pop($prep->execute());
	}

	function checkDuplicate($firstname, $lastname) {

		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "person where firstname=? and lastname=?");

		$prep->bind_params("ss", $firstname, $lastname);

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
		$search1 = $data["firstname"] . " " . $data["lastname"];
		$search2 = $data["lastname"] . " " . $data["firstname"];

		if ($data["id"] > 0) {
			$prep = $this->db->prepare("update " . AppConfig :: DB_PREFIX . "person set firstname=?, lastname=?, illustrator=?, editor=?, author=?, translator=?, reader=?, search1=?, search2=? where id = ?");
			$prep->bind_params("ssiiiiissi", $data["firstname"], $data["lastname"], $data["illustrator"], $data["editor"], $data["author"], $data["translator"], $data["reader"], $search1, $search2, $data["id"]);
		} else {
			$this->checkDuplicate($data["firstname"], $data["lastname"]);

			$prep = $this->db->prepare("insert into " . AppConfig :: DB_PREFIX . "person set firstname=?, lastname=?, illustrator=?, editor=?, author=?, translator=?, reader=?, search1=?, search2=?");
			$prep->bind_params("ssiiiiiss", $data["firstname"], $data["lastname"], $data["illustrator"], $data["editor"], $data["author"], $data["translator"], $data["reader"], $search1, $search2);

		}
		$prep->execute();

		if ($data["id"] > 0) {
			return $this->get($data["id"]);
		} else {
			return $this->get($this->db->insert_id());
		}
	}
	
	function summary() {
	   $prep = $this->db->prepare("select count(*) as c,illustrator,translator,author,editor,reader from " . AppConfig :: DB_PREFIX . "person group by illustrator, translator, author, editor,reader");
	   return $prep->execute();	
	}
}
?>
