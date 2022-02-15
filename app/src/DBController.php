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
    function usersearchstudent($db,$studentyear,$fname,$lname,$dob,$email)
    {
         {
            //send back student type search results
    
            $query = "SELECT * FROM User WHERE AccType=3 AND 
                (Fname LIKE :fname OR :fname = 'defaultvalue!') AND
                (Lname LIKE :lname OR :lname = 'defaultvalue!') AND
                (DOB LIKE :dob OR :dob = 'defaultvalue!') AND
                (Email LIKE :email OR :email = 'defaultvalue!') AND
                (Year LIKE :studentyear OR :studentyear = 'defaultvalue!')";
            $stmt = $db->prepare($query); //prevents SQL injection by escaping SQLite characters
            $stmt->bindParam(':studentyear', $studentyear, SQLITE3_INTEGER);
            $stmt->bindParam(':fname', $fname, SQLITE3_TEXT);
            $stmt->bindParam(':lname', $lname, SQLITE3_TEXT);
            $stmt->bindParam(':dob', $dob, SQLITE3_TEXT);
            $stmt->bindParam(':email', $email, SQLITE3_TEXT);
            return $results = $stmt->execute();
        }
    }
        function usersearchfaculty($db,$facultyrank,$fname,$lname,$dob,$email){
            //send back faculty type search results
    
            $query = "SELECT * FROM User WHERE AccType=2 AND 
                (Fname LIKE :fname OR :fname = 'defaultvalue!') AND
                (Lname LIKE :lname OR :lname = 'defaultvalue!') AND
                (DOB LIKE :dob OR :dob = 'defaultvalue!') AND
                (Email LIKE :email OR :email = 'defaultvalue!') AND
                (Rank LIKE :facultyrank OR :facultyrank = 'defaultvalue!')";
            $stmt = $db->prepare($query); //prevents SQL injection by escaping SQLite characters
            $stmt->bindParam(':facultyrank', $facultyrank, SQLITE3_TEXT);
            $stmt->bindParam(':fname', $fname, SQLITE3_TEXT);
            $stmt->bindParam(':lname', $lname, SQLITE3_TEXT);
            $stmt->bindParam(':dob', $dob, SQLITE3_TEXT);
            $stmt->bindParam(':email', $email, SQLITE3_TEXT);
            return $results = $stmt->execute();
        }
        function gensearch($db,$fname,$lname,$dob,$email,$facultyrank) {
            //send back a general search (may change to exclude admins)
    
            $query = "SELECT * FROM User WHERE
                (Fname LIKE :fname OR :fname = 'defaultvalue!') AND
                (Lname LIKE :lname OR :lname = 'defaultvalue!') AND
                (DOB LIKE :dob OR :dob = 'defaultvalue!') AND
                (Email LIKE :email OR :email = 'defaultvalue!') AND
                (Rank LIKE :facultyrank OR :facultyrank = 'defaultvalue!')";
            $stmt = $db->prepare($query); //prevents SQL injection by escaping SQLite characters
            $stmt->bindParam(':fname', $fname, SQLITE3_TEXT);
            $stmt->bindParam(':lname', $lname, SQLITE3_TEXT);
            $stmt->bindParam(':dob', $dob, SQLITE3_TEXT);
            $stmt->bindParam(':email', $email, SQLITE3_TEXT);
            $stmt->bindParam(':facultyrank', $facultyrank, SQLITE3_TEXT);
            return $results = $stmt->execute();
        }
    
?>