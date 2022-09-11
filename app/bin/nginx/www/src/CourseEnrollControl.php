<?php
require_once "RequestController.php";
require_once "DBConnector.php";
require_once "Constants.php";

class CourseEnrollControl extends RequestController
{
    public static function sectionEnroll($sectionId)
    {
        //error_log("authenticating..", 0);
        $validSession = CourseEnrollControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //error_log($un, 0);
            //check user is authorized for requested function
            $authorized = CourseEnrollControl::authorize($un, Constants::$COURSEENROLLFORM_PHP);
            if($authorized)
            {
                //validate input data
                $validInput = CourseEnrollControl::ValidateSectionId($sectionId);

                if($validInput == true)
                {
                    $results = DBConnector::enroll($un, $sectionId); //enroll user into section

                    if(!$results)
                    {
                        //redirect back on error
                        header("Location: ../public/CourseSearchForm.php?already_enrolled=true");
                    }
                    else
                    {
                        //redirect
                        header("Location: ../public/CourseSearchForm.php?enrolled=true");
                    }
                }
                else
                {
                    //invalid input
                    //error_log("invalid input", 0);
                    CourseEnrollForm::Error(Constants::$INVALID_INPUT);
                }
            }
            else
            {
                //unauthorized user - needs to ba a different form
                //error_log("unauthorized user", 0);
                CourseEnrollForm::Error(Constants::$UNAUTHORIZED);
            }
        }
        else
        {
            //invalid session - needs to ba a different form
            //error_log("invalid session", 0);
            CourseEnrollForm::Error(Constants::$INVALID_SESSION);
        }
    }

    public static function ValidateSectionId(&$sectionId) // validates given input data
    {
        //still needs more input validation

        //validate section id
        if($sectionId != ""){
            $sectionId = CourseEnrollControl::XssValidation($sectionId); //to prevent XSS
        }

        return true;
    }

    public static function ValidateCourseInfo(&$coursename, &$semester, &$year) // validates given search data
    {
        //still needs more input validation

        //validate course name
        if($coursename != ""){
            $coursename = CourseEnrollControl::XssValidation($coursename); //to prevent XSS
        }

        //validate course semester
        if($semester != ""){
            $semester = CourseEnrollControl::XssValidation($semester); //to prevent XSS
        }

        //validate year
        if($year != ""){
            $year = CourseEnrollControl::XssValidation($year); //to prevent XSS
        }

        return true;
    }
    
    public static function enroll($coursename, $semester, $year)
    {
        $validSession = CourseEnrollControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //check user is authorized for requested function
            $authorized = CourseEnrollControl::authorize($un, Constants::$COURSEENROLLFORM_PHP);
            if($authorized)
            {
                //validate course search data
                $validInput = CourseEnrollControl::ValidateCourseInfo($coursename, $semester, $year);

                if($validInput == true)
                {
                    $results = DBConnector::getSections($coursename, $semester, $year); //search course sections

                    //is true on success and false on failure
                    if($results->fetchArray(SQLITE3_ASSOC))
                    {
                        //error_log("results found", 0);

                        /*store course search info in cookie for later retrieval and search in CourseEnrollLogic.php*/

                        if($coursename != "")
                            setcookie('coursename', $coursename, time() + (86400 / 24), "/");
                        else
                            setcookie('coursename', " ", time() + (86400 / 24), "/");
                        
                        if($semester != "")
                            setcookie('semester', $semester, time() + (86400 / 24), "/");
                        else
                            setcookie('semester', " ", time() + (86400 / 24), "/");

                        if($year != "")
                            setcookie('year', $year, time() + (86400 / 24), "/");
                        else
                            setcookie('year', " ", time() + (86400 / 24), "/");
                        
                         //load search form to search and display any search results
                        header("Location: ../public/CourseEnrollForm.php?search");
                    }
                    else
                    {
                        error_log("no results found", 0);
                        header("Location: ../public/CourseSearchForm.php");
                    }
                }
                else
                {
                    //invalid course
                    //error_log("invalid course", 0);
                    CourseEnrollForm::Error(Constants::$INVALID_INPUT);
                }
            }
            else
            {
                //unauthorized user - needs to ba a different form
                //error_log("unauthorized user", 0);
                CourseEnrollForm::Error(Constants::$UNAUTHORIZED);
            }
        }
        else
        {
            //invalid session - needs to ba a different form
            //error_log("invalid session", 0);
            CourseEnrollForm::Error(Constants::$INVALID_SESSION);
        }
    }
}

?>
