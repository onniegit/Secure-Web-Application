<?php
//Access Control
require_once "../src/DashboardController.php";
session_start(); //required to bring session variables into context

if (!isset($_SESSION['email']) or (empty($_SESSION['email']))) //check that session exists and is nonempty
{
    http_response_code(403);
    die('Forbidden');
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
    $atype = $GLOBALS['rc']->GetType();
      if($atype===admin) //admin
      {
          echo "<title>Secure ED. - Admin Dashboard</title>";
      }
      else if($atype===faculty) //faculty
      {
          echo "<title>Secure ED. - Faculty Dashboard</title>";
      }
      else if($atype===student) //student
      {
          echo "<title>Secure ED. - Student Dashboard</title>";
      }
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
          <button class="button_large" type="button" onclick="toLogout();">Log Out</button>
      </nav>

      <?php

      DashboardController::Display();

    ?>
  </div>
</body>
</html>