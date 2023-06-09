<?php
require_once "ValidationStrategy.php";

class SectionIdTypeValidator implements ValidationStrategy
{
    public function IsValid($data)
    {
        $length = strlen($data[0]);

        if($length == 4) //must be 4 digits
        {
            //validate format for id
            $validator = new IntTypeValidator();
            $validCourseID = $validator->IsValid($data);

            if($validCourseID)
                return true; // id is valid
        }
        return false; //ID is invalid
    }   
}

?>