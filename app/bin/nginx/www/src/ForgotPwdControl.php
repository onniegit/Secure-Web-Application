<?php
require_once "../src/DBConnector.php";
require_once "../src/User.php";
require_once "../src/SecurityTemplate.php";

class ForgotPwController extends SecurityTemplate
{   
    public static function ForgotPassword($data, $dataType) // if provided username exists, redirects user to answer their security question
    {
        $validData = self::SecurityCheck(null, null, $data, $dataType);

        if ($validData == true)
        {
            $username = $data[0];

            //get security question
            $secQ = DBConnector::GetSecQuestion($username);

            if ($secQ != null)
            {
                //store username and security question in cookie
                setcookie('username', $username, time() + (86400 / 24), "/");
                setcookie('secquestion', $secQ, time() + (86400 / 24), "/");
                
                header("Location:../public/SecurityForm.php");
            }
            else 
                header("Location: ../public/ForgotPassword.php?emailcheck=fail");
        }
        else 
            header("Location: ../public/ForgotPassword.php?emailcheck=fail");
    }

    public static function Submit($data, $dataType) // checks the provided answer against the answer stored in the db
    {
        $validData = self::SecurityCheck(null, null, $data, $dataType);

        if($validData)
        {
            $username = $_COOKIE['username'];
            $answer = $data[0]; //get user's response

            //get security answer from DB
            $existingAnswer = DBConnector::getSecAnswer($username);
            $existingAnswer = strtolower($existingAnswer);

            if ($answer == $existingAnswer)
                header("Location:../public/PasswordForm.php");
            else 
                header("Location:../public/SecurityForm.php?answercheck=fail");
        }   
    }

    public static function ChangePassword($data, $dataType) // validates that input meets complexity requirements and inputs match, sends to dbconnector for updating
    {
        $validData = self::SecurityCheck(null, null, $data, $dataType);

        if ($validData == true)
        {
            $username = $_COOKIE['username'];
            $password = $data[0];
            $confirmPassword = $data[1];

            if ($password != null && $confirmPassword == $password) //if passwords match
            {
                $hashedNewPassword = hash(Constants::$PASSWORD_HASH, $password);

                DBConnector::UpdatePassword($username, $hashedNewPassword);

                header("Location: ../public/LoginForm.php");               
            }
            else
                header("Location: ../public/PasswordForm.php?passwordcheck=fail");
        }
        else
            header("Location: ../public/PasswordForm.php?passwordcheck=fail");     
    }
}