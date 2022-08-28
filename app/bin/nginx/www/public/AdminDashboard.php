<?php
require_once "dashboard.php";
require_once "../src/CreateAcctControl.php";
require_once "../src/UserSearchControl.php";
class AdminDashboard extends Dashboard
{
  public static function createAccount()
  {
    CreateAcctControl::createAccount();
  }
  public static function userSearch()
  {
    //error_log("calling search", 0);
    UserSearchControl::userSearch();
  }
}
AdminDashboard::LoadPage();

if ($_SERVER["REQUEST_METHOD"] == "GET") //if GET request detected
{
  $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  //if request contains create
  if ("create" == parse_url($url, PHP_URL_QUERY)) {
    AdminDashboard::createAccount(); //call createAccount
  }
  //if request contains search
  elseif ("search" == parse_url($url, PHP_URL_QUERY)) {
    //error_log("found search", 0);
    AdminDashboard::userSearch(); //call userSearch
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
  <title>Secure ED. - Dashboard</title>
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
            <h1>Admin Dashboard</h1>
            <div class=horizontal_line>
                <hr>
            </div>
            <div>
              <button class="button_large" type="button" onclick="location.href='AdminDashboard.php?create'">Create Account</button>
            </div>
            <br>
              <button class="button_large" type="button" onclick="location.href='AdminDashboard.php?search'">User Search</button>
            <br>
      </main>
  </div>
</body>
</html>