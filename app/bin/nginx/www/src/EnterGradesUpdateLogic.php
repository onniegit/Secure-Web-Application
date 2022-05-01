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
}