<?php
//resume the session
session_start();

//store username in session array
$_SESSION['email'] = $_POST['email'];

try 
{
    require_once "../src/ForgotPwController.php";
    $data = array(strtolower($_POST['email']));

    ForgotPwController::ForgotPassword($data, Constants::$USERNAME_TYPE);
}
catch(Exception $e)
{
    //prepare page for content
    include_once "ErrorHeader.php";

    //Display error information
    echo 'Caught exception: ',  $e->getMessage(), "<br>";
    var_dump($e->getTraceAsString());
    echo 'in '.'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']."<br>";

    $allVars = get_defined_vars();
    debug_zval_dump($allVars);
}







