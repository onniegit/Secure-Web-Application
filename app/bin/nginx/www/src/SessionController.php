<?php
require_once "User.php";
require_once "../src/DBConnector.php";

class SessionController
{
    static function CreateSession($uname, $acctype)
    {
        if (isset($_SESSION)) {
            //a session already existed
            session_destroy();
            session_start();
            $_SESSION['email'] = $uname;
            $_SESSION['acctype'] = $acctype;
        }

        else {
            //a session did not exist
            session_start();
            $_SESSION['email'] = $uname;
            $_SESSION['acctype'] = $acctype;
        }
    }
    function ValidateSession()
    {
        return (isset($_SESSION));
    }
    function ValidateLogin()
    {
        session_start();
        if (isset($_SESSION['acctype'])) {
            return true;
        }
        else {
            return false;
        }
    }
    static function GetType()
    {
        session_start();
        if (isset($_SESSION['acctype'])) {
            return $_SESSION['acctype'];
        }
        else {
            return -1;
        }
    }
    function isSetSessionEmail()
    {
        session_start(); //required to bring session variables into context

        if (!isset($_SESSION['email']) or (empty($_SESSION['email']))) //check that session exists and is nonempty
        {
            return false;
        }
        else {
            return true;
        }
    }
    function GetEmail()
    {
        session_start(); //required to bring session variables into context

        if (!isset($_SESSION['email']) or (empty($_SESSION['email']))) //check that session exists and is nonempty
        {
            return "empty";
        }
        else {
            return $_SESSION['email'];
        }
    }

    function HasRights($acctype)
    {
        //session_start();
        //Check if PHP session has already started
        if(session_id() == ''){
            session_start(); //resume session
         }

        if (isset($_SESSION['acctype']) && $_SESSION['acctype'] == $acctype) {
            return true;
        }
        else {
            return false;
        }
    }

    static function authenticateSession() //checks that session is valid
    {
        //session_start();
        //Check if PHP session has already started
        if(session_id() == ''){
            session_start(); //resume session
         }

        //check if session variables are set
        if (isset($_SESSION) AND isset($_SESSION['acctype'])) {
            //a better session check which uses the DB is needed
            //a session exists
            return true;
        }
        else {
            return false;
        }
    }

    static function authorize($un, $res)
    {
        if (DBConnector::CheckRights($un, $res)) {
            return true;
        }
        else {
            return false;
        }
    }
    static function closeSession()
    {
        session_destroy(); //clear all session variables
    }
}

?>