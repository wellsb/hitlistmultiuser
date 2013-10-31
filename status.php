<?php
// yellow - \033[1;33m text
// green back - \033[0;30;42m
// off - \033[0m]

$main_bg = '';
$main_fg = '';

$bar_fg = '35';
$bar_bg = '47';

function getScreenSize() { 
      preg_match_all("/rows.([0-9]+);.columns.([0-9]+);/", strtolower(exec('stty -a |grep columns')), $output);
      if(sizeof($output) == 3) {
        $screen['height'] = $output[1][0];
        $screen['width']= $output[2][0];
      }
      return $screen;
}

function Gotwo($Col,$Row) {
    echo "\033[".$Row.";".$Col."H";
}

function setBack($screen, $color) {
    for ($i = 1; $i <= $screen['height'] - 1; $i++) {
        ybar($screen, $color, $i);
    }
    echo "\033[0m";
}

function ybar($screen, $color, $row){
    echo "\033[0;30;".$color."m";
    Gotwo('1', $row);
    for ($i = 1; $i <= $screen['width']; $i++) {
        echo " ";
    }
    echo "\033[0m";
}

function echoCol($fg, $bg, $text) {
    echo "\033[0;".$fg.";".$bg."m".$text;
    echo "\033[0m";
}

$screen = getScreenSize();

// Draw backgrounf color
setBack($screen, '44');

// Draw top bar
ybar($screen, '47', '1');

// Draw bottom bar
ybar($screen, $bar_bg, $screen['height'] - 1);

Gotwo('2', '1');
echoCol('30', '47', 'Program Title');

// muck about
Gotwo('10', '10');
echoCol('37', '40', 'something');

Gotwo('10', '12');
echoCol('37', '41', 'something');

Gotwo('10', '14');
echoCol('37', '42', 'something');

Gotwo('10', '16');
echoCol('37', '43', 'something');

Gotwo('10', '18');
echoCol('37', '44', 'something');

Gotwo('10', '20');
echoCol('37', '45', 'something');

Gotwo('10', '22');
echoCol('37', '46', 'something');

Gotwo('10', '24');
echoCol('37', '27', 'something');

// Put prompt to bottom of screen
Gotwo('1', $screen['height'] -1 );
echo "\n";
?>
