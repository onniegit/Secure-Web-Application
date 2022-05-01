<?php
//resume the session
session_start();

//store username in session array
$_SESSION['email'] = $_POST['email'];

try 
{
    require_once "../src/ForgotPwController.php";

    ForgotPwController::ForgotPassword(strtolower($_POST['email']));
}

catch(Exception $e)
{
    //prepare page for content
    include_once "ErrorHeader.php";

    //Display error information
    echo 'Caught exception: ',  $e->getMessage(), "<br>";
}







