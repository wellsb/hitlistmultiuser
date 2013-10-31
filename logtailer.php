<?php

// - Config - - - - - - - -

// moodle22-2
//    'dbhost' => 'localhost',
//    'dbuser' => 'moodletwotwouser',
//    'dbpass' => 'moodletwotwodashtwo',
//    'dbdata' => 'moodle22-2');

// DB connection config
$config = array(
    'dbhost' => 'localhost',
    'dbuser' => 'moodle241user',
    'dbpass' => '@ndyC@p$',
    'dbdata' => 'moodle241');
date_default_timezone_set('Europe/London');
// - Config - - - - - - - -

function doConnect($config) {
    global $connect1;
    $connect1 = mysql_connect($config['dbhost'], $config['dbuser'], $config['dbpass']);
    if (!$connect1) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db($config['dbdata'], $connect1);
}

function doDisConnect() {
    GLOBAL $connect1;

    if (!$connect1) {
        die('Nothing to close: ' . mysql_error());
    } else {
        mysql_close($connect1);
    }
}

function tailLog() {
    GLOBAL $connect1;

    $query = stripcslashes(mysql_real_escape_string("
        SELECT * FROM (
            SELECT
            mdl_log.id,
            mdl_log.time,
            mdl_log.ip,
            mdl_log.module,
            mdl_log.url,
            mdl_log.info
            FROM mdl_log
            ORDER BY mdl_log.id DESC
            LIMIT 51
        ) AS thelog
    ORDER BY thelog.id;"));
    $returned = mysql_query($query, $connect1) or die(mysql_error());
    $first = true;
    while($readrow=mysql_fetch_array($returned, MYSQL_NUM)) {
        //echo "\n".$readrow[0]." - ".date('Y/m/d', $readrow[1])." - ".$readrow[2]." - ".$readrow[3]." - ".$readrow[4]." - ".$readrow[5];
        if ($first) {
            echo "\nid\ttime\t\tip\t\tmodule\turl\t\t\tinfo";
            $first= false;
        }
        echo "\n".$readrow[0]."\t".date('Y/m/d-G:i', $readrow[1])."\t".$readrow[2]."\t".$readrow[3]."\t".$readrow[4]."\t\t\t".$readrow[5];
    }
}


function makeExit() {
    echo "\nExit\n\n";
    exit(0);
}

doConnect($config);

while (true) {
    tailLog();
    sleep(3);
}



doDisConnect();
makeExit();
?>
