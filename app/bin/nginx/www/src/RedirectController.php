<?php

define("admin", 1);
define("faculty", 2);
define("student", 3);
   class RedirectController
   {
       public $ValidateLogin = 'isValid';

       function ValidateLogin()
       {
        session_start();
        if(isset($_SESSION['acctype']))
        {
            return true;
        }
        else
        {
            return false;
        }
       }
       function GetType()
       {
        session_start();
        if(isset($_SESSION['acctype']))
        {
            return $_SESSION['acctype'];
        }
        else
        {
            return -1;
        }
       }
   }

   global $rc;
   $rc = new RedirectController();
?>