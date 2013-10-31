<?php


include('config.php');
include('lib_hitlistmulti.php');

$proc = $argv[1];
$config['muser'] = $argv[2];
$config['mpasswd'] = $argv[3];

//$killfile = 'kill';



function sessionfiler($config) {
    // Make a filename unique to the run_id like 1_cookie.txt
    $cookie = $config['sessionfiles']."/ses_".$config['muser'].".ses";

    // If the cookie file exists try to delete it or exit
    if (file_exists($cookie)) {
        echo "\nUnlinking ".$cookie;
        if (!unlink($cookie)) {
            echo "\nCould not delete cookie: ".$cookie;
            $this->makeExit();
        }
    } else {
        echo "No need to unlink cookie: ".$cookie;
    }
    return $cookie;
}

// login
function mlogin($config, $this_cookiefile, $proc) {
    
    $goodlogin = false;
    $loginas = array('username' => $config['muser'], 'password'=> $config['mpasswd']);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,              $config['base_url'].'/login/index.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 1 = send page output to curl_exec($ch);
    
    // Use SSL
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    //curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/var/www/html/assets/hitlistmultiuser/moodle-test.nottingham.ac.uk.crt");

    // Use Proxy
    //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
    //curl_setopt($ch, CURLOPT_PROXY, '128.243.253.109:8080');

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,1);
    curl_setopt($ch,CURLOPT_POST,            count($loginas));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $loginas);
    curl_setopt($ch, CURLOPT_COOKIEFILE, "$this_cookiefile"); 
    curl_setopt($ch, CURLOPT_COOKIEJAR, "$this_cookiefile");
    $result=curl_exec($ch);
    curl_close($ch);

    // Output the page contents?
    if ($config['renderpage']) {
        print_r($result);
    }
    
    if (strpos($result, $config['firstpageneedle'])) {
        //\033[" . $this->foreground_colors[$foreground_color] . "m"
        file_put_contents('log.out', "\n\033[1;33mProc: ".$proc." - Good Login: ".$config['muser']."\033[0m", FILE_APPEND);
        $goodlogin = true;
    }
    sleep(1);
    return $goodlogin;
}

function hitemRandom($config, $hitlist, $this_cookiefile, $proc, $killfile) {

    $hitlistcount = count($hitlist);
    echo "\nHistListCount: ".$hitlistcount;

    while (!mustexit($killfile, $proc)) {
        //echo "\nProc: ".$proc;
        file_put_contents('log.out', "\nProc: ".$proc." - hitemRandom", FILE_APPEND);
        
        $concs = 1;
        while ($concs <= $config['hit_concurrent']) {
            
            $this_set = $hitlist[array_rand($hitlist)];
            //echo "\nADDR[".$concs."]: ".key($this_set);
            file_put_contents('log.out', "\nProc: ".$proc." - ADDR[".$concs."]: ".key($this_set), FILE_APPEND);

            $ch{$concs} = curl_init();
            curl_setopt($ch{$concs}, CURLOPT_URL, key($this_set));

            curl_setopt($ch{$concs}, CURLOPT_RETURNTRANSFER, 1);
            
            // Use SSL
            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            //curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/var/www/html/assets/hitlistmultiuser/moodle-test.nottingham.ac.uk.crt");

            // Use Proxy
            //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            //curl_setopt($ch, CURLOPT_PROXY, '128.243.253.109:8080');

            curl_setopt($ch{$concs}, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($ch{$concs}, CURLOPT_HTTPHEADER,        array('Content-Type: text/plain'));
            curl_setopt($ch{$concs}, CURLOPT_COOKIEFILE,        "$this_cookiefile"); 
            curl_setopt($ch{$concs}, CURLOPT_COOKIEJAR,         "$this_cookiefile");

            $concs++;
        }
        
        $mh = curl_multi_init();

        $concs = 1;
        while ($concs <= $config['hit_concurrent']) {
            curl_multi_add_handle($mh, $ch{$concs});
            $concs++;
        }

        //execute the handles
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        //close the handles
        $concs = 1;
        while ($concs <= $config['hit_concurrent']) {
            curl_multi_remove_handle($mh, $ch{$concs});
            $concs++;
        }

        curl_multi_close($mh);
        sleep(1);
    } //while no kill
}

function hitemSeq($config, $hitlist, $this_cookiefile, $proc, $killfile) {
    // print_r($hitlist);
    echo "\nEntered Seq";

    while (True) {
        //echo "\nIter: ".$iter;
        foreach ($hitlist as $label => $testset) {
            if (!mustexit($killfile, $proc)) {
                foreach ($testset as $addr => $needle) {
                    $curloutput = 0;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,              $addr);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    
                    // Use SSL
                    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
                    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    //curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/var/www/html/assets/hitlistmultiuser/moodle-test.nottingham.ac.uk.crt");

                    // Use Proxy
                    //curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
                    //curl_setopt($ch, CURLOPT_PROXY, '128.243.253.109:8080');
                    
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                    curl_setopt($ch, CURLOPT_HTTPHEADER,        array('Content-Type: text/plain'));
                    curl_setopt($ch, CURLOPT_COOKIEFILE,        "$this_cookiefile"); 
                    curl_setopt($ch, CURLOPT_COOKIEJAR,         "$this_cookiefile");
                    $curloutput=curl_exec ($ch);
                    curl_close($ch);
                    $wait = mt_rand($config['hit_delay_lower'], $config['hit_delay_upper']).".".mt_rand(0, 9);
                    if (strpos($curloutput, $needle)) {
                        echo "found";
                        //\033[0;31m
                        //"\033[0m"
                        //black = 40
                        file_put_contents('log.out', "\n------------------------------------------\n\033[0;30;42mProc:\033[0m \t\t".$proc."\n\033[0;30;42muser:\033[0m \t\t".$config['muser']."\n\033[0;30;42mlabel:\033[0m \t\t".$label."\n\033[0;30;42maddr:\033[0m \t\t".$addr."\n\033[0;30;42mneedle:\033[0m \t".$needle.". . . \033[0;30;42mFOUND\033[0m\n\033[0;30;42mwait:\033[0m \t\t".$wait, FILE_APPEND);
                    } else {
                        echo "NO";
                        file_put_contents('log.out', "\n------------------------------------------\n\033[0;30;42mProc:\033[0m \t\t".$proc."\n\033[0;30;42muser:\033[0m \t\t".$config['muser']."\n\033[0;30;42mlabel:\033[0m \t\t".$label."\n\033[0;30;42maddr:\033[0m \t\t".$addr."\n\033[0;30;42mneedle:\033[0m \t".$needle.". . . \033[0;30;41mNO\033[0m\n\033[0;30;42mwait:\033[0m \t\t".$wait, FILE_APPEND);
                    }
                    //sleep($config['hit_delay']);
                    sleep($wait);
                } // for each test set
            } else { // second deep test for kill
                makeExit($this_cookiefile);
            }
        } // for each hitlist
    } //while no kill
}

function doLogin($config, $this_cookiefile, $proc) {
    echo "\nStart Login...";
    if (mlogin($config, $this_cookiefile, $proc)) {
        echo "success";
        return true;
    } else {
        echo "fail";
        return false;
    }
}

function makeExit($this_cookiefile) {
    echo "\nExit\n\n";
    if (!unlink($this_cookiefile)) {
       echo "\nCould not delete cookie: ".$this_cookiefile." on exit";
    }
    exit(0);
}

$this_cookiefile = sessionfiler($config);

if (doLogin($config, $this_cookiefile, $proc)) {
    //hitemRandom($config, $hitlist, $this_cookiefile, $proc, $config['killfile']);
    hitemSeq($config, $hitlist, $this_cookiefile, $proc, $config['killfile']);
} else {
    file_put_contents('log.out', "\nProc: ".$proc." - login fail", FILE_APPEND);
    makeExit($this_cookiefile);
}

makeExit($this_cookiefile);

?>