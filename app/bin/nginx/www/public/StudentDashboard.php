<?php
require_once "dashboard.php";
require_once "../src/CourseSearchControl.php";
class StudentDashboard extends Dashboard
{
  public static function courseSearch()
  {
    //error_log("calling search", 0);
    CourseSearchControl::courseSearch();
  }
}
StudentDashboard::LoadPage();

if ($_SERVER["REQUEST_METHOD"] == "GET") //if GET request detected
{
  $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

  //if request contains search
  if ("search" == parse_url($url, PHP_URL_QUERY)) {
    //error_log("found search", 0);
    StudentDashboard::courseSearch(); //call courseSearch
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
            <h1>Student Dashboard</h1>
            <div class=horizontal_line>
                <hr>
            </div>
            <div>
              <button class="button_large" type="button" onclick="location.href='StudentDashboard.php?search'">Course Search</button>
            </div>
            <br>
      </main>
  </div>
</body>
</html>