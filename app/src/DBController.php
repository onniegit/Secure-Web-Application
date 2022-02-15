<?php
    /*Ensures the database was initialized and obtain db link*/
    $GLOBALS['dbPath'] = '../db/persistentconndb.sqlite';
    global $db;
    $db = new SQLite3($GLOBALS['dbPath'], $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $encryptionKey = "");

    function ValidateUser($un,$pw)
    {
    //convert password to 80 byte hash using ripemd256 before comparing
     $hashpassword = hash('ripemd256', $pw); 
 
     $myusername = strtolower($un); //makes username noncase-sensitive

    //query for count
     $query = "SELECT COUNT(*) as count FROM User WHERE Email='$myusername' AND Password='$hashpassword'";
     $count = $GLOBALS['db']->querySingle($query);

    //query for the row(s)
     $query = "SELECT * FROM User WHERE Email='$myusername' AND Password='$hashpassword'";
     $results = $GLOBALS['db']->query($query);

     if ($results != false AND ($userinfo = $results->fetchArray()) != (null OR false))
     {
        $GLOBALS['acctype'] = $userinfo[2];
         return true;
     }

     else return false;
    }
?>