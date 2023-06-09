<?php
require_once "VerificationStrategy.php";

//checks that data does not contain SQL injection
class SQLiVerifier implements VerificationStrategy
{
    public function IsSafe($data)
    {
        $harmfuls =  array("'", '"', "\x1a", ';', '=', '\\', "\0", "\n", "\r"); //blacklist of dangerous values

        foreach ($harmfuls as $harmful)
        {
            //if contains harmful
            if (strpos($data[0], $harmful) !== false)
                return false; //not SQLi safe
        }

        return true; //safe
    }   
}

?>