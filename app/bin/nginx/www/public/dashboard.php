<?php
//Access Control
require_once "../src/SessionController.php";
require_once "Form.php";

class Dashboard extends Form
{
  public static function LoadPage()
  { //check that session exists and is nonempty   
    if (!SessionController::isSetSessionEmail()) {
      
      http_response_code(403);
      die('Forbidden');
    }
  }
}

$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//if request contains logout
if ("logout" == parse_url($url, PHP_URL_QUERY)) {
  Dashboard::Logout(); //call logout
}
?>
