<?php
class Bokkilden {
	function getBookInfo($isbn) {
		return $this->getBookData($isbn);
	}

	function getBookData($isbn) {
		$result = array ();

		$cmd = AppConfig :: WGET . " -O- 'http://www.bokkilden.no/SamboWeb/avansertSok.do?isbn=$isbn&mp=rom'";
		$klienturl = "http://www.bokkilden.no/SamboWeb/avansertSok.do?isbn=$isbn&mp=rom";
		$result["url"] = $klienturl;

		$data = array ();
		$res = exec($cmd, & $data);

		$regs = array ();
		$allRaw = utf8_decode(implode($data, '###'));
		preg_match("/class=\"bokfaktatabell\">(.*?)<\/table>/", $allRaw, & $regs);
		$info = $this->splitIntoFields($regs[1]);

		preg_match("/ProduktHovedelement.*?>(.*?)<div/", $allRaw, &$regs);

		$this->addTitleAuthor(&$info, &$regs);

		$result["info"] = $info;
		return $result;
	}
	
	function addTitleAuthor($info, $regs) {
		$info["title"] = "FOO";
	}

	function splitIntoFields($one) {
		$one = str_replace(array (
			"<tr>",
			"</tr>",
			"<b>",
			"</b>",
			"</br>"
		), "", $one);

		$matches = array ();
		preg_match_all("/<td.*?>(.*?)<\/td>/", $one, & $matches);

		$lookup = array (
			"Utgitt:" => "published_year",
			"Utgave:" => "edition",
			"ISBN:" => "isbn",
			"Alder:" => "note",
			"Genre:" => "genre",
			"Innb.:" => "note",
			"Sider:" => "note",
			"Forlag:" => "publisher"
		);

		$result = array ();

		$k = null;
		$orgKey = null;
		foreach ($matches[1] as $field) {
			$field = trim($field);

			$orgtittel = array ();
			if (preg_match("/Originaltittel:(.*)/", $field, & $orgtittel)) {
				$result["org_title"] = trim($orgtittel[1]);
				continue;
			}

			if (preg_match("/Spr.*:/", $field, & $orgtittel)) {
				$k = "note";
				$orgKey = "Spr&aring;k:";
				continue;
			} else if (array_key_exists($field, $lookup)) {
				$k = $lookup[$field];
				$orgKey = $field;
				continue;
			}
			
			if ($k == "note") {
				$field = "$orgKey $field";
			}

			if (array_key_exists($k, $result)) {
				$result[$k] .= "<br>" . $field;
			} else {
				$result[$k] = $field;
			}
		}

		return $result;
	}

}
?>
