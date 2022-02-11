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

    //convert password to 80 byte hash using ripemd256 before comparing
    $hashpassword = hash('ripemd256', $mypassword);

    /*if($myusername==null)
    {throw new Exception("input did not exist");}*/


    $myusername = strtolower($myusername); //makes username noncase-sensitive
    global $acctype;


    //query for count
    $query = "SELECT COUNT(*) as count FROM User WHERE Email='$myusername' AND (Password='$mypassword' OR Password='$hashpassword')";
    $count = $db->querySingle($query);

    //query for the row(s)
    $query = "SELECT * FROM User WHERE Email='$myusername' AND (Password='$mypassword' OR Password='$hashpassword')";
    $results = $db->query($query);

    if ($results !== false) //query failed check
    {
        if (($userinfo = $results->fetchArray()) !== (null || false)) //checks if rows exist
        {
            // users or user found
            $error = false;

            $acctype = $userinfo[2];
        } else {
            // user was not found
            $error = true;

        }
    } else {
        //query failed
        $error = true;

    }

    //determine if an account that met the credentials was found
    if ($count >= 1 && !$error) {
        //login success

        if (isset($_SESSION)) {
            //a session already existed
            session_destroy();
            session_start();
            $_SESSION['email'] = $myusername;
            $_SESSION['acctype'] = $acctype;
        } else {
            //a session did not exist
            session_start();
            $_SESSION['email'] = $myusername;
            $_SESSION['acctype'] = $acctype;
        }
        //redirect
        header("Location: ../public/dashboard.php");
    } else {
        //login fail
        header("Location: ../public/index.php?login=fail");
    }
//note: since the database is not changed, it is not backed up
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