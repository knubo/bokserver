<?php
class Publisher extends Base {

	function Publisher($dbi) {
		$this->db = $dbi;
		$this->table = "publisher";
	}
}

?>