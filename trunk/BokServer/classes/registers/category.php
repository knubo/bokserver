<?php
class Category extends Base {

	function Category($dbi) {
		$this->db = $dbi;
		$this->table = "category";
	}

}

?>