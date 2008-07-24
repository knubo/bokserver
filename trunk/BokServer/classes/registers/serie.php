<?php
class Serie extends Base {

	function Serie($dbi) {
		$this->db = $dbi;
		$this->table = "serie";
	}
}

?>