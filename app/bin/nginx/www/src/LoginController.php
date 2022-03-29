<?php
require_once "../src/DBConnector.php";
require_once "../src/User.php";
require_once "../src/InputValidator.php";

global $acctype;

class LoginController
{
    public static function Login($un,$pw)
    {
        if (LoginController::ValidateInput($un,$pw)==true) //Validate Input
        {
            $uname = InputValidator::XssValidation($un); //to prevent XSS
            $pword = InputValidator::XssValidation($pw);

            $User = DBConnector::GetUser($uname); //GetUser() -> User

            if (LoginController::ValidateUser($uname,$User->GetEmail(),$pw,$User->GetPassword())==true) //Validate User
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

    function ValidateInput($un,$pw) // validates input for format
    {
        if(InputValidator::ValidateEmail($un)==true AND InputValidator::ValidatePassword($pw)==true) return true;

        else return false;
    }
    
    function ValidateUser($un,$userUname,$pw,$userPword) // verifies correctness of username and password
    {
        $hashedInputPword = hash('ripemd256', $pw);

        if ($un == $userUname AND $hashedInputPword == $userPword)
        {
            return true;
        }

        else return false;
    }
}