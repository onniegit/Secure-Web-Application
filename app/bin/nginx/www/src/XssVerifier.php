<?php
require_once "VerificationStrategy.php";

//checks that data does not contain XSS
class XssVerifier implements VerificationStrategy
{
    public function IsSafe($data)
    {
        $string = $data[0];

        if ($string != strip_tags($string) )
            return false; // Contains HTML or PHP

        return true; // Does not contain HTML
    }   
}

?>