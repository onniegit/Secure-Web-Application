<?php
require_once "User.php";
require_once "SecurityTemplate.php";
require_once "DBConnector.php";
require_once "LogoutController.php";


class EditAccountControl extends SecurityTemplate
{
    /*
    submitUser - updates given user info, called from EditAccountForm
    $User - user data
    */
    public static function SubmitUser($data, $dataType = 4) //Constants::$USER_TYPE = 4
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$EDITACCOUNTFORM_PHP, $data, $dataType);

        if($secResult === true)
        {
            //error_log("attempt to save", 0);
            $results = DBConnector::UpdateUser($data); //update user

            //is true on success and false on failure
            if($results)
            {
                //error_log("user saved", 0);
                header("Location: ../public/UserSearchForm.php");
            }
            else
            {
                //error_log("unable to save user", 0);
                $secResult = Constants::$EDIT_FAILED;
            }
        }
        else{
            switch ($secResult)
            {
                case Constants::$EDIT_FAILED: 
                    EditAccountForm::Error(Constants::$EDIT_FAILED);
                    break;
                case Constants::$UNAUTHORIZED: //unauthorized user - needs to ba a different form

                case Constants::$INVALID_SESSION: //invalid session - needs to ba a different form

                default:
                    LogoutController::Logout();
            }
        }
    }

    //sets cookie data for client-side
    private static function SetCookie($User)
    {
        /*store user info in cookie for later retrieval in EditAccountForm.php - need to make cookie secure (set httponly flag)*/
        setcookie('email', $User->GetEmail(), time() + 60, "/"); // 60 = 1 min, "/" = cookie is available in entire website
        setcookie('acctype', $User->GetAccType(), time() + 60, "/"); 
        setcookie('password', $User->GetPassword(), time() + 60, "/");
        setcookie('fname', $User->GetFName(), time() + 60, "/");
        setcookie('lname', $User->GetLName(), time() + 60, "/");
        setcookie('dob', $User->GetDOB(), time() + 60, "/");
        setcookie('studentyear', $User->GetYear(), time() + 60, "/");
        setcookie('facultyrank', $User->GetRank(), time() + 60, "/");
        setcookie('squestion', $User->GetSQuestion(), time() + 60, "/");
        setcookie('sanswer', $User->GetSAnswer(), time() + 60, "/");
        setcookie('prevemail', $User->GetEmail(), time() + 60, "/"); //add uname of the user searched for
    }
    
    /*
    editAccount - retrieves info for a given user to edit, called from UserSearchForm
    $acctId - user's email
    */
    public static function EditAccount($data, $dataType = 2) //Constants::$USERNAME_TYPE = 2
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$EDITACCOUNTFORM_PHP, $data, $dataType);
        
        if($secResult == true)
        {
            //error_log($secResult, 0);

            //get account info.
            $User = DBConnector::GetAccount($data);
                
            if($User != null)
            {
                //error_log("setting cookie", 0);
                self::SetCookie($User); //set cookie for client retrieval
            }

            //error_log("loading...", 0);
            //load edit account form with user info
            header("Location: ../public/EditAccountForm.php?edit");
        }
        else 
        {
            switch ($secResult)
            {
                case Constants::$UNAUTHORIZED: //unauthorized user - needs to ba a different form

                case Constants::$INVALID_SESSION: //invalid session - needs to ba a different form

                default:
                    LogoutController::Logout();  //initiate logout on invalid session || unauthorized
            }
        }
    }
}

?>
