<?php
require_once "../src/DBConnector.php";
require_once "../src/User.php";
require_once "../src/RequestController.php";

class ForgotPwController extends RequestController
{   
    public static function ForgotPassword($un) // if provided username exists, redirects user to answer their security question
    {
        if (ForgotPwController::ValidateEmail($un)==true)
        {
            if (DBConnector::UsernameExists($un)==true)
            {
                header("Location:../public/ForgotPasswordSecQ.php");
            }

            else header("Location: ../public/ForgotPassword.php?emailcheck=fail");
        }

        else header("Location: ../public/ForgotPassword.php?emailcheck=fail");
    }

    public static function SecurityQuestion($un, $answer) // checks the provided answer against the answer stored in the db
    {
        $tempUser = DBConnector::TempUser($un);
        $myAnswer = $tempUser->GetSAnswer();
        $userInput = ForgotPwController::XssValidation($answer);

        if ($userInput == $myAnswer)
        {
            header("Location:../public/ForgotPasswordChange.php");
        }

        else header("Location:../public/ForgotPasswordSecQ.php?answercheck=fail");
    }

    public static function SecQ($un) // returns the user's security question (used to display to form)
    {
        $tempUser = DBConnector::TempUser($un);
        return $tempUser->GetSQuestion();
    }

    public static function UpdatePassword($un, $new, $confirm) // validates that input meets complexity requirements and inputs match, sends to dbconnector for updating
    {
        $newPassword = $new;
        $newPasswordConfirm = $confirm;

        if ($newPassword == null OR $newPasswordConfirm == null)
        {
            header("Location: ../public/ForgotPasswordChange.php?blank");
        }

        if (ForgotPwController::ValidatePassword($new)==true AND $newPassword == $newPasswordConfirm)
        {
            $hashedNewPassword = hash('ripemd256', $newPassword);

            DBConnector::UpdatePasswordDB($un, $hashedNewPassword);

            header("Location: ../public/index.php");
        }

        else header("Location: ../public/ForgotPasswordChange.php?passwordcheck=fail");
    }
}