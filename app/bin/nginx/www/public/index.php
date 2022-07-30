<?php
try {
  require_once "../src/RedirectController.php";

  if ($GLOBALS['redirectController']->ValidateLogin()) 
  {
    //redirect to dashboard
    header("Location: dashboard.php");
  } else {

    //redirect to login
    header("Location: LoginForm.php");
  }
}
catch(Exception $e)
{
    header("Location: LoginForm.php?login=fail");
}
?>