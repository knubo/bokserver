<?php
class Book {
	private $db;

	function Book($dbi) {
		$this->db = $dbi;
	}

	function search($type, $query, $limit) {
		$query = implode("%", explode(" ", $query)) . "%";

		switch ($type) {
			case "title" :
				$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "book where title like ? limit ?");
				break;
			case "ISBN" :
				$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "book where ISBN like ? limit ?");
				break;

		}
		$prep->bind_params("si", $query, $limit);

		return $prep->execute();
	}

	function get($id) {
		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "book where id = ?");

		$prep->bind_params("i", $id);

		return array_pop($prep->execute());
	}

	function checkDuplicate($usernumber, $id) {

		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "book where usernumber=? and id <> ?");

		$prep->bind_params("ii", $usernumber, $id);

		$res = $prep->execute();

		if (count($res) == 0) {
			return;
		}

		header("HTTP/1.0 513 Validation Error");

		$fields = array (
			"usernumber"
		);
		die(json_encode($fields));
	}

	function save($data) {
		if ($data["coauthor_id"] == 0) {
			$data["coauthor_id"] = null;
		}
		if ($data["illustrator_id"] == 0) {
			$data["illustrator_id"] = null;
		}
		if ($data["translator_id"] == 0) {
			$data["translator_id"] = null;
		}
		if ($data["editor_id"] == 0) {
			$data["editor_id"] = null;
		}
		if ($data["placement_id"] == 0) {
			$data["placement_id"] = null;
		}
		if ($data["series"] == 0) {
			$data["series"] = null;
		}
		if ($data["publisher_id"] == 0) {
			$data["publisher_id"] = null;
		}

		if ($data["id"] > 0) {
			$this->checkDuplicate($data["usernumber"], $data["id"]);
			$prep = $this->db->prepare("update " . AppConfig :: DB_PREFIX . "book set usernumber=?, title=?, subtitle=?, org_title=?, ISBN=?, author_id=?, coauthor_id=?, illustrator_id=?, translator_id=?, editor_id=?, publisher_id=?, price=?, published_year=?, written_year=?, category_id=?, placement_id=?, edition=?, impression=?, series=?, number_in_series=? where id=?");
			$prep->bind_params("issssiiiiiisiiiiiiisi", $data["usernumber"], $data["title"], $data["subtitle"], $data["org_title"], $data["ISBN"], $data["author_id"], $data["coauthor_id"], $data["illustrator_id"], $data["translator_id"], $data["editor_id"], $data["publisher_id"], $data["price"], $data["published_year"], $data["written_year"], $data["category_id"], $data["placement_id"], $data["edition"], $data["impression"], $data["series"], $data["number_in_series"], $data["id"]);
			$prep->execute();
			return $this->get($data["id"]);
		}

		$prep = $this->db->prepare("insert into " . AppConfig :: DB_PREFIX . "book set usernumber=?, title=?, subtitle=?, org_title=?, ISBN=?, author_id=?, coauthor_id=?, illustrator_id=?, translator_id=?, editor_id=?, publisher_id=?, price=?, published_year=?, written_year=?, category_id=?, placement_id=?, edition=?, impression=?, series=?, number_in_series=?");
		$prep->bind_params("issssiiiiiisiiiiiiis", $data["usernumber"], $data["title"], $data["subtitle"], $data["org_title"], $data["ISBN"], $data["author_id"], $data["coauthor_id"], $data["illustrator_id"], $data["translator_id"], $data["editor_id"], $data["publisher_id"], $data["price"], $data["published_year"], $data["written_year"], $data["category_id"], $data["placement_id"], $data["edition"], $data["impression"], $data["series"], $data["number_in_series"]);
		$prep->execute();
		return $this->get($this->db->insert_id());
	}

	function nextUserNumber() {
		$prep = $this->db->prepare("select max(usernumber) as nextUserNumber from " . AppConfig :: DB_PREFIX . "book");
		$res = $prep->execute();

		$data = array_pop($res);

		if ($data["nextUserNumber"]) {
			$data["nextUserNumber"]++;
		} else {
			$data["nextUserNumber"] = 1;
		}

		return $data;
	}
}
?>
