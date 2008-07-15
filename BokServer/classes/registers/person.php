<?php
class Person {
	private $db;

	function Person($dbi) {
		$this->db = $dbi;
	}

	function search($type, $query, $limit) {
		$query = implode("%", explode(" ", $query))."%";

		$prep = $this->db->prepare("select * from " . AppConfig :: DB_PREFIX . "person where " .
		"(search1 like ? or " .
		" search2 like ?) and " .
		"illustrator=? and translator=? and author=? and editor=? limit ?");

		$illustrator = (strpos($type, "I") === FALSE) ? 0 : 1;
		$author = (strpos($type, "A") === FALSE) ? 0 : 1;
		$translator = (strpos($type, "T") === FALSE) ? 0 : 1;
		$editor = (strpos($type, "E") === FALSE) ? 0 : 1;


		$prep->bind_params("ssiiiii", $query, $query, $illustrator, $translator, $author, $editor, $limit);


		return $prep->execute();
	}

}
?>
