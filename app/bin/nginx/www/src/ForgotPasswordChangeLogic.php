<?php
session_start();

try {
    require_once "../src/ForgotPwController.php";

    ForgotPwController::UpdatePassword($_SESSION['email'], $_POST["newpassword"], $_POST["confirmpassword"]);
}

catch(Exception $e)
{
    //prepare page for content
    include_once "ErrorHeader.php";

    //Display error information
    echo 'Caught exception: ',  $e->getMessage(), "<br>";
}
