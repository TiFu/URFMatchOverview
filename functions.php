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

?>