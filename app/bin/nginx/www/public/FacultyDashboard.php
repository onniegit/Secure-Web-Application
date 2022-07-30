<?php
require_once "dashboard.php";
class FacultyDashboard extends Dashboard
{
}
FacultyDashboard::LoadPage();
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
              <button class="button_large" type="button" onclick="location.href='enter_grades.php'">Enter Grades</button>
            </div>
            <br>
      </main>
  </div>
</body>
</html>