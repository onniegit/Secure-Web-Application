<?php
session_start();

try{
    require_once "../src/ForgotPwController.php";

    ForgotPwController::SecurityQuestion($_SESSION['email'], $_POST["Answer"]);
}

catch(Exception $e)
{
    //prepare page for content
    include_once "ErrorHeader.php";

    //Display error information
    echo 'Caught exception: ',  $e->getMessage(), "<br>";
}



