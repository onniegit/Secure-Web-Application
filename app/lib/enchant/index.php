<?php
try {
  require_once "../../src/RedirectController.php";

  if ($GLOBALS['rc']->ValidateLogin()) 
  {
    //redirect to dashboard
    header("Location: ../../public/dashboard.php");
  } else {
      
    //redirect to login
    header("Location: ../../public/LoginForm.php");
  }
}
catch(Exception $e)
{
    header("Location: ../../public/index.php?login=fail");
}