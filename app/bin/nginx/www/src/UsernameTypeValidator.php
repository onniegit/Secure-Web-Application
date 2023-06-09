<?php

require_once "ValidationStrategy.php";

class UsernameTypeValidator implements ValidationStrategy
{
    public function IsValid($data) //check if username is valid size and email format
    {
        $valid = false;
        $email = $data[0];
        $length = strlen($email);

        if($length >= 11 && $length <= 25) //check the size
        {
            if(filter_var($email, FILTER_VALIDATE_EMAIL) == true) //check the format
                $valid = true; //valid email format
        }

        return $valid;
    }   
}

?>