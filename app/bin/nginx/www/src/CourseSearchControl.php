<?php
require_once "SecurityTemplate.php";
require_once "DBConnector.php";
require_once "LogoutController.php";

class CourseSearchControl extends SecurityTemplate
{
    public static function courseSearch()
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$COURSESEARCHFORM_PHP, null, null);

        if($secResult === true)
            header("Location: ../public/CourseSearchForm.php");
        else
            self::ReturnError($secResult);
    }

    public static function ReturnError($errorCode)
    {
        switch ($errorCode)
        {
            case Constants::$INVALID_INPUT: //invalid input
                CourseSearchForm::Error(Constants::$INVALID_INPUT);
                break;
            case Constants::$UNAUTHORIZED: //unauthorized user - needs to ba a different form

            case Constants::$INVALID_SESSION: //invalid session - needs to ba a different form

            default:
                LogoutController::Logout();  //initiate logout on invalid session || unauthorized
        }
    }
    
    public static function Submit($data, $dataType = 8) //Constants::$COURSE_SEARCH_TYPE = 8
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$COURSESEARCHFORM_PHP, $data, $dataType);

        if($secResult === true)
        {
            $results = DBConnector::SearchCourse($data); //search course

            //is true on success and false on failure
            if($results->fetchArray(SQLITE3_ASSOC))
            {
                //error_log("results found", 0);

                /*store course search info in cookie for later retrieval and search in CourseSearchLogic.php*/

                if($data[0] != "")
                    setcookie('courseid', $data[0], time() + (86400 / 24), "/"); // 86400 = 1 day, "/" = cookie is available in entire website
                else
                    setcookie('courseid', " ", time() + (86400 / 24), "/");

                if($data[1] != "")
                    setcookie('coursename', $data[1], time() + (86400 / 24), "/");
                else
                    setcookie('coursename', " ", time() + (86400 / 24), "/");
                        
                if($data[2] != "")
                    setcookie('semester', $data[2], time() + (86400 / 24), "/");
                else
                    setcookie('semester', " ", time() + (86400 / 24), "/");

                if($data[3] != "")
                    setcookie('department', $data[3], time() + (86400 / 24), "/");
                else
                    setcookie('department', " ", time() + (86400 / 24), "/");
            }
            else
            {
                //error_log("no results found", 0);
                //set to -1 to prevent another search in CourseSearchLogic.php
                setcookie('courseid', "-1", time() + (86400 / 24), "/");
            }
                    
            //load search form to search and display any search results
            header("Location: ../public/CourseSearchForm.php?search");
        }
        else
            self::ReturnError($secResult);
    }
}

?>
