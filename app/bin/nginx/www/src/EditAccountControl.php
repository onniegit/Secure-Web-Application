<?php
require_once "User.php";
require_once "RequestController.php";
require_once "DBConnector.php";


class EditAccountControl extends RequestController
{
    /*
    submitUser - updates given user info, called from EditAccountForm
    $User - user data
    */
    public static function submitUser($User)
    {
        $validSession = EditAccountControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //check user is authorized for requested function
            $authorized = EditAccountControl::authorize($un, Constants::$EDITACCOUNTFORM_PHP);
            if($authorized)
            {
                //validate submitted user data
                $validUser = EditAccountControl::ValidateUserInfo($User);

                if($validUser == true)
                {
                    $results = DBConnector::updateUser($User); //update user

                    //is true on success and false on failure
                    if($results)
                    {
                        //error_log("user saved", 0);
                        header("Location: ../public/UserSearchForm.php");
                    }
                    else
                    {
                        //error_log("uable to save user", 0);
                        EditAccountForm::Error(Constants::$EDIT_FAILED);
                    }
                }
                else
                {
                    //invalid user
                    //error_log("invalid user", 0);
                    EditAccountForm::Error(Constants::$INVALID_INPUT);
                }
            }
            else
            {
                //unathorized access
                //error_log("unathorized", 0);
                EditAccountForm::Error(Constants::$UNAUTHORIZED);
            }
        }
        else
        {
            //invalid session
            //error_log("invalid session", 0);
            EditAccountForm::Error(Constants::$INVALID_SESSION);
        }
    }
    
    /*
    editAccount - retrieves info for a given user to edit, called from UserSearchForm
    $acctId - user's email
    */
    public static function editAccount($acctId)
    {
        $validSession = EditAccountControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //check user is authorized for requested function
            $authorized = EditAccountControl::authorize($un, Constants::$EDITACCOUNTFORM_PHP);
            if($authorized)
            {
                //get account info.
                $User = DBConnector::GetUser($acctId);
                
                if($User != null)
                {
                     /*store user info in cookie for later retrieval in EditUserForm.php - need to make cookie secure (set httponly flag)*/
                     setcookie('email', $User->GetEmail(), time() + (86400 / 24), "/");
                     setcookie('acctype', $User->GetAccType(), time() + (86400 / 24), "/"); // 86400 = 1 day, "/" = cookie is available in entire website
                     setcookie('password', $User->GetPassword(), time() + (86400 / 24), "/");
                     setcookie('fname', $User->GetFName(), time() + (86400 / 24), "/");
                     setcookie('lname', $User->GetLName(), time() + (86400 / 24), "/");
                     setcookie('dob', $User->GetDOB(), time() + (86400 / 24), "/");
                     setcookie('studentyear', $User->GetYear(), time() + (86400 / 24), "/");
                     setcookie('facultyrank', $User->GetRank(), time() + (86400 / 24), "/");
                     setcookie('squestion', $User->GetSQuestion(), time() + (86400 / 24), "/");
                     setcookie('sanswer', $User->GetSAnswer(), time() + (86400 / 24), "/");
                     setcookie('prevemail', $acctId, time() + (86400 / 24), "/"); //add uname of the user searched for
                }
                else
                {
                    //user not found, set cookie using original account id
                    setcookie('prevemail', $acctId, time() + (86400 / 24), "/");
                }

                //load edit account form with user info
                header("Location: ../public/EditAccountForm.php?edit");
            }
            else
            {
                UserSearchForm::Error(Constants::$UNAUTHORIZED);
            }
        }
        else
        {
            //invalid session
            //error_log("invalid session", 0);
            UserSearchForm::Error(Constants::$INVALID_SESSION);
        }
    }
}

?>
