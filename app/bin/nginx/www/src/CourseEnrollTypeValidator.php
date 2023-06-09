<?php

require_once "ValidationStrategy.php";

class CourseEnrollTypeValidator implements ValidationStrategy
{
    public function IsValid($data) //check if username is valid size and email format
    {
        $index = 0;
        $valid = false;
        foreach($data as $value)
        {
            switch($index) 
            {
                case 2:
                    //validate year
                    $validator = new YearTypeValidator();
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