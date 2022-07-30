<?php
require_once "RequestController.php";
require_once "SessionController.php";

class LogoutController extends RequestController
{
    static function Logout()
    {
        if (SessionController::authenticateSession()) {
            SessionController::closeSession();
        }

        //launch login form
        header("Location: ../public/LoginForm.php");    }
}

?>