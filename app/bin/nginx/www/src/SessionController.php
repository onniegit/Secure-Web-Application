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
    static function LogoutSession(){
        session_start();
        if (isset($_SESSION['acctype'])) {
            //a session exists
            session_destroy(); //clear all session variables
        }
        else{throw new Exception("Session did not exist");}
    //redirect
    
    }
}
?>