<?php

$GLOBALS['dbPath'] = 'bin/nginx/www/db/persistentconndb.sqlite';

if(file_exists($GLOBALS['dbPath'])) {
    unlink($GLOBALS['dbPath']);
}

array_map('unlink', glob("uploads/*"));

shell_exec('php bin/nginx/www/config/Config.php');