<?php

class Constants
{
    public static $INVALID_CREDENTIALS = 1; //error code - invalid login credentials
    public static $INVALID_INPUT = 2; //error code - invalid input format
    public static $PASSWORD_HASH = 'ripemd256'; //hash key for stored passwords
    
    //Resource names
    public static $LOGINFORM_PHP = 'LoginForm.php';
}

?>