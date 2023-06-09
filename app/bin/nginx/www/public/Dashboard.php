<?php
require_once "../src/LogoutController.php"; //include controller
require_once "../src/SecurityTemplate.php"; //Access Control

class Dashboard
{
  public static function LoadPage()
  { 
    //check that session exists and is nonempty
    
    session_start(); //required to bring session variables into context

    if (isset($_SESSION['email'])) //check that session exists
    {
      //error_log("session is set", 0);
      if (!SecurityTemplate::isSetSessionEmail($_SESSION['email'])) 
      {
        //error_log("session error", 0);
        Dashboard::Logout(); //call logout
      }
      else
        return;
    }
    else
      header("Location: ../public/LoginForm.php"); //launch login form
  }

  public static function Logout()
  {
    self::LoadPage();
    
    //call logout method
    LogoutController::Logout();
  }
}

$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//if request contains logout
if ("logout" == parse_url($url, PHP_URL_QUERY)) {
  Dashboard::Logout(); //call logout
}
?>
