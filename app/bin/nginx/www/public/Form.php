<?php
require_once "../src/LogoutController.php"; //include controller

class Form
{
  public static function Logout()
  {
    try
    {
      //call logout method
      LogoutController::Logout();
  
    }
    catch(Exception $e)
    {
      echo "error2!";
    }
  }
}

?>

