<?php
class LogoutController extends RequestController{
    static function Logout(){
        if (SessionController::authenticate()){
            SessionController::closeSession();
        }
        else{throw new Exception("Session did not exist");}
    


        //redirect
        header("Location: ../public/LoginForm.php");
}
}

?>