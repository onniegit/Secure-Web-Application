<?php
require_once "ValidationStrategy.php";

class GradeFileTypeValidator implements ValidationStrategy
{
    public function IsValid($path)
    {
        //check file size
        if ($_FILES['file']['size'] > 1048576) // 1 MB = 1048576 bytes
        {
            return false; //file too large
        } 
          
        if ($path['extension'] != 'csv') // only allows .csv files to be uploaded
        {
            //invalid file extension
            return false;
        }
        
        //insert data into the database if csv
        
        //prepare vars to insert data into database
        $handle = fopen(($_FILES['file']['tmp_name']), "r"); //sets a read-only pointer at beginning of file
          
        while (($data = fgetcsv($handle, 9001, ",")) !== FALSE) //iterate through csv
        { 
            $allowedNumCols = 2;
            if (count($data) != $allowedNumCols) // prevents extra columns which could contain malicious code
            {
                //error_log("invalid file", 0); //invalid file format
                fclose($handle);
                return false;
            }

            // ensures student ID and grade are in correct format
            $validator = new StudentIdTypeValidator();
            $validID = $validator->IsValid(array($data[0]));

            $validator = new GradeTypeValidator();
            $validGrade = $validator->IsValid(array($data[1]));

            if ($validID == false OR $validGrade == false)
            {
                //invalid file format
                fclose($handle);
                return false;
            }
        }
        fclose($handle);

        return true;
    }   
}

?>