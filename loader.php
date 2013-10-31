<?php

error_reporting(E_ALL);	//E_STRICT for the mad man
include('config.php');
include('lib_hitlistmulti.php');

$userdbfile = 'users.out';

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

$users = readtoarray($userdbfile);

echo "\nRamp: ".$config['ramp'];

for ($i = 1; $i <= $config['users']; $i++) {
    if (!mustexit($config['killfile'], $i)) {
        if (isset($this_user)) {
            unset ($this_user);
        }
        if (isset($this_pass)) {
            unset ($this_pass);
        }
        $this_user = $users[$i][0];
        $this_pass = $users[$i][1];

        echo "\n-----\nProc: ".$i."\nUser: ".$this_user."\nPass: ".$this_pass;
        exec("php hitlist.php $i $this_user $this_pass&> /dev/null &");
        echo "\n";
        usleep($config['ramp']);
    }
}

?>
