<?php
require_once "ValidationStrategy.php";

class UserTypeValidator implements ValidationStrategy
{
    public function IsValid($data) //check if username is valid size and email format
    {
        $index = 0;
        $valid = false;
        foreach($data as $value)
        {
            switch($index)
            {
                case 0:
                case 10:
                    //validate email
                    $validator = new UsernameTypeValidator();
                    $valid = $validator->IsValid(array($value));
                    break;
                default:
                    //validate everything else
                    $validator = new CharStringTypeValidator();
                    $valid = $validator->IsValid(array($value));
            }

            if($valid == false)
                break;
            else
                $index += 1;
        }

        return $valid; 
    }   
}

?>