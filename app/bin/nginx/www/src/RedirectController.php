<?php
    require_once "SessionController.php";
   class RedirectController extends SessionController
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
       /* function GetType()
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
       } */
   }

   global $rc;
   $rc = new RedirectController();
?>