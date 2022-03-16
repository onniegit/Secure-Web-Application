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
    var_dump($e->getTraceAsString());
    echo 'in '.'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']."<br>";

    $allVars = get_defined_vars();
    debug_zval_dump($allVars);
}
