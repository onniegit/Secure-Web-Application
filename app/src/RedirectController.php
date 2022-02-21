<?php
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
   }

   global $rc;
   $rc = new RedirectController();
?>