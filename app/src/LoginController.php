<?php
require "../src/DBConnector.php";
require "../src/User.php";

global $acctype;

class LoginController
{

    public static function Login($un,$pw)
    {
        if (LoginController::ValidateInput($un,$pw)==true) //Validate Input
        {
            $User = DBConnector::GetUser($un); //GetUser() -> User

            if (LoginController::ValidateUser($un,$User->GetEmail(),$pw,$User->GetPassword()==true))
            {
                $acctype = $User->GetAccType();

                if (isset($_SESSION))
                {
                    //a session already existed
                    session_destroy();
                    session_start();
                    $_SESSION['email'] = $un;
                    $_SESSION['acctype'] = $acctype;
                } 
                
                else
                {
                    //a session did not exist
                    session_start();
                    $_SESSION['email'] = $un;
                    $_SESSION['acctype'] = $acctype;
                }

                //redirect
                header("Location: ../public/dashboard.php");
            }

            else
            {
                header("Location: ../public/LoginForm.php?login=fail");
            }
        }

        else
        {
            header("Location: ../public/LoginForm.php?login=fail");
        }
    }

    function ValidateInput($un,$pw)
    {
        $passwordFormat = "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*()]).{8,20}/";

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