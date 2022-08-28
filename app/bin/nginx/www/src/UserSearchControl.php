<?php
require_once "RequestController.php";
require_once "DBConnector.php";

class UserSearchControl extends RequestController
{
    public static function userSearch()
    {
        //error_log("authenticating..", 0);
        $validSession = UserSearchControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //error_log($un, 0);
            //check user is authorized for requested function
            $authorized = UserSearchControl::authorize($un, Constants::$USERSEARCHFORM_PHP);
            if($authorized)
            {
                header("Location: ../public/UserSearchForm.php");
            }
            else
            {
                //error_log("not authorized", 0);
            }
        }
        else
        {
            //invalid session
            //error_log("invalid session", 0);
        }
    }
    public static function submit($User)
    {
        $validSession = UserSearchControl::authenticateSession();
        if($validSession)
        {
            $un = $_SESSION['email'];
            //check user is authorized for requested function
            $authorized = UserSearchControl::authorize($un, Constants::$USERSEARCHFORM_PHP);
            if($authorized)
            {
                //validate user search data
                $validUser = UserSearchControl::ValidateUserSearch($User);

                if($validUser == true)
                {
                    $results = DBConnector::searchUser($User); //search user

                    //is true on success and false on failure
                    if($results->fetchArray(SQLITE3_ASSOC))
                    {
                        error_log("results found", 0);

                        /*store user search info in cookie for later retrieval and search in UserSearchLogic.php*/
                        setcookie('acctype', $User->GetAccType(), time() + (86400 / 24), "/"); // 86400 = 1 day, "/" = cookie is available in entire website
                        setcookie('fname', $User->GetFName(), time() + (86400 / 24), "/");
                        setcookie('lname', $User->GetLName(), time() + (86400 / 24), "/");
                        setcookie('dob', $User->GetDOB(), time() + (86400 / 24), "/");
                        setcookie('email', $User->GetEmail(), time() + (86400 / 24), "/");
                        setcookie('studentyear', $User->GetYear(), time() + (86400 / 24), "/");
                        setcookie('facultyrank', $User->GetRank(), time() + (86400 / 24), "/");
                    }
                    else
                    {
                        error_log("no results found", 0);
                        //set to null to prevent another search in UserSearchLogic.php
                        setcookie('acctype', null, time() + (86400 / 24), "/");
                    }
                    
                    //load search form to search and display any search results
                    header("Location: ../public/UserSearchForm.php?search");
                }
                else
                {
                    //invalid user
                    //error_log("invalid user", 0);
                    UserSearchForm::Error(Constants::$INVALID_INPUT);
                }
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
