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
   }

   global $redirectController;
   $redirectController = new RedirectController();
?>