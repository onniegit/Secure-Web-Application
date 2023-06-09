<?php
    require_once "../src/LoginController.php";
   class RedirectController
   {
       public static function ValidateLogin()
       {
            session_start();
            if (isset($_SESSION['acctype'])) 
            {
                if (LoginController::IsAccountType(Constants::$FACULTY_TYPE)) 
                {
                   //error_log("found faculty!", 0);
                    header("Location: ../public/FacultyDashboard.php");
                }
                elseif (LoginController::IsAccountType(Constants::$ADMIN_TYPE)) 
                {
                    //error_log("found admin!", 0);
                    header("Location: ../public/AdminDashboard.php");
                }
                elseif (LoginController::IsAccountType(Constants::$STUDENT_TYPE)) 
                {
                    //error_log("found student!", 0);
                    header("Location: ../public/StudentDashboard.php");
                }
                else
                    header("Location: ../public/LoginForm.php"); //redirect to login
            }
            else
                header("Location: ../public/LoginForm.php"); //redirect to login
       }
   }

   RedirectController::ValidateLogin();
?>