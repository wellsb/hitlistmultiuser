<?php

$userdbfile = 'usedmem.out';

function getScreenSize() { 
      preg_match_all("/rows.([0-9]+);.columns.([0-9]+);/", strtolower(exec('stty -a |grep columns')), $output);
      if(sizeof($output) == 3) {
//        $screen['width'] = $output[1][0];
//        $screen['height']= $output[2][0];
        
        //swapped
        $screen['height'] = $output[1][0];
        $screen['width']= $output[2][0];
      }
      return $screen;
}

function readtoarray($userdbfile) {
    if (isset($read)) {
        unset($read);
    }
    $read = array();
    ini_set('auto_detect_line_endings', TRUE);
    $handle = fopen($userdbfile,'r');
    while (($this_row = fgetcsv($handle) ) !== FALSE ) {
        array_push($read, $this_row);
    }
    return $read;
}

function scaleit($a, $new_min, $new_max) {
    $min = min($a);
    $max = max($a);
    foreach ($a as $i => $v) {
        $a[$i] = round(((($new_max - $new_min) * ($v - $min)) / ($max - $min)) + $new_min);
    }
    return $a;
}

function clearscreen($out = TRUE) {
    $clearscreen = chr(27)."[H".chr(27)."[2J";
    if ($out) print $clearscreen;
    else return $clearscreen;
}

function Gotwo($Col,$Row) {
    echo "\033[".$Row.";".$Col."H";
}

// Get screen size
$screen = getScreenSize();

// Read the file into memory
$read = readtoarray($userdbfile);

// As many last entries that fit on the screen
$lastn = array_slice($read, -$screen['width'], $screen['width']);

// Strip just mem values from input data into a new array
$justmem = array();
foreach ($lastn as $this_mem) {
//    array_unshift($justmem, $this_mem[1]);
    array_push($justmem, $this_mem[1]);
}

// Strip date values from input data into a new array
$justdates = array();
foreach ($lastn as $this_date) {
//    array_unshift($justmem, $this_mem[1]);
    array_push($justdates, $this_date[0]);
}

// Scale the highest and lowest values to the screen height
$scaled = scaleit($justmem, '1', $screen['height']);

clearscreen();

$mycol = 1;
$dateselect = 0;
foreach ($scaled as $this_scale) {
    if ($dateselect <= count($justdates)) {
        $this_date = array_slice(str_split($justdates[$dateselect]), -6);
    } else {
        $this_date = array(0,0,0,0,0,0);
    }
    
    $lat = $screen['height']-1;
    $datecharpos = 1;
    while ($lat > $screen['height'] - $this_scale) {

        Gotwo($mycol, $lat);
        
        if ($datecharpos < 6) {
            echo $this_date[$datecharpos];
            $datecharpos++;
        } else {
            echo "\033[1;43"."m "."\033[0m";
        }
        //echo "\033[1;43"."m "."\033[0m";
        usleep(200);
        $lat--;
    }
    $mycol++;
    $dateselect++;
    //$mycol = $mycol +1;
}

echo "\nW: ".$screen['width'];
echo "\nH: ".$screen['height'];
Gotwo('1', $screen['height']);

?>