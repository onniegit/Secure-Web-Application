<?php
try {
  require_once "www/src/RedirectController.php";

  if ($GLOBALS['rc']->ValidateLogin()) 
  {
    //redirect to dashboard
    header("Location: www/public/dashboard.php");
  } else {
      
    //redirect to login
    header("Location: www/public/LoginForm.php");
  }
}
catch(Exception $e)
{
    header("Location: www/public/index.php?login=fail");
}