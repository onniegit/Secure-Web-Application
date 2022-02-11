<?php
try {

  if (isset($_SESSION)) 
  {
    //redirect to dashboard
    header("Location: public/dashboard.php");
  } else {
      
    //redirect to login
    header("Location: public/index.php");
  }
}
catch(Exception $e)
{
    header("Location: ../public/index.php?login=fail");
}