<?php
require_once "SecurityTemplate.php";

class LogoutController extends SecurityTemplate
{
    public static function Logout()
    {
        $sec_result = self::SecurityCheck(array(null, null), null, null, null);
        
        //error_log($sec_result, 0);
        self::closeSession();

        if ($sec_result === true)
            header("Location: ../public/LoginForm.php"); //launch login form
        else
            header("Location: ../public/LoginForm.php?login=fail"); //launch login form w/ error msg
    }

    public static function closeSession()
    {
        session_destroy(); //clear all session variables
    }
}

?>