<?php
// function to store the file on data foler 
function storeUrlToFilesystem($url, $localFile) {
    try {
        $data = file_get_contents($url);
		if ($data === FALSE) { // check if HTTP request was successfull
			echo "Request failed<br>";
			return false;
		}
        $handle = fopen($localFile, "w");
        fwrite($handle, $data);
        fclose($handle);
        return true;
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }
    return false;
}

function getRandomMatchId() {
	$dirscan = scandir(MATCH_PATH);
	$files = array();
	foreach ($dirscan as $file) {
		if (endsWith($file, ".json")) {
			$files[] = $file;
		}
	}
	$idFileCount = count($files);
	$file = mt_rand(0, $idFileCount - 1); // file selected
	return substr($files[$file],0, -5);
}
function transformTypeToText($type) {
	$arr = explode("_", $type);
	$val = "";
	foreach ($arr as $a) {
		$val .= ucfirst(strtolower($a)) ." ";
	}
	return rtrim($val);
}

function transformColumnNameToText($text) {
	$pieces = preg_split('/(?=[A-Z])/',$text);
	$ret = "";
	foreach ($pieces as $piece) {
		$ret .= ucfirst($piece) ." ";
	}
	return rtrim($ret);
}

						function tableCell($rowKey, $value) {
							if ($rowKey == "champion") {
								return '<span><img data-uk-tooltip title="' .$value .'" style="border-radius:50%;" width="24" height="24" src="images/champion/' .str_replace(" ", "%20", $value) .'46.png" alt=""></span>';
							} else if (strpos(strtolower($rowKey), "rate") !== false) {
								return round($value * 100,0) ."%";
							} else if ($rowKey == "kda") {
								return number_format($value, 1, ",", ".");
							} else {
								return number_format($value, 0, ",", ".");
							}
							
							return $value;
						}

function regionToServer($region) {
	if ($region == "BR") {
		return "Brazil";
	}

	if ($region == "EUNE") {
		return "EU Nordic & East";
	}
	
	if ($region == "EUW") {
		return "EU West";
	}

	if ($region == "LAN") {
		return "Latin America North";
	}

	if ($region == "LAS") {
		return "Latin America South";
	}

	if ($region == "KR") {
		return "Korea";
	}

	if ($region == "OCE") {
		return "Oceania";
	}
	
	if ($region == "RU") {
		return "Russia";
	}

	if ($region == "TR") {
		return "Turkey";
	}
	if ($region == "NA") {
		return "North America";
	}

}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}
?>