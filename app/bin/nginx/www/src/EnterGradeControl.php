<?php
require_once "../src/DBConnector.php";
require_once "../src/User.php";
require_once "../src/InputValidator.php";

session_start(); //required to bring session variables into context

class EnterGradeControl extends InputValidator
{
    function ValidateInput($crn) // sanitizes crn, returns false if not in correct format
    {
        if ($crn == false)
        {
            http_response_code(403);
            die("CRN must be a number.");
        }

        //prepare vars to insert data into database
        $handle = fopen(($_FILES['file']['tmp_name']), "r"); //sets a read-only pointer at beginning of file
        $path = pathinfo($_FILES['file']['name']); //path info for file

        if ($path['extension'] != 'csv') // only allows .csv files to be uploaded
        {
            http_response_code(403);
            die("You may only upload .csv files.");
        }

        //insert data into the database if csv
        if($path['extension'] == 'csv') //check if file is .csv
        {
            while (($data = fgetcsv($handle, 9001, ",")) !== FALSE) //iterate through csv
            { 
                $allowedNumCols = 2;
                if (count($data) != $allowedNumCols) // prevents extra columns which could contain malicious code
                {
                    http_response_code(403);
                    die("csv file is not in valid format.");
                }

                // ensures student ID and grade are in correct format
                if (InputValidator::ValidateSID($data[0])==false OR InputValidator::ValidateGrade($data[1])==false)
                {
                    http_response_code(403);
                    die("csv file is not in valid format.");
                }
            }
        }
        fclose($handle);
    }

        function EnterGrade($session, $crn)
        {
            // access control will need to be updated
            /*if (SessionControl::Authenticate($session) == false)
            {
                http_response_code(403);
                die("No valid session.");
            }
    
            if (EnterGradeControl::Authorize($_SESSION['acctype']) == false)
            {
                http_response_code(403);
                die("You are not permitted to access this function.");
            }*/
    
            $currentDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');//get root directory
            $uploadDirectory = "\uploads\\";
    
            //get info about the file
            $filename = $_FILES['file']['name'];
            $filetmp  = $_FILES['file']['tmp_name'];
            $sanitizedCrn = filter_var($crn, FILTER_VALIDATE_INT);
    
            //create the upload path with the original filename
            $uploadPath = $currentDirectory . $uploadDirectory . basename($filename);
    
            //copy file to uploads folder
            copy($filetmp, $uploadPath);
    
            EnterGradeControl::ValidateInput($sanitizedCrn); // see above, parses csv and validates input

            // if all validation has passed, input will be saved in the database
            DBConnector::SaveGrade($sanitizedCrn);
    
            header("Location: ../public/dashboard.php");
        }

    }