<?php
class Bibsys {
	function getBookInfo($isbn) {
		$infoUrl = $this->getInfoUrl($isbn);

		if(!$infoUrl) {
			return array("failed"=>"1");
		}

		return $this->getBookData($infoUrl);
	}

	function getBookData($infoURL) {
		$result = array ();

		$url = "http://ask.bibsys.no/ask/action/$infoURL&visningsformat=RIS";
		$klienturl = "http://ask.bibsys.no/ask/action/$infoURL&visningsformat=default";
		$result["url"] = $klienturl;

		$data = array ();
		$cmd = AppConfig::WGET." -O- '$url'";
		$res = exec($cmd, & $data);

		$regs = array ();
		ereg("<pre>(.*)</pre>", implode($data, '###'), & $regs);

		$result["info"] = $this->splitIntoFields($regs[1]);
		return $result;
	}

	function splitIntoFields($one) {
		$info = explode("###", $one);
		$lookup = array (
			"T1  - " => "title",
			"A1  - " => "author",
			"Y1  - " => "written_year",
			"N1  - " => "note",
			"N2  - " => "note",
			"N3  - " => "note",
			"KW  - " => "note",
			"N4  - " => "note",
			"N5  - " => "note",
			"SP  - " => "note",
			"PB  - " => "publisher"
		);

		$result = array ();

		foreach ($info as $line) {
			if(strlen($line) == 0) {
				continue;
			}
			$k = substr($line, 0, 6);
			if (!array_key_exists($k, $lookup)) {
				continue;
			}
			
			if($k == "Y1  - ") {
				$regs = array();
				ereg('.*([0-9][0-9][0-9][0-9]).*', $line,& $regs);
				$result[$lookup[$k]] = $regs[1];
			} else {
				if(array_key_exists($lookup[$k], $result)) {
					$result[$lookup[$k]] .= "<br>".substr($line, 6);					
				} else {
					$result[$lookup[$k]] = substr($line, 6);					
				}
			}
		}

		return $result;
	}

	function getInfoUrl($isbn) {
		$data = array ();
		$cmd = AppConfig::WGET." -O- 'http://ask.bibsys.no/ask/action/result?fid=isbn&term=$isbn'";
		$res = exec($cmd, & $data);

		$regs = array ();
		foreach ($data as $one) {

			if (ereg('href="(show?.*)" class', $one, & $regs) > 0) {
				return $regs[1];
			}
		}
		return false;
	}
}
?>
