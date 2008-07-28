<?php
class Bibsys {
	function getBookInfo($isbn) {
		$infoUrl = $this->getInfoUrl($isbn);

		return $this->getBookData($infoUrl);
	}

	function getBookData($infoURL) {
		$result = array ();

		$url = "http://ask.bibsys.no/ask/action/$infoURL&visningsformat=RIS";
		$klienturl = "http://ask.bibsys.no/ask/action/$infoURL&visningsformat=default";
		$result["url"] = $klienturl;

		$data = array ();
		$cmd = "/usr/local/bin/wget -O- '$url'";
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
			"N1  - " => "note_1",
			"N2  - " => "note_2",
			"PB  - " => "publisher"
		);

		$result = array ();

		foreach ($info as $line) {
			$k = substr($line, 0, 6);
			if (!array_key_exists($k, $lookup)) {
				continue;
			}
			$result[$lookup[$k]] = substr($line, 6);
		}

		return $result;
	}

	function getInfoUrl($isbn) {
		$data = array ();
		$cmd = "/usr/local/bin/wget -O- 'http://ask.bibsys.no/ask/action/result?fid=isbn&term=$isbn'";
		$res = exec($cmd, & $data);

		$regs = array ();
		foreach ($data as $one) {

			if (ereg('href="(show?.*)" class', $one, & $regs) > 0) {
				return $regs[1];
			}
		}

	}
}
?>
