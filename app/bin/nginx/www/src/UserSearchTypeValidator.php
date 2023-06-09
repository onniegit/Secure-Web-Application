<?php
require_once "ValidationStrategy.php";

class UserSearchTypeValidator implements ValidationStrategy
{
    public function IsValid($data) //check if username is valid size and email format
    {
        $index = 0;
        $valid = false;
        foreach($data as $value)
        {
            //error_log($value, 0);
            switch($index)
            {
                case 0:
                    //validate email, as first element in array
                    $validator = new UsernameTypeValidator();
                    $valid = $validator->IsValid(array($value));
                    break;
                default:
                    //validate everything else
                    $validator = new CharStringTypeValidator();
                    $valid = $validator->IsValid(array($value));
                    break;
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