<?php
require_once "dashboard.php";
require_once "../src/CourseEnrollControl.php";
class CourseEnrollForm extends Dashboard
{
    public static function Error($ErrorCode)
    {
        //check the error code, display appropriate page
        switch($ErrorCode) 
        {
            case Constants::$INVALID_SESSION:
                header("Location: ../public/LoginForm.php");
                break;
            case Constants::$UNAUTHORIZED:
                header("Location: ../public/CourseEnrollForm.php");
                break;
            case Constants::$INVALID_INPUT:
                header("Location: ../public/CourseEnrollForm.php?input=fail");
                break;
            default:
        }
    }

    public static function submit()
    {
        /*Get information from the post request*/
        $sectionId = $_POST['sectionid']; 

        //enroll course
        CourseEnrollControl::sectionEnroll($sectionId);
    }
}
CourseEnrollForm::LoadPage();

if ($_SERVER["REQUEST_METHOD"] == "POST") //if POST request detected
{
    //check if sectionid got submitted
    if(isset($_POST["sectionid"])){
        CourseEnrollForm::submit(); //call submit for course enroll
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../resources/secure_app.css">
    <link rel="icon" type="image/svg" href="../resources/Header_Lock_Image.svg">
    <script async src="../resources/nav.js"></script>
    <meta charset="utf-8" />
    <title>Secure ED. - Course Enroll</title>
</head>

<body>
    <div id="wrapper">
        <header>
            <table class="header_table">
                <tbody>
                <tr>
                    <td class="lock"><img src="../resources/Header_Lock_Image.svg" style="width:9vh;" alt="Header_lock"></td>
                    <td class="title"><b>Secure ED.</b></td>
                    <td class="header_table_cell"></td>
                </tr>
                </tbody>
            </table>
        </header>

        <!--Navigation Buttons-->
        <nav>
            <button class="button_large" type="button" onclick="location.href = 'StudentDashboard.php'">Dashboard</button>
            <button class="button_large" type="button" onclick="Logout();">Log Out</button>
        </nav>

        <main>

            <!--Heading-->
            <h1>Course Enroll</h1>
            <div class=horizontal_line>
                <hr>
            </div>

            <div class="course_enroll_results">
                <h1>
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "GET") //if GET request detected
                    {
                        /*Get information from cookie*/
                        
                        if(isset($_COOKIE['coursename']))
                        {
                            $coursename = $_COOKIE['coursename'];
                    
                            if(isset($_COOKIE['semester']))
                            {
                                $semester = $_COOKIE['semester'];
                                
                                if(isset($_COOKIE['year']))
                                {
                                    $year = $_COOKIE['year'];
                                    echo "$coursename " ."(" . "$semester " . "$year" . ")";
                                }
                            }
                        }
                    }
                    ?>
                </h1>
                <div class="horizontal_line">
                    <hr>
                </div>

                <table class="course_enroll_table">
                    <thead>
                    <tr>
                        <td class="course_enroll_column_name">
                            <b><u>Course Code</u></b>
                        </td>

                        <td class="course_enroll_column_name">
                            <b><u>Section</u></b>
                        </td>

                        <td class="course_enroll_column_name">
                            <b><u>Professor</u></b>
                        </td>

                        <td class="course_enroll_column_name">
                            <b><u>Time</u></b>
                        </td>

                        <td class="course_enroll_column_name">
                            <b><u>Location</u></b>
                        </td>

                        <td class="course_enroll_column_name">
                        </td>
                    </tr>
                    </thead>
                </table>
                <div id="results"></div>
            </div>
        </main>
    </div>
    <?php
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if ("search" == parse_url($url, PHP_URL_QUERY))
        {
            echo "<script async src='../resources/sectionsearchdisplay.js'></script>";
        }
    ?>
</body>
</html>
