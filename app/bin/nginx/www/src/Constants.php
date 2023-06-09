<?php

class Constants
{
    public static $PASSWORD_HASH = 'ripemd256'; //hash key for stored passwords

    //error codes
    public static $NONE = 0; // no error
    public static $INVALID_CREDENTIALS = 1; //error code - invalid login credentials
    public static $INVALID_INPUT = 2; //error code - invalid input format
    public static $INVALID_SESSION = 3; //error code - invalid session
    public static $UNAUTHORIZED = 4; //error code - unauthorized access
    public static $EDIT_FAILED = 5; //error code - failure trying to update user data
    public static $USER_NOT_FOUND = 6; //failure trying to get user data
    
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

    //input data type enumeration
    public static $LOGIN_TYPE = 1; //username and password data
    public static $USERNAME_TYPE = 2; //email format
    public static $PASSWORD_TYPE = 3; //password format
    public static $USER_TYPE = 4; //user data format
    public static $USER_SEARCH_TYPE = 5; //user search data format
    public static $CHAR_STRING_TYPE = 6; //character stirng data format
    public static $INT_TYPE = 7; //int data format
    public static $COURSE_SEARCH_TYPE = 8; //course search data format
    public static $GRADE_TYPE = 9; //course grade format (in CSV document)
    public static $STUDENT_ID_TYPE = 10; //student id format (in CSV document)
    public static $COURSE_ENROLL_TYPE = 11; //course enroll data format
    public static $SECTION_ID_TYPE = 12; //4 digit course section identifier
    public static $ENTER_GRADE_TYPE = 13; //course id and csv file
    public static $YEAR_TYPE = 14; //4-digit year
}

?>