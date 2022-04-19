<?php
require_once "../src/EnterGradeControl.php";

session_start(); // required to bring session variables into context

try {
    if (isset($_POST['submit'])) // passes the session and crn to enter grade control for processing
    {
        EnterGradeControl::EnterGrade(isset($_SESSION['email']), $_POST['crn']);
    }
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