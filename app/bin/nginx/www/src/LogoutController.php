<?php
require_once "../src/SessionController.php";
class LogoutController{
    static function Logout(){
SessionController::LogoutSession();
    


//redirect
header("Location: ../public/LoginForm.php");
}
}

?>