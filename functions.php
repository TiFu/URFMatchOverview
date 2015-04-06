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
	$dirscan = scandir(ID_PATH);
	$files = array();
	foreach ($dirscan as $file) {
		if (endsWith($file, ".txt")) {
			$files[] = $file;
		}
	}
	$idFileCount = count($files);
	$file = mt_rand(0, $idFileCount - 1); // file selected

	// Number of lines:
	$fileLines = count(file(ID_PATH .$files[$file]));
	$lineNumber = mt_rand(0, $fileLines - 1); // Random number
	$file = new SplFileObject(ID_PATH .$files[$file]);
	$file->seek($lineNumber);
		
	return $file->current();
}
function transformTypeToText($type) {
	$arr = explode("_", $type);
	$val = "";
	foreach ($arr as $a) {
		$val .= ucfirst(strtolower($a)) ." ";
	}
	return rtrim($val);
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