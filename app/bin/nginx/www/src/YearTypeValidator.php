<?php
require_once "ValidationStrategy.php";
class YearTypeValidator implements ValidationStrategy
{
    public function IsValid($data) //check if username is valid size and email format
    {
        $valid = false;
        $year = $data[0];
        $length = strlen($year);

        if($length == 4) //check the size
        {
            if(filter_var($year, FILTER_VALIDATE_INT) == true) //check the format
                $valid = true; //valid email format
        }

        return $valid;
    }   
}

?>