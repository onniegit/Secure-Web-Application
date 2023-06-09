<?php
require_once "ValidationStrategy.php";

class PasswordTypeValidator implements ValidationStrategy
{
    public function IsValid($data) //check password is valid size and format
    { 
        $password = $data[0];
        $length = strlen($password);
        
        //defence in depth - perform multiple checks (some sqli slips thru format check)

        //$pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,10}$/';

        if ($length >= 8 && $length <= 10) //password length is 8 to 10
        {
            //error_log("valid strlen", 0);
            // requirement is at least one capital letter, one lowercase letter, one number, and one special char from whitelist
            $pattern = "/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*])/"; // whitelist of special chars ! @ # $ % ^ & *

            if (preg_match($pattern, $password) == true)
                return true; //all checks passed
        }
            
        return false;
    }   
}

?>