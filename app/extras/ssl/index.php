<?php
try {
  require_once "../../bin/nginx/www/src/RedirectController.php";

  if ($GLOBALS['rc']->ValidateLogin()) 
  {
    //redirect to dashboard
    header("Location: ../../bin/nginx/www/public/dashboard.php");
  } else {
      
    //redirect to login
    header("Location: ../../bin/nginx/www/public/LoginForm.php");
  }
}
catch(Exception $e)
{
    header("Location: ../../bin/nginx/www/public/index.php?login=fail");
}