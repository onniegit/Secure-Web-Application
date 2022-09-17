<?php

class Constants
{
    public static $PASSWORD_HASH = 'ripemd256'; //hash key for stored passwords

    //error codes
    public static $INVALID_CREDENTIALS = 1; //error code - invalid login credentials
    public static $INVALID_INPUT = 2; //error code - invalid input format
    public static $INVALID_SESSION = 3; //error code - invalid session
    public static $UNAUTHORIZED = 4; //error code - unauthorized access
    public static $EDIT_FAILED = 5; //error code - failure trying to update user data
    
    //Resource names
    public static $LOGINFORM_PHP = 'LoginForm.php';
    public static $CREATEACCOUTFORM_PHP = 'CreatAcctForm.php';
    public static $USERSEARCHFORM_PHP = 'UserSearchForm.php';
    public static $COURSESEARCHFORM_PHP = 'CourseSearchForm.php';
    public static $COURSEENROLLFORM_PHP = 'CourseEnrollForm.php';
    public static $EDITACCOUNTFORM_PHP = 'EditAccountForm.php';
    public static $ENTERGRADEFORM_PHP = 'EnterGradeForm.php';
    
    //Account types
    public static $ADMIN_TYPE = "1";
    public static $FACULTY_TYPE = "2";
    public static $STUDENT_TYPE = "3";
}

?>