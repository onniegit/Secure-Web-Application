<?php
require_once "SecurityTemplate.php";
require_once "DBConnector.php";
require_once "LogoutController.php";

class UserSearchControl extends SecurityTemplate
{
    public static function UserSearch()
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$USERSEARCHFORM_PHP, null, null);

        if($secResult === true)
        {
            header("Location: ../public/UserSearchForm.php"); //display search form
        }
        else
        {
            //check the error code, display appropriate page
            switch ($secResult)
            {
                case Constants::$INVALID_SESSION:

                case Constants::$UNAUTHORIZED:

                default:
                    LogoutController::Logout();  //initiate logout on invalid session || unauthorized
            }
        }
    }

    public static function Submit($data, $dataType = 5) //Constants::$USER_SEARCH_TYPE = 5
    {
        $secResult = self::SecurityCheck(array(null, null), Constants::$USERSEARCHFORM_PHP, $data, $dataType);

        if($secResult === true)
        {
            $results = DBConnector::SearchUser($data); //search user

            //is true on success and false on failure
            if($results->fetchArray(SQLITE3_ASSOC))
            {
                error_log("results found", 0);

                /*store user search info in cookie for later retrieval and search in UserSearchLogic.php*/
                setcookie('email', $data[0], time() + (86400 / 24), "/");
                setcookie('acctype', $data[1], time() + (86400 / 24), "/"); // 86400 = 1 day, "/" = cookie is available in entire website
                setcookie('fname', $data[2], time() + (86400 / 24), "/");
                setcookie('lname', $data[3], time() + (86400 / 24), "/");
                setcookie('dob', $data[4], time() + (86400 / 24), "/");
                setcookie('studentyear', $data[5], time() + (86400 / 24), "/");
                setcookie('facultyrank', $data[6], time() + (86400 / 24), "/");
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

            //check the error code, display appropriate page
            switch ($secResult)
            {
                case Constants::$INVALID_SESSION:
                    UserSearchForm::Error(Constants::$INVALID_SESSION);
                    break;
                case Constants::$UNAUTHORIZED:
                    // do something
                    break;
                case Constants::$INVALID_INPUT:
                    UserSearchForm::Error(Constants::$INVALID_INPUT);
                    break;
                default:
                    break;
            }
        }
    }
}

?>
