<?php
require_once "ValidationStrategy.php";
class StudentIdTypeValidator implements ValidationStrategy
{
    public function IsValid($data)
    {
        $valid = false;
        $studentId = $data[0];
        $idFormat = '/^927\d{6}$/'; // whitelist; start - 927 - any 6 digits - end 

        if(preg_match($idFormat, $studentId) == true) 
            $valid = true; //valid id format

        /*
        if (strlen($studentId) == 9) //must be 9 digits
        {
            //check that student id is a whole number
            $validator = new IntTypeValidator();
            $valid = $validator->IsValid($data);
        }*/

        return $valid;
    }   
}

?>