<?php
date_default_timezone_set('Europe/London');

$this_second = 1;
$minute_values = array();
while ($this_second < 61) {
    //echo "\nUsed: ".trim(substr(shell_exec('free -m'), 99, 10))."\n";
    $this_sample = trim(substr(shell_exec('free -m'), 99, 10));
    $minute_values[] = $this_sample;
    echo "\nSec: ".$this_second;
    echo "\nArrayCount: ".count($minute_values);
    echo "\nThis sample: ".$this_sample;
    echo "\nMinAvrg: ".round(array_sum($minute_values) / count($minute_values), 0);
    echo "\n---------";

    if ($this_second == 60) {
        file_put_contents('usedmem.out', date('y/m/d-H:i').",".round(array_sum($minute_values) / count($minute_values), 0)."\n", FILE_APPEND);
        $this_second = 1;
        unset ($minute_values);
        $minute_values = array();
    } else {
        $this_second++;
    }
    usleep(1000000);
}

?>
