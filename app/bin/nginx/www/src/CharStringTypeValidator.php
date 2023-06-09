<?php

require_once "ValidationStrategy.php";

class CharStringTypeValidator implements ValidationStrategy
{
    public function IsValid($data)
    {
        $length = strlen($data[0]);
        //error_log($length, 0);

        if ($length >= 0 && $length <= 100) //check the size of the string
            return true; // input is valid

        return false; //user data is invalid
    }   
}

?>