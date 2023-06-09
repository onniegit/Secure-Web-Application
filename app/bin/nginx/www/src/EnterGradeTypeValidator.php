<?php
require_once "ValidationStrategy.php";
require_once "SectionIdTypeValidator.php";
require_once "GradeFileTypeValidator.php";

class EnterGradeTypeValidator implements  ValidationStrategy
{
    public function IsValid($data) //check if username is valid size and email format
    {
        $valid = false;

        //validate course number
        $validator = new SectionIdTypeValidator();
        $validCourse = $validator->IsValid($data); // see above, parses csv and validates input

        if($validCourse == true)
        {
            $path = pathinfo($_FILES['file']['name']); //path info for file
            
            //validate file data
            $validator = new GradeFileTypeValidator();
            $validGrade = $validator->IsValid($path); // check grade csv file

            if ($validGrade == true)
                $valid = true;
        }

        return $valid;
    }   
}

?>