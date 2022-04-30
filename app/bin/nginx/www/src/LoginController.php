<?php
require_once "../src/DBConnector.php";
require_once "../src/User.php";
require_once "../src/RequestController.php";

global $acctype;

class LoginController extends RequestController
{
    public static function Login($un,$pw)
    {

        if (LoginController::ValidateInput($un,$pw)==true) //Validate Input
        {
            $uname = LoginController::XssValidation($un); //to prevent XSS
            $pword = LoginController::XssValidation($pw);

            $User = DBConnector::GetUser($uname); //GetUser() -> User

            if (LoginController::ValidateCredentials($uname,$User->GetEmail(),$pw,$User->GetPassword())==true) //Validate User's credentials
            {
                LoginController::CreateSession($User, $uname, $pword);

                if (LoginController::Authenticate(isset($_SESSION)==true AND DBConnector::CheckRights($uname,10)==true))
                {
                //redirect
                header("Location: ../public/dashboard.php");
                }
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
        if(LoginController::ValidateEmail($un)==true AND LoginController::ValidatePassword($pw)==true) return true;

        else return false;
    }
    
    function ValidateCredentials($un,$userUname,$pw,$userPword) // verifies correctness of username and password
    {
        $hashedInputPword = hash('ripemd256', $pw);

        if ($un == $userUname AND $hashedInputPword == $userPword)
        {
            return true;
        }

        else return false;
    }
}