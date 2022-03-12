<?php
try {
  require_once "../nginx/www/src/RedirectController.php";

  if ($GLOBALS['rc']->ValidateLogin()) 
  {
    //redirect to dashboard
    header("Location: ../nginx/www/public/dashboard.php");
  } else {
      
    //redirect to login
    header("Location: ../nginx/www/public/LoginForm.php");
  }
}
catch(Exception $e)
{
    header("Location: ../nginx/www/public/index.php?login=fail");
}