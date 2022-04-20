<?php

trait InputValidator
{
    function ValidateEmail($un) // returns true if input is in email format
    {
        if(filter_var($un,FILTER_VALIDATE_EMAIL)==true) return true;

        else return false;
    }

    function ValidatePassword($pw) // returns true if password is in correct format - does not verify correctness of password
    {
        // requirement is at least one capital letter, one lowercase letter, one number, and one special char from whitelist
        $passwordFormat = "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,20}/"; // whitelist of special chars ! @ # $ % ^ & *

        if(preg_match($passwordFormat,$pw)==true) return true;

        else return false;
    }

    function XssValidation($input) // applies in-built php method to input to prevent XSS
    {
        return htmlspecialchars($input);
    }

    function ValidateSID($sid) // returns true if student ID is in correct format
    {
        $sidFormat = '/^(?=.*[0-9]).{9}$/';

        if(preg_match($sidFormat,$sid)==true) return true;

        else return false;
    }

    function ValidateGrade($grade) // returns true if grade is in correct format
    {
        $gradeFormat = '/^[A-F]$/';

        if(preg_match($gradeFormat,$grade)==true) return true;

        else return false;
    }
}