<?php
require_once "RequestController.php";
require_once "DBConnector.php";
require_once "Constants.php";
require_once "CSInfo.php";

class CourseSearchControl extends RequestController
{
    public static function courseSearch()
    {
        //error_log("authenticating..", 0);
        $validSession = CourseSearchControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //error_log($un, 0);
            //check user is authorized for requested function
            $authorized = CourseSearchControl::authorize($un, Constants::$COURSESEARCHFORM_PHP);
            if($authorized)
            {
                header("Location: ../public/CourseSearchForm.php");
            }
            else
            {
                //error_log("not authorized", 0);
            }
        }
        else
        {
            //invalid session
            //error_log("invalid session", 0);
        }
    }

    public static function ValidateCourseInfo(&$CSInfo) // validates given search data
    {
        //still needs more input validation

        //validate course id
        if($CSInfo->GetCourseId() != ""){
            $courseid = CourseSearchControl::XssValidation($CSInfo->GetCourseId()); //to prevent XSS
            $CSInfo->SetCourseId($courseid);
        }

        //validate course name
        if($CSInfo->GetCourseName() != ""){
            $cname = CourseSearchControl::XssValidation($CSInfo->GetCourseName()); //to prevent XSS
            $CSInfo->SetCourseName($cname);
        }

        //validate department
        if($CSInfo->GetDepartment() != ""){
            $department = CourseSearchControl::XssValidation($CSInfo->GetDepartment()); //to prevent XSS
            $CSInfo->SetDepartmente($department);
        }

        //validate semester
        if($CSInfo->GetSemester() != ""){
            $semester = CourseSearchControl::XssValidation($CSInfo->GetSemester()); //to prevent XSS
            $CSInfo->SetSemester($semester);
        }
        
        return true;
    }
    
    public static function submit($CSInfo)
    {
        $validSession = CourseSearchControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //check user is authorized for requested function
            $authorized = CourseSearchControl::authorize($un, Constants::$COURSESEARCHFORM_PHP);
            if($authorized)
            {
                //validate course search data
                $validUser = CourseSearchControl::ValidateCourseInfo($CSInfo);

                if($validUser == true)
                {
                    $results = DBConnector::searchCourse($CSInfo); //search course

                    //is true on success and false on failure
                    if($results->fetchArray(SQLITE3_ASSOC))
                    {
                        //error_log("results found", 0);

                        /*store course search info in cookie for later retrieval and search in CourseSearchLogic.php*/
                        if($CSInfo->GetCourseId() != "")
                            setcookie('courseid', $CSInfo->GetCourseId(), time() + (86400 / 24), "/"); // 86400 = 1 day, "/" = cookie is available in entire website
                        else
                            setcookie('courseid', " ", time() + (86400 / 24), "/");

                        if($CSInfo->GetCourseName() != "")
                            setcookie('coursename', $CSInfo->GetCourseName(), time() + (86400 / 24), "/");
                        else
                            setcookie('coursename', " ", time() + (86400 / 24), "/");
                        
                        if($CSInfo->GetSemester() != "")
                            setcookie('semester', $CSInfo->GetSemester(), time() + (86400 / 24), "/");
                        else
                            setcookie('semester', " ", time() + (86400 / 24), "/");

                        if($CSInfo->GetDepartment() != "")
                            setcookie('department', $CSInfo->GetDepartment(), time() + (86400 / 24), "/");
                        else
                            setcookie('department', " ", time() + (86400 / 24), "/");
                    }
                    else
                    {
                        error_log("no results found", 0);
                        //set to -1 to prevent another search in CourseSearchLogic.php
                        setcookie('courseid', "-1", time() + (86400 / 24), "/");
                    }
                    
                    //load search form to search and display any search results
                    header("Location: ../public/CourseSearchForm.php?search");
                }
                else
                {
                    //invalid user
                    //error_log("invalid user", 0);
                    CourseSearchForm::Error(Constants::$INVALID_INPUT);
                }
            }
        }
        else
        {
            //invalid session
            //error_log("invalid session", 0);
            CourseSearchForm::Error(Constants::$INVALID_SESSION);
        }
    }
}

?>
