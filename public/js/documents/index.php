<?php 

if ($fh = fopen('..\..\..\.env', 'r')) {
    while (!feof($fh)) {
        $line = fgets($fh);
        echo $line . "\n";
    }
    fclose($fh);
}

