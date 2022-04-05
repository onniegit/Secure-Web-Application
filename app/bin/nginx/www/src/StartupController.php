<?php

clearDB();

clearUploads();

loadConfig();

function clearDB() 
{
    $GLOBALS['dbPath'] = 'bin/nginx/www/db/persistentconndb.sqlite';

    if(file_exists($GLOBALS['dbPath'])) {
        unlink($GLOBALS['dbPath']);
    }
}

function clearUploads() 
{
    array_map('unlink', glob("uploads/*"));
}

function loadConfig()
{
    shell_exec('php bin/nginx/www/config/Config.php');
}
