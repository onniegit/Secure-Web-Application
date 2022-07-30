<?php
require_once "User.php";
require_once "RequestController.php";
require_once "../public/LoginForm.php";
require_once "Constants.php";

global $acctype;

class LoginController extends RequestController
{
    public static function Login($un, $pw)
    {
        $inputValidation = RequestController::ValidateInput($un, $pw); //Validate Input

        if ($inputValidation == true) {
            $User = DBConnector::GetUser($un); //GetUser() -> User

            //Validate User's credentials
            $validUser = LoginController::ValidateCredentials($User, $pw);

            //check user is authorized for requested function
            $authorized = LoginController::authorize($un, Constants::$LOGINFORM_PHP);

            if ($validUser == true and $authorized) {

                //create user session
                LoginController::CreateSession($un, $User->GetAccType());

                if (RequestController::HasRights(faculty)) {
                    header("Location: ../public/FacultyDashboard.php");
                }
                elseif (RequestController::HasRights(admin)) {
                    header("Location: ../public/AdminDashboard.php");
                }
                elseif (RequestController::HasRights(student)) {
                    header("Location: ../public/StudentDashboard.php");
                }
            }
            else // invalid user credentials or unauthorized
            {
                LoginForm::Error(Constants::$INVALID_CREDENTIALS);
            }
        }
        else // invalid input
        {
            LoginForm::Error(Constants::$INVALID_INPUT);
        }
    }

    function ValidateCredentials($User, $pw) // verifies correctness of username and password

    {
        $userPword = $User->GetPassword();

        $hashedInputPword = hash(Constants::$PASSWORD_HASH, $pw);

        if ($hashedInputPword == $userPword) {
            return true;
        }
        else
            return false;
    }
}