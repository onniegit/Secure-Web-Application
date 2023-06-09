<?php
require_once "SecurityTemplate.php";
require_once "UsernameTypeValidator.php";
require_once "../public/LoginForm.php";

global $acctype;

class LoginController extends SecurityTemplate
{
    //call Login with Constants::$LOGIN_TYPE (constant 1) as default value
    public static function Login($data, $dataType = 1)
    {
        //validate input, not session validation and access control check needed
        $validData = self::SecurityCheck(null, null, $data, $dataType);
        
        if($validData == true)
        {
            //error_log("valid data", 0);

            //authenticate user
            $validUSer = self::SecurityCheck($data, null, null, null);

            if($validUSer === true)
            {
                if (self::IsAccountType(Constants::$FACULTY_TYPE)) 
                {
                   //error_log("found faculty!", 0);
                    header("Location: ../public/FacultyDashboard.php");
                }
                elseif (self::IsAccountType(Constants::$ADMIN_TYPE)) 
                {
                    //error_log("found admin!", 0);
                    header("Location: ../public/AdminDashboard.php");
                }
                elseif (self::IsAccountType(Constants::$STUDENT_TYPE)) 
                {
                    //error_log("found student!", 0);
                    header("Location: ../public/StudentDashboard.php");
                }
            }
            else
                LoginForm::Error(Constants::$INVALID_CREDENTIALS); //user not found
        }
        else
            LoginForm::Error(Constants::$INVALID_INPUT);
    }

    private static function IsAccountType($acctype)
    {
        //session_start();
        //Check if PHP session has already started
        if(session_id() == '')
        {
            session_start(); //resume session
         }

        if (isset($_SESSION['acctype']) && $_SESSION['acctype'] == $acctype)
        {
            return true;
        }
        else 
        {
            return false;
        }
    }
}
?>