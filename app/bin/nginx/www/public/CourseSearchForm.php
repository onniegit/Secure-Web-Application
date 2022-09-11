<?php
require_once "dashboard.php";
require_once "../src/CSInfo.php";
require_once "../src/CourseSearchControl.php";
require_once "../src/CourseEnrollControl.php";
class CourseSearchForm extends Dashboard
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
                header("Location: ../public/CourseSearchForm.php");
                break;
            case Constants::$INVALID_INPUT:
                header("Location: ../public/CourseSearchForm.php?input=fail");
                break;
            default:
        }
    }

    public static function submit()
    {
        $CSInfo = new CSInfo(); //create object

        /*Get information from the post request*/
        $CSInfo->SetCourseId($_POST['courseid']); 
        $CSInfo->SetCourseName($_POST['coursename']);
        $CSInfo->SetSemester($_POST['semester']);
        $CSInfo->SetDepartment($_POST['department']);

        //search course
       CourseSearchControl::submit($CSInfo);
    }

    public static function enroll()
    {
        /*Get information from the post request*/
        $coursename = $_POST['coursename'];
        $semester = $_POST['semester'];
        $year = $_POST['year'];
        
        CourseEnrollControl::enroll($coursename, $semester, $year);
    }
}
CourseSearchForm::LoadPage();

if ($_SERVER["REQUEST_METHOD"] == "POST") //if POST request detected
{
    //check which form got submitted (course search or course enroll)
    if(isset($_POST["searchForm"])){
        CourseSearchForm::submit(); //call submit for course search
    }
    else
    {
        CourseSearchForm::enroll(); //call enroll for course enroll
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
    <title>Secure ED. - Course Search</title>
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
            <h1>Course Search</h1>
            <div class=horizontal_line>
                <hr>
            </div>

            <br><br>

            <!--Search filters-->
            <div style="text-align:center">
                <div class = "search_pane">
                    <h1>Search Filters:</h1>
                    <div class=horizontal_line>
                        <hr>
                    </div>
                    <?php
                        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if ("input=fail" == parse_url($url, PHP_URL_QUERY))
                        {
                            echo "input is invalid.";
                        }
                        else if("already_enrolled=true" == parse_url($url, PHP_URL_QUERY))
                        {
                            echo "already enrolled.";
                        }
                        else if("enrolled=true" == parse_url($url, PHP_URL_QUERY))
                        {
                            echo "successfully enrolled.";
                        }
                    ?>
                    <form action="CourseSearchForm.php" method="post">
                        <table>
                            <tbody>
                            <tr>
                                <!--Semester field-->
                                <td class="search_filter">
                                    <label class="search_filter">
                                        Semester:
                                    </label>
                                </td>

                                <td class="search_filter_input">
                                    <input type="text" id ="semester" name="semester">
                                </td>

                                <!--Department field-->
                                <td class="search_filter">
                                    <label class="search_filter">
                                        Department:
                                    </label>
                                </td>

                                <td class="search_filter_input">
                                    <input type="text" id="department" name="department">
                                </td>
                            </tr>

                            <tr>
                                <!--Course Name Field-->
                                <td class="search_filter">
                                    <label class="search_filter">
                                        Course Name:
                                    </label>
                                </td>
                                <td class="search_filter_input">
                                    <input type="text" id="coursename" name="coursename"/>
                                </td>

                                <!--Course ID Field-->
                                <td class="search_filter">
                                    <label class="search_filter">
                                        Course ID:
                                    </label>
                                </td>
                                <td>
                                    <input type="text" id="courseid" name="courseid">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="hidden" id="searchForm" name="searchForm" value="true">
                                </td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right">
                                    <button class="button_large" type="submit">
                                        Search
                                    </button></td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <!--End of Search filters-->


                <!--Search results-->
                <div class="course_search_results">
                    <h1>Results:</h1>
                    <div class="horizontal_line">
                        <hr>
                    </div>

                    <table class="course_search_table">
                        <thead>
                        <tr>
                            <td class="course_search_results_column_name">
                                <b><u>Course Name</u></b>
                            </td>

                            <td class="course_search_results_column_name">
                                <b><u>Course ID</u></b>
                            </td>

                            <td class="course_search_results_column_name">
                                <b><u>Professor</u></b>
                            </td>

                            <td class="course_search_results_column_name">
                                <b><u>Semester</u></b>
                            </td>

                            <td class="course_search_results_column_name">
                                <b><u>Location</u></b>
                            </td>

                            <td class="course_search_results_column_name">
                            </td>
                        </tr>
                        </thead>
                    </table>
                    <div id="results"></div>
                </div>
            </div>
            <!--End of Search results-->
        </main>
    </div>
    <?php
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if ("search" == parse_url($url, PHP_URL_QUERY))
        {
            echo "<script async src='../resources/coursesearchdisplay.js'></script>";
        }
    ?>
</body>
</html>