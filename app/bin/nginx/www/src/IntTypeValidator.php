<?php
require_once "ValidationStrategy.php";

class IntTypeValidator implements ValidationStrategy
{
    public function IsValid($data)
    {
        $number = $data[0];

        //validate format

        if(filter_var($number, FILTER_VALIDATE_INT) == true) 
        {
            // no need to validate SQLi
            //no need to validate XSS
            return true; // all inputs valid
        }

        return false;
    }   
}

?>