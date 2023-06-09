<?php
require_once "../src/DBConnector.php";
require_once "../src/LogoutController.php";
require_once "../src/SecurityTemplate.php";

class EnterGradeControl extends SecurityTemplate
{
    public static function EnterGrade()
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$ENTERGRADEFORM_PHP, null, null);

        if($secResult === true) //authorized
            header("Location: ../public/EnterGradeForm.php");
        else
        {
            //check the error code, display appropriate page
            switch ($secResult)
            {
                case Constants::$INVALID_SESSION:
                    //FacultyDashboard::Error(Constants::$INVALID_SESSION);
                    //break;
                case Constants::$UNAUTHORIZED:
                    //FacultyDashboard::Error(Constants::$UNAUTHORIZED);
                    //break;
                default:
                    LogoutController::Logout();  //initiate logout on invalid session || unauthorized
            }
        }
    }

    //Constants::$ENTER_GRADE_TYPE = 13
    public static function SubmitGrade($data, $dataType = 13) // validates crn and .csv files for correct format
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$ENTERGRADEFORM_PHP, $data, $dataType);

        if($secResult === true)
        {
            //error_log("passed sec", 0);
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

            // if all validation has passed, input will be saved to the database
            DBConnector::SaveGrade($data);
    
            header("Location: ../public/FacultyDashboard.php");
        }
        else
        {
            //error_log("failed sec", 0);
            //check the error code, display appropriate page
            switch ($secResult)
            {
                case Constants::$INVALID_SESSION:
                    
                case Constants::$UNAUTHORIZED:
                    LogoutController::Logout();  //initiate logout on invalid session || unauthorized
                    break;
                case Constants::$INVALID_INPUT:

                default:
                    EnterGradeForm::Error(Constants::$INVALID_INPUT);
            }
        }
    }
}