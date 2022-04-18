<?php

class SessionController{
    static function CreateSession($User,$uname, $pword){
        
        $acctype = $User->GetAccType(); //determines which dashboard to present
        if (isset($_SESSION))
                {
                    //a session already existed
                    session_destroy();
                    session_start();
                    $_SESSION['email'] = $uname;
                    $_SESSION['acctype'] = $acctype;
                } 
                
                else
                {
                    //a session did not exist
                    session_start();
                    $_SESSION['email'] = $uname;
                    $_SESSION['acctype'] = $acctype;
                }
    }
    static function authenticate(){
        session_start();
        if (isset($_SESSION['acctype'])) {
            //a session exists
            return true;
        }
        else{ 
            return false;
        }
    }
    static function closeSession(){
        
            session_destroy(); //clear all session variables
        
    //redirect
    
    }
}
?>