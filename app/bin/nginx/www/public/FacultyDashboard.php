<?php
require_once "Dashboard.php";
require_once "../src/EnterGradeControl.php";
class FacultyDashboard extends Dashboard
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
              header("Location: ../public/FacultyDashboard.php");
              break;
          case Constants::$INVALID_INPUT:
              //header("Location: ../public/CourseEnrollForm.php?input=fail");
              break;
          default:
        }
    }

  public static function enterGrade()
  {
    //error_log("calling enter grade", 0);
    EnterGradeControl::EnterGrade();
  }

}
FacultyDashboard::LoadPage();

if ($_SERVER["REQUEST_METHOD"] == "GET") //if GET request detected
{
  $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  //if request contains grade
  if ("grade" == parse_url($url, PHP_URL_QUERY)) {
    FacultyDashboard::enterGrade(); //call enter grade
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
    <?php

echo "<title>Secure ED. - Dashboard</title>";

?>

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
          <button class="button_large" type="button" onclick="Logout();">Log Out</button>
      </nav>
      <main>          
            <h1>Faculty Dashboard</h1>
            <div class=horizontal_line>
                <hr>
            </div>
            <div>
              <button class="button_large" type="button" onclick="location.href='FacultyDashboard.php?grade'">Enter Grades</button>
            </div>
            <br>
      </main>
  </div>
</body>
</html>