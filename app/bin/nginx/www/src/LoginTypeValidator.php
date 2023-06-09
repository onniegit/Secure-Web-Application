<?php
require_once "ValidationStrategy.php";
require_once "PasswordTypeValidator.php";

class LoginTypeValidator implements ValidationStrategy
{
    public function IsValid($data)
    {
        $username =  $data[0];
        $password = $data[1];

        //validate username
        $validator = new UsernameTypeValidator();
        $validUsername = $validator->IsValid(array($username));

        if($validUsername === true)
        {

            //validate password
            $validator = new PasswordTypeValidator();
            $validPassword = $validator->IsValid(array($password));

            if($validPassword === true)
                return true; // all inputs valid
        }

        return false; 
    }   
}

?>