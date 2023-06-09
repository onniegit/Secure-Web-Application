<?php
require_once "SecurityTemplate.php";
require_once "DBConnector.php";

class CourseEnrollControl extends SecurityTemplate
{
    public static function SectionEnroll($data, $dataType = 12) //Constants::$SECTION_ID_TYPE = 12
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$COURSEENROLLFORM_PHP, $data, $dataType);

        if ($secResult === true) 
        {
            $username = $_SESSION['email'];

            $results = DBConnector::Enroll($username, $data); //enroll user into section

            if (!$results)
                header("Location: ../public/CourseSearchForm.php?already_enrolled=true"); //redirect back on error
            else
                header("Location: ../public/CourseSearchForm.php?enrolled=true"); //redirect
        } 
        else
            self::ReturnError($secResult);
    }

    public static function Enroll($data, $dataType = 11) //Constants::$COURSE_ENROLL_TYPE
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$COURSEENROLLFORM_PHP, $data, $dataType);

        if ($secResult === true)
        {
            $results = DBConnector::GetSections($data); //search course sections

            //is true on success and false on failure
            if ($results->fetchArray(SQLITE3_ASSOC)) 
            {
                //error_log("results found", 0);

                /*store course search info in cookie for later retrieval and search in CourseEnrollLogic.php*/

                if ($data[0] != "")
                    setcookie('coursename', $data[0], time() + (86400 / 24), "/");
                else
                    setcookie('coursename', " ", time() + (86400 / 24), "/");
            
               if ($data[1] != "")
                    setcookie('semester', $data[1], time() + (86400 / 24), "/");
                else
                    setcookie('semester', " ", time() + (86400 / 24), "/");

                if ($data[2] != "")
                    setcookie('year', $data[2], time() + (86400 / 24), "/");
                else
                    setcookie('year', " ", time() + (86400 / 24), "/");

                //load search form to search and display any search results
                header("Location: ../public/CourseEnrollForm.php?search");
            } 
            else
            {
                //error_log("no results found", 0);
                header("Location: ../public/CourseSearchForm.php");
            }
        } 
        else
            self::ReturnError($secResult);
    }

    public static function ReturnError($errorCode)
    {
        switch ($errorCode)
        {
            case Constants::$INVALID_INPUT: //invalid input
                CourseEnrollForm::Error(Constants::$INVALID_INPUT);
                break;
            case Constants::$UNAUTHORIZED: //unauthorized user - needs to ba a different form
                CourseEnrollForm::Error(Constants::$UNAUTHORIZED);
                break;
            case Constants::$INVALID_SESSION: //invalid session - needs to ba a different form
                CourseEnrollForm::Error(Constants::$INVALID_SESSION);
                break;
            default:
        }
    }
}

?>