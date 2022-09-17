<?php
require_once "../src/DBConnector.php";
require_once "../src/RequestController.php";

class EnterGradeControl extends RequestController
{
    function EnterGrade()
    {
        //error_log("authenticating..", 0);
        $validSession = EnterGradeControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //error_log($un, 0);
            //check user is authorized for requested function
            $authorized = EnterGradeControl::authorize($un, Constants::$ENTERGRADEFORM_PHP);
            if($authorized)
            {
                header("Location: ../public/EnterGradeForm.php");
            }
            else
            {
                //error_log("not authorized", 0);
                FacultyDashboard::Error(Constants::$UNAUTHORIZED);
            }
        }
        else
        {
            //invalid session
            //error_log("invalid session", 0);
            FacultyDashboard::Error(Constants::$INVALID_SESSION);
        }
    }

    function ValidateFile()
    {
        $path = pathinfo($_FILES['file']['name']); //path info for file

        if ($path['extension'] != 'csv') // only allows .csv files to be uploaded
        {
            //invalid file extension
            return false;
        }

        //insert data into the database if csv
        if($path['extension'] == 'csv')
        {
            //prepare vars to insert data into database
            $handle = fopen(($_FILES['file']['tmp_name']), "r"); //sets a read-only pointer at beginning of file
            
            while (($data = fgetcsv($handle, 9001, ",")) !== FALSE) //iterate through csv
            { 
                $allowedNumCols = 2;
                if (count($data) != $allowedNumCols) // prevents extra columns which could contain malicious code
                {
                    //invalid file format
                    fclose($handle);
                    return false;
                }

                // ensures student ID and grade are in correct format
                if (EnterGradeControl::ValidateSID($data[0])==false OR EnterGradeControl::ValidateGrade($data[1])==false)
                {
                    //invalid file format
                    fclose($handle);
                    return false;
                }
            }
            fclose($handle);
        }

        return true;
    }

    public static function ValidateCourseNo(&$crn) // validates given course number
    {
        //still needs more input validation

        $crn = filter_var($crn, FILTER_VALIDATE_INT); // sanitizes crn, returns false if not in correct format

        if($crn == false)
            return false;

        //validate course id
        $crn = EnterGradeControl::XssValidation($crn); //to prevent XSS
              
        return true;
    }

    function submitGrade($crn) // validates crn and .csv files for correct format
    {
        //error_log("authenticating..", 0);
        $validSession = EnterGradeControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //error_log($un, 0);
            //check user is authorized for requested function
            $authorized = EnterGradeControl::authorize($un, Constants::$ENTERGRADEFORM_PHP);
            if($authorized)
            {   
                //validate file data
                $validFile = EnterGradeControl::ValidateFile();

                //validate course number
                $validCourse = EnterGradeControl::ValidateCourseNo($crn); // see above, parses csv and validates input

                if($validFile == true && $validCourse == true)
                {
                    $currentDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');//get root directory
                    $uploadDirectory = "\uploads\\";
    
                    //get info about the file
                    $filename = $_FILES['file']['name'];
                    $filetmp  = $_FILES['file']['tmp_name'];
                    
                    //create the upload path with the original filename
                    $uploadPath = $currentDirectory . $uploadDirectory . basename($filename);

                    //error_log($uploadPath, 0);
    
                    //copy file to uploads folder
                    copy($filetmp, $uploadPath);

                    // if all validation has passed, input will be saved in the database
                    DBConnector::SaveGrade($crn);
    
                    header("Location: ../public/FacultyDashboard.php");
                }
                else
                {
                    //error_log("invalid input", 0);
                    EnterGradeForm::Error(Constants::$INVALID_INPUT);
                }
            }
            else
            {
                //error_log("not authorized", 0);
                FacultyDashboard::Error(Constants::$UNAUTHORIZED);
            }
        }
        else
        {
            //invalid session
            //error_log("invalid session", 0);
            FacultyDashboard::Error(Constants::$INVALID_SESSION);
        }
    }
}