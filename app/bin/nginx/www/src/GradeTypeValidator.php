<?php
require_once "ValidationStrategy.php";

class GradeTypeValidator implements ValidationStrategy
{
    public function IsValid($data)
    {
        $valid = false;
        $grade = $data[0];
        
        $gradeFormat = '/^[ABCDF]$/'; // whitelist; start - any [ABCDF] - end 

        if(preg_match($gradeFormat, $grade) == true) 
            $valid = true; //valid grade format

        return $valid;
    }   
}

?>