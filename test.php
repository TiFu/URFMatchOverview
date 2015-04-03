<?php
set_time_limit(5);
$start = time();

for ($i = 0; $i < 6; ++$i) {
   echo "Ahmed";
   time_sleep_until($start + $i + 1);
}
?>