<?php

$db = new mysqli("localhost", "root", "", "challenge");

if ($db->connect_errno > 0) {
    die('Unable to connect to database [' . $db->connect_error . ']');
}
$championnum = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 48, 50, 51, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64,
    67, 68, 69, 72, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 89, 90, 91, 92, 96, 98, 99, 101, 102, 103, 104, 105, 106, 107, 110, 111, 112, 113, 114, 115, 117, 119, 120, 121, 122, 126, 127, 131, 133, 134, 143, 150, 222, 154, 157, 161, 201, 201, 236, 238, 254, 266, 267, 268, 412, 421, 429, 432
);
for ($c = 0; $c < count($championnum); $c++) {
    $result = $db->query("SELECT * FROM `euw` WHERE 'id' = $championnum[$c] ORDER BY `numgames` DESC");
    while($row = $result->fetch_assoc()){
        
        
    }

}