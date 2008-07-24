<?php
class Book {
	private $db;

	function Book($dbi) {
		$this->db = $dbi;
	}

	function search($type, $query, $limit) {

		switch ($type) {
			case "title" :
				$query = implode("%", explode(" ", $query)) . "%";
				$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "book where title like ? and usernumber is not null limit ?");
				$prep->bind_params("si", $query, $limit);
				break;
			case "ISBN" :
				$query = $query . "%";
				$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "book where ISBN like ? and usernumber is not null limit ?");
				$prep->bind_params("si", $query, $limit);
				break;
			case "usernumber" :
				$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "book where usernumber = ? limit ?");
				$prep->bind_params("ii", $query, $limit);
				break;
		}

		return $prep->execute();
	}
	
	function searchDetailed($data) {
		$searchWrap = $this->db->search("select title, ISBN, usernumber,B.id as id, concat(PLA.placement, ' ',PLA.info) as placement from " . AppConfig :: DB_PREFIX.
		"book B left join (" . AppConfig :: DB_PREFIX . "placement PLA) on (PLA.id=placement_id)",
		"order by title");
		
		$searchWrap->addAndParam("s", "title", $data["title"]);
		$searchWrap->addAndParam("s", "ISBN", $data["ISBN"]);
		$searchWrap->addAndParam("i", "written_year", $data["written_year"]);
		$searchWrap->addAndOrQuery0("i", "author_id", "coauthor_id", $data["author_id"]);
		$searchWrap->addAndParam0("i", "editor_id", $data["editor_id"]);
		$searchWrap->addAndParam0("i", "illustrator_id", $data["illustrator_id"]);
		$searchWrap->addAndParam0("i", "translator_id", $data["translator_id"]);
		$searchWrap->addAndParam0("i", "category_id", $data["category_id"]);
		$searchWrap->addAndParam0("i", "serie", $data["serie"]);
		$searchWrap->addAndParam0("i", "placement_id", $data["placement_id"]);
		$searchWrap->addAndParam0("i", "publisher_id", $data["publisher_id"]);
		$searchWrap->addOnlySql("(usernumber is not null)");
	
		return $searchWrap->execute();
		
	}
	

	function get($id) {
		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "book where id = ?");

		$prep->bind_params("i", $id);

		return array_pop($prep->execute());
	}
	
	function getfullUserNumber($userNumber) {
		$prep = $this->db->prepare("select id from " . AppConfig :: DB_PREFIX . "book where usernumber = ?");

		$prep->bind_params("i", $userNumber);

		$arr = $prep->execute();
		
		if(count($arr) == 0) {
			return $arr;
		}
		
		$one = array_pop($arr);
		
		return $this->getfull($one["id"]);
		
	}

	function getfull($id) {
		$prep = $this->db->prepare("select usernumber, title, subtitle, org_title, ISBN, concat(A.lastname, ', ', A.firstname) as author, A.id as author_id, " .
		"concat(CO.lastname, ', ', CO.firstname) as coauthor, CO.id as coauthor_id, concat(I.lastname, ', ', I.firstname) as illustrator, I.id as illustrator_id, concat(T.lastname, ', ', T.firstname) as translator, T.id as translator_id,  " .
		"concat(E.lastname, ', ', E.firstname) as editor, E.id as editor_id, PUB.name as publisher, PUB.id as publisher_id,price,published_year,written_year,C.name as category, C.id as category_id, " .
		"concat(PLA.placement, ' ',PLA.info) as placement, PLA.id as placement_id,edition,impression,S.name as series, S.id as series_id,number_in_series from " .
		AppConfig :: DB_PREFIX . "book B " .
		"left join (" . AppConfig :: DB_PREFIX . "placement PLA) on (PLA.id=placement_id) " .
		"left join (" . AppConfig :: DB_PREFIX . "person CO) on (CO.id = coauthor_id) " .
		"left join (" . AppConfig :: DB_PREFIX . "person A) on (A.id = author_id) " .
		"left join (" . AppConfig :: DB_PREFIX . "person E) on (E.id = editor_id) " .
		"left join (" . AppConfig :: DB_PREFIX . "person I) on (I.id = illustrator_id) " .
		"left join (" . AppConfig :: DB_PREFIX . "person T) on (T.id = translator_id) " .
		"left join (" . AppConfig :: DB_PREFIX . "publisher PUB) on (PUB.id = publisher_id) " .
		"left join (" . AppConfig :: DB_PREFIX . "category C) on (C.id = category_id) " .
		"left join (" . AppConfig :: DB_PREFIX . "serie S) on (S.id = series)" .
		"where B.id = ?");

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
	
	function delete($id) {
		$prep = $this->db->prepare("update " . AppConfig :: DB_PREFIX . "book set usernumber=null where id = ?");
		$prep->bind_params("i", $id);
		$prep->execute();
		
		return array("result"=>"1");
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
	
	function bookCount() {
		$prep = $this->db->prepare("select count(*) as c from " . AppConfig :: DB_PREFIX . "book where usernumber is not null");
		$res = $prep->execute();

		$data = array_pop($res);
		
		return $data["c"];
	}
}
?>
