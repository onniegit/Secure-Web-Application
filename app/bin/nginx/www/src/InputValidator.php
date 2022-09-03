<?php
require_once "User.php";

trait InputValidator
{
    public static function ValidateEmail($un) // returns true if input is in email format
    {
        if(filter_var($un,FILTER_VALIDATE_EMAIL)==true) 
            return true;
        else 
            return false;
    }

    public static function ValidatePassword($pw) // returns true if password is in correct format - does not verify correctness of password
    {
        // requirement is at least one capital letter, one lowercase letter, one number, and one special char from whitelist
        $passwordFormat = "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,20}/"; // whitelist of special chars ! @ # $ % ^ & *

        if(preg_match($passwordFormat,$pw)==true) 
            return true;
        else 
            return false;
    }

    public static function XssValidation($input):string // applies in-built php methods to input to prevent XSS
    {
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);

        return $input;
    }

    public static function ValidateSID($sid) // returns true if student ID is in correct format
    {
        $sidFormat = '/^(?=.*[0-9]).{9}$/';

        if(preg_match($sidFormat,$sid)==true) 
            return true;
        else 
            return false;
    }

    public static function ValidateGrade($grade) // returns true if grade is in correct format
    {
        $gradeFormat = '/^[A-F]$/';

        if(preg_match($gradeFormat,$grade)==true) 
            return true;
        else 
            return false;
    }

    public static function ValidateInput($un,$pw) // validates username and password for format
    {
        $un = InputValidator::XssValidation($un); //to prevent XSS
        $pw = InputValidator::XssValidation($pw); //to prevent XSS

        $valEmail = InputValidator::ValidateEmail($un); //check email format
        $valPassw = InputValidator::ValidatePassword($pw); //check password format

        if($valEmail == true AND $valPassw == true) 
            return true;
        else 
            return false;
    }

    public static function ValidateUserSearch(&$User) // validates given search data
    {
        //validate email
        if($User->GetEmail() != ""){
            $email = InputValidator::XssValidation($User->GetEmail()); //to prevent XSS
            $valEmail = InputValidator::ValidateEmail($email); //check email format
        
            if($valEmail == false)
            {
                error_log("invalid email", 0);
                return false;
            }
        }

        //validate account type
        if($User->GetAccType() != ""){
            $acctype = InputValidator::XssValidation($User->GetAccType()); //to prevent XSS
            $User->SetAccType($acctype);
        }

        //validate first name
        if($User->GetFName() != ""){
            $fname = InputValidator::XssValidation($User->GetFName()); //to prevent XSS
            $User->SetFName($fname);
        }

        //validate last name
        if($User->GetLName() != ""){
            $lname = InputValidator::XssValidation($User->GetLName()); //to prevent XSS
            $User->SetLName($lname);
        }

        //validate dob
        if($User->GetDOB() != ""){
            $dob = InputValidator::XssValidation($User->GetDOB()); //to prevent XSS
            $User->SetDOB($dob);
        }

        //validate year
        if($User->GetYear() != ""){
            $year = InputValidator::XssValidation($User->GetYear()); //to prevent XSS
            $User->SetYear($year);
        }

        //validate rank
        if($User->GetRank() != ""){
            $rank = InputValidator::XssValidation($User->GetRank()); //to prevent XSS
            $User->SetRank($rank);
        }
        
        return true;
    }

    public static function ValidateUserInfo(&$User) // validates given user data
    {
        //validate email
        $email = InputValidator::XssValidation($User->GetEmail()); //to prevent XSS
        $valEmail = InputValidator::ValidateEmail($email); //check email format
        
        if($valEmail == false)
        {
            error_log("invalid email", 0);
            return false;
        }

        //validate password
        $password = InputValidator::XssValidation($User->GetPassword()); //to prevent XSS
        $valPassw = InputValidator::ValidatePassword($password); //check password format
        
        if($valPassw == false)
        {
            error_log("invalid password", 0);
            return false;
        }

        //validate account type
        $acctype = InputValidator::XssValidation($User->GetAccType()); //to prevent XSS
        $User->SetAccType($acctype);

        //validate first name
        $fname = InputValidator::XssValidation($User->GetFName()); //to prevent XSS
        $User->SetFName($fname);

        //validate last name
        $lname = InputValidator::XssValidation($User->GetLName()); //to prevent XSS
        $User->SetLName($lname);

        //validate dob
        $dob = InputValidator::XssValidation($User->GetDOB()); //to prevent XSS
        $User->SetDOB($dob);

        //validate year
        $year = InputValidator::XssValidation($User->GetYear()); //to prevent XSS
        $User->SetYear($year);

        //validate rank
        $rank = InputValidator::XssValidation($User->GetRank()); //to prevent XSS
        $User->SetRank($rank);

        //validate question
        $question = InputValidator::XssValidation($User->GetSQuestion()); //to prevent XSS
        $User->SetSQuestion($question);

        //validate answer
        $answer = InputValidator::XssValidation($User->GetSAnswer()); //to prevent XSS
        $User->SetSAnswer($answer);

        //validate previous email
        $prevemail = InputValidator::XssValidation($User->GetPrevEmail()); //to prevent XSS
        $User->SetPrevEmail($prevemail);

        return true;
    }
}