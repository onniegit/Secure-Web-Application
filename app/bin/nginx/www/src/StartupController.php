<?php

class StartupController
{

public static function clearDB() 
{
    $GLOBALS['dbPath'] = 'bin/nginx/db/persistentconndb.sqlite';

    if(file_exists($GLOBALS['dbPath'])) {
        unlink($GLOBALS['dbPath']);
    }
}

public static function clearUploads() 
{
    array_map('unlink', glob("uploads/*"));
}

public static function loadConfig()
{
    shell_exec('php bin/nginx/www/config/Config.php');
}

public static function start()
{
    StartupController::clearDB();
    StartupController::clearUploads();
    StartupController::loadConfig();
    //launch loginForm
    //currently launched from the batch file
}

}

StartupController::start();

?>