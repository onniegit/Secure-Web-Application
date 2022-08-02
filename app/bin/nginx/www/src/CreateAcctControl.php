<?php
require_once "RequestController.php";
require_once "DBConnector.php";

class CreateAcctControl extends RequestController
{
    public static function createAccount()
    {
        //error_log("create account", 0);
        $validSession = CreateAcctControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //check user is authorized for requested function
            $authorized = CreateAcctControl::authorize($un, Constants::$CREATEACCOUTFORM_PHP);
            if($authorized)
            {
                //error_log("authorized", 0);
                header("Location: ../public/CreateAcctForm.php");
            }
        }
        else
        {
            //invalid session
            //error_log("invalid session", 0);
            CreateAcctForm::Error(Constants::$INVALID_SESSION);
        }
    }

    public static function submit($User)
    {
        $validSession = CreateAcctControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //check user is authorized for requested function
            $authorized = CreateAcctControl::authorize($un, Constants::$CREATEACCOUTFORM_PHP);
            if($authorized)
            {
                //validate user data
                $validUser = CreateAcctControl::ValidateUserInfo($User);

                if($validUser == true)
                {
                    DBConnector::createUser($User); //save user
                    header("Location: ../public/AdminDashboard.php");
                }
                else
                {
                    //invalid user
                    //error_log("invalid user", 0);
                    CreateAcctForm::Error(Constants::$INVALID_INPUT);
                }
            }
        }
        else
        {
            //invalid session
            //error_log("invalid session", 0);
            CreateAcctForm::Error(Constants::$INVALID_SESSION);
        }
    }
}

?>
