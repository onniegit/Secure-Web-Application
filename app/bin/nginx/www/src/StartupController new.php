<?php

class StartupController
{
    public static function clearUploads()
    {
        array_map('unlink', glob("uploads/*"));
    }

    public static function loadConfig()
    {
        error_log("init db",0);
        //initialize the database
        shell_exec('php bin/nginx/www/config/Config.php');
        error_log("done init",0);
    }

    public static function start()
    {
        //DBConnector::clearDB();        
        StartupController::clearUploads();        
        StartupController::loadConfig();        
        //load login form        
        header("Location: ../public/LoginForm.php");
    }
}


?>
