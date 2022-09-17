<?php
require_once "../public/Dashboard.php";
require_once "../src/EnterGradeControl.php";

class EnterGradeForm extends Dashboard
{
    public static function Error($ErrorCode)
    {
        //check the error code, display appropriate page
        switch($ErrorCode) 
        {
            case Constants::$INVALID_SESSION:
                header("Location: ../public/LoginForm.php");
                break;
            case Constants::$INVALID_INPUT:
                header("Location: ../public/EnterGradeFormForm.php?input=fail");
                break;
            default:
        }
    }

    public static function submit()
    {
        if (isset($_POST['submit']) && isset($_POST['crn'])) 
        {
            /*
             pass the section number (crn) to enter grade control for processing
             the file name will be read from $_FILES — HTTP File Upload variable
             */
            EnterGradeControl::submitGrade($_POST['crn']);
        }
    }
}
EnterGradeForm::LoadPage();

if ($_SERVER["REQUEST_METHOD"] == "POST") //if POST request detected
{
    EnterGradeForm::Submit(); //call submit
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../resources/secure_app.css">
    <link rel="icon" type="image/svg" href="../resources/Header_Lock_Image.svg">
    <script async src="../resources/nav.js"></script>
    <meta charset="utf-8" />
    <title>Secure ED. - Enter Grades</title>
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
            <button class="button_large" type="button" onclick="location.href ='FacultyDashboard.php'">Dashboard</button>
            <button class="button_large" type="button" onclick="Logout();">Log Out</button>
        </nav>

        <main>

            <!--Heading-->
            <h1>Enter Grades</h1>
            <div class="horizontal_line">
                <hr>
            </div>

            <div style="text-align:center">
                <div style="text-align:center;">
                <?php
                $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                if ("input=fail" == parse_url($url, PHP_URL_QUERY))
                {
                    echo "input is invalid.";
                }
                ?>
                    <form action="../public/EnterGradeForm.php" method="POST" enctype="multipart/form-data">
                        <div class="enter_grades_input" style="text-align:left">
                            Course ID: <input type="text" name="crn" id="crn"/>
                            <input type="hidden" name="MAX_FILE_SIZE" value="9437184000" />
                            <input type="file" name="file" id="file"/>
                            <input type="submit" name="submit" id="submit" value="Submit">&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="button" value="Cancel" onclick=" location.href = 'FacultyDashboard.php'">

                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>