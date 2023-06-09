<?php
require_once "SecurityTemplate.php";
require_once "DBConnector.php";
require_once "LogoutController.php";

/**
 * Summary of CreateAccountControl
 */
class CreateAccountControl extends SecurityTemplate
{

    public static function CreateAccount()
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$CREATEACCOUTFORM_PHP, null, null);
        
        //error_log($secResult, 0);
        
        if($secResult == true)
        {
            error_log("authorized", 0);
            header("Location: ../public/CreateAccountForm.php");
        }
        else
            self::ReturnError($secResult);
    }

    /**
     * Summary of ReturnError
     * @param mixed $errorCode
     * @return void
     */
    public static function ReturnError($errorCode)
    {
        switch ($errorCode)
        {
            case Constants::$INVALID_INPUT: //invalid input
                CreateAccountForm::Error(Constants::$INVALID_INPUT);
                break;
            case Constants::$UNAUTHORIZED: //unauthorized user - needs to ba a different form

            case Constants::$INVALID_SESSION: //invalid session - needs to ba a different form

            default:
                LogoutController::Logout();  //initiate logout on invalid session || unauthorized
        }
    }
    
    public static function Submit($data, $dataType = 4) //onstants::$USER_TYPE = 4
    {
        $secResult = false;
        //error_log("in submit method", 0);

        error_log("calling  security check", 0);
        $secResult = self::SecurityCheck(array(null, null), Constants::$CREATEACCOUTFORM_PHP, $data, $dataType);

        error_log($secResult, 0);

        if($secResult === true)
        {
            error_log("creating user", 0);
            DBConnector::CreateUser($data); //save user
            header("Location: ../public/AdminDashboard.php");
        }
        else
            self::ReturnError($secResult);
    }
}

?>
