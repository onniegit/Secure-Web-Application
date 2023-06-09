<?php
try 
{
    require_once "../src/LoginController.php";
    require_once "../src/Constants.php";

    //create array for input
    $credentials = array($_POST['username'], $_POST['password']);
    LoginController::Login($credentials);
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




