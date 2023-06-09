<?php
require_once "User.php";
require_once "DBConnector.php";
require_once "VerificationStrategy.php";
require_once "LoginTypeValidator.php";
require_once "PasswordTypeValidator.php";
require_once "EnterGradeTypeValidator.php";
require_once "CharStringTypeValidator.php";
require_once "CourseEnrollTypeValidator.php";
require_once "CourseSearchTypeValidator.php";
require_once "GradeTypeValidator.php";
require_once "IntTypeValidator.php";
require_once "SectionIdTypeValidator.php";
require_once "StudentIdTypeValidator.php";
require_once "UserTypeValidator.php";
require_once "UserSearchTypeValidator.php";
require_once "UsernameTypeValidator.php";
require_once "YearTypeValidator.php";
require_once "SQLiVerifier.php";
require_once "XssVerifier.php";

/**
 * Summary of SecurityTemplate
 */
abstract class SecurityTemplate
{
    public static function SecurityCheck($_user, $resource, $data, $dataType)
    {
        $security_flag = false; //be false by default

        if($_user != null && $_user[0] == null) //username (email) not specified
        {
            session_start(); //required to bring session variables into context
            if (isset($_SESSION['email']))
                $_user[0] = $_SESSION['email']; //get the username
        }
        
        if($_user != null) //if user array is not  null
        {
            $security_flag = self::Authenticate($_user); //check session

            if($security_flag == false)
                return false;
        }

        if($resource != null) //resource is specified
        {
            $security_flag = self::Authorize($_user[0], $resource); //check access rights

            if($security_flag == false)
                return false;
        }

        if($data != null && $dataType != null) //data and type are specified
        {
            $security_flag = self::DataCheck($data, $dataType); //check input data

            if($security_flag == false)
                return false;
        }

        return $security_flag;
    }

    public static function DataCheck($data, $dataType)
    {
        //error_log("checking data", 0);
        //error_log($dataType, 0);
        $correctData = false;

        $validFormat = self::Validate($data, $dataType);

        if($validFormat == true)
        {
            //error_log("format ok", 0);
            $correctData = self::Verify($data);
        }

        return $correctData;
    }

    public static function Verify($data)
    {
        $validData = false;

        //check for SQL injection
        $VerificationStrategy = new SQLiVerifier();
        $sqlSafe = $VerificationStrategy->IsSafe($data);
        
        if($sqlSafe == true)
        {
            //error_log("sql ok", 0);
            //check for xss 
            $VerificationStrategy = new XssVerifier();
            $xssSafe = $VerificationStrategy->IsSafe($data);

            if ($xssSafe == true) 
            {
                $validData = true;
                //error_log("xss ok", 0);

            }
        }
        return $validData;
    }

    public static function Validate($data, $dataType)
    {
        $validationStrategy = null;
        
        switch($dataType)
        {
            case Constants::$CHAR_STRING_TYPE:
                $validationStrategy = new CharStringTypeValidator();
                break;
            case Constants::$COURSE_ENROLL_TYPE:
                $validationStrategy = new CourseEnrollTypeValidator();
                break;
            case Constants::$COURSE_SEARCH_TYPE:
                $validationStrategy = new CourseSearchTypeValidator();
                break;
            case Constants::$ENTER_GRADE_TYPE:
                $validationStrategy = new EnterGradeTypeValidator();
                break;
            case Constants::$GRADE_TYPE:
                $validationStrategy = new GradeTypeValidator();
                break;
            case Constants::$INT_TYPE:
                $validationStrategy = new IntTypeValidator();
                break;
            case Constants::$LOGIN_TYPE:
                $validationStrategy = new LoginTypeValidator();
                break;
            case Constants::$PASSWORD_TYPE:
                $validationStrategy = new PasswordTypeValidator();
                break;
            case Constants::$SECTION_ID_TYPE:
                $validationStrategy = new SectionIdTypeValidator();
                break;
            case Constants::$STUDENT_ID_TYPE:
                $validationStrategy = new StudentIdTypeValidator();
                break;
            case Constants::$USER_TYPE:
                $validationStrategy = new UserTypeValidator();
                break;
            case Constants::$USER_SEARCH_TYPE:
                $validationStrategy = new UserSearchTypeValidator();
                break;
            case Constants::$USERNAME_TYPE:
                $validationStrategy = new UsernameTypeValidator();
                break;
            case Constants::$YEAR_TYPE:
                $validationStrategy = new YearTypeValidator();
                break;
            default:
        }

        if($validationStrategy != null)
            return $validationStrategy->IsValid($data);
        else
            return false;
    }

    /**
     * Summary of Authenticate
     * Check that user is logged with a valid session
     * @param mixed $_user
     * @return bool
     */
    public static function Authenticate($_user)
    {
        if(self::IsSetSession($_user[0]) == true)
        {
            /*
                create a new session ID and discard the old one, makes it more difficult 
                for an attacker to hijack a user's session.
                It's important to call the function after a user has been authenticated,
                to avoid an attacker guessing or stealing the session id of an authenticated user
            */
            //error_log("session is set", 0);
            session_regenerate_id();
            return true;
        }
        else if($_user[1] != null) //password is set
        {
            $username = $_user[0]; //username
            $password = $_user[1]; //password
            //error_log($username, 0);
            //error_log($password, 0);

            $User = DBConnector::GetAccount($_user);

            if ($User != null) 
            {
                //error_log("found user", 0);
                //check User's password matches
                $validPass = self::PasswordMatches($User, $password);

                if ($validPass)
                {
                    //error_log("password match", 0);
                    //create user session
                    self::CreateSession($username, $User->GetAccType());
                    return true;
                }
            }

        }
        
        return false;

        /*
            check if session variables are set
            a better session check which uses the DB is needed
        */
        //if (isset($_SESSION) AND isset($_SESSION['acctype']))     
            //return true; //a session exists
       // else
            //return false;
    }

    private static function PasswordMatches($User, $pw) // verifies correctness password
    {
        $userPword = $User->GetPassword();

        $hashedInputPword = hash(Constants::$PASSWORD_HASH, $pw);

        if ($hashedInputPword == $userPword) {
            return true;
        }
        else
            return false;
    }

    /**
     * Summary of Authorize
     * @param mixed $username
     * @param mixed $resource
     * @return bool
     */
    public static function Authorize($username, $resource) //check if user has rights to requested resource
    {
        $auth_flag = false;

        if ($username != null && $resource != null) 
        {
            if (DBConnector::GetRights($username, $resource) != null)
                $auth_flag = true;
        }

        return $auth_flag;
    }

    private static function CreateSession($uname, $acctype)
    {
            //error_log("creating session", 0);
            //error_log($uname, 0);
            session_destroy(); //destroy existing session
            session_start();
            $_SESSION['email'] = $uname;
            $_SESSION['acctype'] = $acctype;
            $_SESSION['logged_in'] = true;
            //error_log($_SESSION['email'], 0);
    }
    
    /**
     * Summary of IsSetSessionEmail
     * @param mixed $username
     * @return bool
     */
    public static function IsSetSession($username)
    {
        session_start(); //required to bring session variables into context

        if (!isset($_SESSION['email']) or (empty($_SESSION['email']))) //check that session exists and is nonempty
        {
            return false;
        }
        else if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) //check if logged in
        {
            //check if email matches user
            if($_SESSION['email'] == $username || $username == null)
                return true; //username is logged in
            else
                return false; //not logged in
        }
        else if($username == null)
            return true; //email is set not logged in

        return false;    
    }
}
?>