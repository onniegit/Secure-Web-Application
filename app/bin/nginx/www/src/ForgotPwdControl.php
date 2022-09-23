<?php
require_once "../src/DBConnector.php";
require_once "../src/User.php";
require_once "../src/RequestController.php";
require_once "../src/Constants.php";

class ForgotPwController extends RequestController
{   
    public static function ForgotPassword($un) // if provided username exists, redirects user to answer their security question
    {
        $validUsername = ForgotPwController::ValidateEmail($un);
        if ($validUsername == true)
        {
            //get security question
            $secQ = DBConnector::getSecQuestion($un);

            if ($secQ != null)
            {
                //store username and security question in cookie
                setcookie('username', $un, time() + (86400 / 24), "/");
                setcookie('secquestion', $secQ, time() + (86400 / 24), "/");
                
                header("Location:../public/SecurityForm.php");
            }
            else 
                header("Location: ../public/ForgotPassword.php?emailcheck=fail");
        }
        else 
            header("Location: ../public/ForgotPassword.php?emailcheck=fail");
    }

    public static function ValidateAnswer($answer)
    {
        return ForgotPwController::XssValidation($answer);
    }

    public static function Submit($answer) // checks the provided answer against the answer stored in the db
    {
        $un = $_COOKIE['username'];
        $userInput = ForgotPwController::ValidateAnswer($answer);
        
        //get security answer
        $myAnswer = DBConnector::getSecAnswer($un);
        $myAnswer = strtolower($myAnswer);

        if ($userInput == $myAnswer)
            header("Location:../public/PasswordForm.php");
        else 
            header("Location:../public/SecurityForm.php?answercheck=fail");
    }

    public static function getSecQ($un) // returns the user's security question (used to display to form)
    {
        $User = DBConnector::GetUser($un);
        
        if($User != null)
        {
            return $User->GetSQuestion();
        }
        
        return null;
    }

    public static function ChangePassword($password, $confirmPassword) // validates that input meets complexity requirements and inputs match, sends to dbconnector for updating
    {
        $un = $_COOKIE['username'];

        if ($password != null && $confirmPassword == $password) //if passwords match
        {
            $validPwd = ForgotPwController::ValidatePassword($password);

            if ($validPwd == true)
            {
                $hashedNewPassword = hash(Constants::$PASSWORD_HASH, $password);

                DBConnector::UpdatePassword($un, $hashedNewPassword);

                header("Location: ../public/LoginForm.php");
            }
            else
                header("Location: ../public/PasswordForm.php?passwordcheck=fail");        
        }
        else
            header("Location: ../public/PasswordForm.php?passwordcheck=fail");
    }
}