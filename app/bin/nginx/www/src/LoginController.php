<?php
require_once "../src/DBConnector.php";
require_once "../src/User.php";

global $acctype;

class LoginController
{
    public static function Login($un,$pw)
    {
        if (LoginController::ValidateInput($un,$pw)==true) //Validate Input
        {
            $uname = htmlspecialchars($un); //to prevent XSS
            $pword = htmlspecialchars($pw);

            $User = DBConnector::GetUser($uname,$pword); //GetUser() -> User

            if (LoginController::ValidateUser($uname,$User->GetEmail(),$pword,$User->GetPassword()==true)) //Validate User
            {
                $acctype = $User->GetAccType(); //determines which dashboard to present

                if (isset($_SESSION))
                {
                    //a session already existed
                    session_destroy();
                    session_start();
                    $_SESSION['email'] = $uname;
                    $_SESSION['acctype'] = $acctype;
                } 
                
                else
                {
                    //a session did not exist
                    session_start();
                    $_SESSION['email'] = $uname;
                    $_SESSION['acctype'] = $acctype;
                }

                //redirect
                header("Location: ../public/dashboard.php");
            }

            else // invalid user credentials
            {
                header("Location: ../public/LoginForm.php?login=fail");
            }
        }

        else // invalid input
        {
            header("Location: ../public/LoginForm.php?login=fail");
        }
    }

    function ValidateInput($un,$pw)
    {
        // password requirements
        $passwordFormat = "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,20}/"; // whitelist of special chars ! @ # $ % ^ & *

        // username must be in email format, password must match regex
        if(filter_var($un,FILTER_VALIDATE_EMAIL)==true AND preg_match($passwordFormat,$pw)==true)
        {
            return true;
        }

        else return false;
    }

    
    function ValidateUser($un,$userUname,$pw,$userPword)
    {
        $inputUname = strtolower($un); //makes username noncase-sensitive
        $hashedInputPword = hash('ripemd256', $pw);

        if ($inputUname == $userUname && $hashedInputPword == $userPword)
        {
            return true;
        }

        else return false;
    }
}