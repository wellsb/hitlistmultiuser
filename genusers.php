<?php
error_reporting(E_ALL);	//E_STRICT for the mad man
$userdbfile = 'users.out';
$userPrefix = 'u';
$passPrefix = 'p';
$sepPrefix = '00';


// - Config - - - - - - - -
$config_host = 'localhost';
$config_user = 'something';
$config_pass = 'something';
$config_data = 'something';
date_default_timezone_set('UTC');
// - Config - - - - - - - -

function doConnect() {
    GLOBAL $connect1, $config_host, $config_user, $config_pass, $config_data;
    echo "\nConnect...";
    $connect1 = mysql_connect($config_host, $config_user, $config_pass);
    if (!$connect1) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db($config_data, $connect1);
}

function genRandom ($length, $type, $sepPrefix) {
    $ranfn = "";
    $possible = "123456789abcdfghjkmnpqrtvwxyzABCDFGHJKLMNPQRTVWXYZ";
    //$possible = "123";
    $maxlength = strlen($possible);

    $i = 0; 
    while ($i < $length) {
        $char = substr($possible, mt_rand(1, $maxlength -1), 1);
        $ranfn .= $char;
        $i++;
    }
    $gen = $type.$sepPrefix.$ranfn;
    return $gen;
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

function Exists($needle, $readin) {
    foreach ($readin as $read_row) {
        if (in_array($needle, $read_row)) {
            return True;
        }
    }
    return False;
}

$usercount = 5;

doConnect();

    echo "\n";
    for ($i = 1; $i <= $usercount; $i++) {
        echo "\n- - - - -\niter: ".$i;
        if (isset($this_user)) {
            unset($this_user);
        }

        if (isset($this_password)) {
            unset($this_password);
        }

        //gen
        $this_user = genRandom ('12', $userPrefix, $sepPrefix);
        $this_password = genRandom ('12', $passPrefix, $sepPrefix);

        // read current state of file
        if (isset($read)) {
            unset($read);
        }
        $read = readtoarray($userdbfile);
        echo "\nCount: ".count($read);

        //check if genned user or password already exists, if not add to the file
        if ((!Exists($this_user, $read)) and (!Exists($this_password, $read))) {
            echo "\nusername: ".$this_user;
            $this_password_before = $this_password;
            echo "\npass before: ".$this_password;
            $this_password = md5($this_password);
            echo "\npass after: ".$this_password;
            $this_email = $this_user."@embertest.co.uk";
            echo "\nemail: ".$this_email;
            $this_time = time();
            file_put_contents($userdbfile, $this_user.",".$this_password_before."\n", FILE_APPEND);
            //moodle 22.2
            $result = mysql_query("INSERT INTO mdl_user VALUES ('', 'manual', '1', '0', '0', '0', '1', '$this_user', '$this_password', '$this_user', '$this_user', '$this_password_before', '$this_email', '0', '', '', '', '', '', '', '', '', '', '', '', 'GB', 'en', '', '99', '0', '0', '0', '0', '', '', '0', '', 'embertest', '0', '1', '0', '2', '1', '1', '0', '0', '$this_time', '$this_time', '0', '', '0')");

            //moodle 24.2
            //$result = mysql_query("INSERT INTO mdl_user VALUES ('', 'manual', '1', '0', '0', '0', '1', '$this_user', '$this_password', '$this_user', '$this_user', '$this_password_before', '$this_email', '0', '', '', '', '', '', '', '', '', '', '', '', 'GB', 'en', '', '99', '0', '0', '0', '0', '', '', '0', '', 'embertest', '0', '1', '0', '2', '1', '1', '0', '$this_time', '$this_time', '0', '')");

         if ($result) {
            echo "\nMySQL Good";
        } else {
            echo "\nMySQL Bad";
        }
        } else {
            echo "\nBROKE ON: ".$this_user." or ".$this_password;
            break;
        }
}

echo "\n";
?>
