<?php
try {
    /*Get DB connection*/
    require_once "../src/DBController.php";

    
    function DisplayError($errorCode)
    {
        if($errorCode==1)
        {
            echo "Username or password is incorrect, please try again.";
        }
    }

    //consolidated method to validate username and password
    //password must be between 8 and 20 characters
    //and must contain at least one upper case, lower case,
    //number, and symbol !@#$%^&*()
    function ValidateInput($un,$pw)
    {
        $passwordFormat = "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*()]).{8,20}/";

        if(filter_var($un,FILTER_VALIDATE_EMAIL)==true AND preg_match($passwordFormat,$pw)==true)
        {
            return true;
        }

        else return false;
    }

    if(ValidateInput($_POST['username'],$_POST['password']))
    {
        $myusername = htmlentities($_POST['username']);
        $mypassword = htmlentities($_POST['password']);
    }

    if($mypassword==null||$myusername==null)
    {
        $errorCode = 1;
        DisplayError($errorCode);
    }

    global $acctype;
    
    if (ValidateUser($myusername,$mypassword)) // user found
    {
        $error = false;

        if (isset($_SESSION)) 
        {
            //a session already existed
            session_destroy();
            session_start();
            $_SESSION['email'] = $myusername;
            $_SESSION['acctype'] = $acctype;
        } 
        else 
        {
            //a session did not exist
            session_start();
            $_SESSION['email'] = $myusername;
            $_SESSION['acctype'] = $acctype;
        }
        //redirect
        header("Location: ../public/dashboard.php");
    }

    else // user not found
    {
        $error = true;

        //login fail
        header("Location: ../public/LoginForm.php?login=fail");
        //note: since the database is not changed, it is not backed up
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