<?php

function mustexit($killfile, $proc) {
    if (file_exists($killfile)) {
        file_put_contents('log.out', "\n\033[0;31mGot Kill: ".$proc."\033[0m", FILE_APPEND);
        return True;
    } else {
        return False;
    }
}



?>
