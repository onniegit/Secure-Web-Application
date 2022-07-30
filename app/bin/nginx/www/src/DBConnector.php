<?php
require_once "User.php";
require_once "Constants.php";

/*Ensures the database was initialized and obtain db link*/

$GLOBALS['dbPath'] = '../../db/persistentconndb.sqlite';
global $db;
$db = new SQLite3($GLOBALS['dbPath'], $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $encryptionKey = "");

class DBConnector
// all database interaction is handled within this class
// prepared statements are used throughout to prevent SQL injection
{
    public static function GetUser($un) // returns a User object from the database

    {
        $query = "SELECT * 
                    FROM User
                    INNER JOIN UserRole ON User.UserID = UserRole.uid
                    WHERE Email=:un";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':un', $un, SQLITE3_TEXT);
        $result = $stmt->execute();
        $userinfo = array();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            array_push($userinfo, $row['UserID'], $row['Email'], $row['AccType'], $row['Password'], $row['FName'], $row['LName'], $row['DOB'],
                $row['Year'], $row['Rank'], $row['SQuestion'], $row['SAnswer']);
        }

        if ($userinfo) {
            $User = new User();
            $User->SetEmail($userinfo[1]);
            $User->SetAccType($userinfo[2]);
            $User->SetPassword($userinfo[3]);
            $User->SetFName($userinfo[4]);
            $User->SetLName($userinfo[5]);
            $User->SetDOB($userinfo[6]);
            $User->SetYear($userinfo[7]);
            $User->SetRank($userinfo[8]);
            $User->SetSQuestion($userinfo[9]);
            $User->SetSAnswer($userinfo[10]);

            return $User;
        }

        else {
            header("Location: ../public/LoginForm.php?login=fail");
        }
    }

    public static function CheckRights($un, $res) // returns true if provided username has access rights for requested resource

    {
        $query = "SELECT * 
                    FROM User
                    INNER JOIN UserRole ON User.UserID = UserRole.uid
                    WHERE Email=:un";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':un', $un, SQLITE3_TEXT);
        $result = $stmt->execute();
        $userinfo = array();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            array_push($userinfo, $row['UserID'], $row['Email'], $row['AccType'], $row['Password'], $row['FName'], $row['LName'], $row['DOB'],
                $row['Year'], $row['Rank'], $row['SQuestion'], $row['SAnswer']);
        }

        $query = "SELECT * FROM AccessRight
                  INNER JOIN Resource
                  WHERE RoleId = :acctype
                  AND ResourceID = rid
                  AND ResourceName = :res";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':acctype', $userinfo[2], SQLITE3_INTEGER);
        $stmt->bindParam(':res', $res, SQLITE3_TEXT);
        $result = $stmt->execute();
        $exists = array();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $exists[] = $row;
        }

        // if there is an access right for the given role
        if ($exists) {
            return true;
        }

        else
            return false;

    }

    public static function UsernameExists($un) // returns true if provided username exists within the db

    {
        $query = "SELECT * FROM User WHERE Email=:un";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':un', $un, SQLITE3_TEXT);
        $result = $stmt->execute();
        $exists = array();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $exists[] = $row;
        }

        if ($exists) {
            return true;
        }

        else
            return false;
    }

    public static function TempUser($un) // returns a User object

    {
        $query = "SELECT * 
                    FROM User
                    INNER JOIN UserRole ON User.UserID = UserRole.uid
                    WHERE Email=:un";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':un', $un, SQLITE3_TEXT);
        $result = $stmt->execute();
        $userinfo = array();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            array_push($userinfo, $row['UserID'], $row['Email'], $row['AccType'], $row['Password'], $row['FName'], $row['LName'], $row['DOB'],
                $row['Year'], $row['Rank'], $row['SQuestion'], $row['SAnswer']);
        }

        $User = new User();
        $User->SetEmail($userinfo[1]);
        $User->SetAccType($userinfo[2]);
        $User->SetPassword($userinfo[3]);
        $User->SetFName($userinfo[4]);
        $User->SetLName($userinfo[5]);
        $User->SetDOB($userinfo[6]);
        $User->SetYear($userinfo[7]);
        $User->SetRank($userinfo[8]);
        $User->SetSQuestion($userinfo[9]);
        $User->SetSAnswer($userinfo[10]);

        return $User;
    }

    public static function UpdatePasswordDB($un, $pw) // updates a user's password, backs up db

    {
        $query = "UPDATE User SET Password=:pw WHERE Email =:un";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':pw', $pw);
        $stmt->bindParam(':un', $un);
        $results = $stmt->execute();

        $GLOBALS['db']->backup($GLOBALS['db'], "temp", $GLOBALS['dbPath']);
    }

    public static function SaveGrade($crn) // input has been validated before this method is called

    {
        $handle = fopen(($_FILES['file']['tmp_name']), "r"); //sets a read-only pointer at beginning of file
        $path = pathinfo($_FILES['file']['name']); //path info for file


        while (($data = fgetcsv($handle, 9001, ",")) !== FALSE) {
            $query = "INSERT INTO Grade VALUES ('$crn', '$data[0]', '$data[1]')"; //create query for db
            $GLOBALS['db']->exec($query);
        }

        $GLOBALS['db']->backup($GLOBALS['db'], "temp", $GLOBALS['dbPath']);

        fclose($handle);
    }

    function usersearchstudent($db, $studentyear, $fname, $lname, $dob, $email)
    {
        //send back student type search results

        $query = "SELECT * FROM User
                INNER JOIN UserRole ON User.UserID = UserRole.uid WHERE AccType=3 AND 
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
    function usersearchfaculty($db, $facultyrank, $fname, $lname, $dob, $email)
    {
        //send back faculty type search results

        $query = "SELECT * FROM User 
                INNER JOIN UserRole ON User.UserID = UserRole.uid WHERE AccType=2 AND 
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

    function gensearch($db, $fname, $lname, $dob, $email, $facultyrank)
    {
        //send back a general search (may change to exclude admins)

        $query = "SELECT * FROM User
                INNER JOIN UserRole ON User.UserID = UserRole.uid WHERE
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

    public static function clearDB()
    {
        $query = "DROP TABLE User";
        $stmt = $GLOBALS['db']->prepare($query);
        try {

            $result = $stmt->execute();
            if (!$result)
                error_log($GLOBALS['db']->lastErrorMsg(), 0);
        }
        catch (Exception $e) {
            error_log($GLOBALS['db']->lastErrorMsg(), 0);
        }

        try {
            $query = "DROP TABLE Section";
            $stmt = $GLOBALS['db']->prepare($query);
            $stmt->execute();
        }
        catch (Exception $e) {
            error_log($GLOBALS['db']->lastErrorMsg(), 0);
        }

        try {
            $query = "DROP TABLE Enrollment";
            $stmt = $GLOBALS['db']->prepare($query);
            $stmt->execute();
        }
        catch (Exception $e) {
            error_log($GLOBALS['db']->lastErrorMsg(), 0);
        }

        try {
            $query = "DROP TABLE Grade";
            $stmt = $GLOBALS['db']->prepare($query);
            $stmt->execute();
        }
        catch (Exception $e) {
            error_log($GLOBALS['db']->lastErrorMsg(), 0);
        }

        try {
            $query = "DROP TABLE Course";
            $stmt = $GLOBALS['db']->prepare($query);
            $stmt->execute();
        }
        catch (Exception $e) {
            error_log($GLOBALS['db']->lastErrorMsg(), 0);
        }

        try {
            $query = "DROP TABLE Role";
            $stmt = $GLOBALS['db']->prepare($query);
            $stmt->execute();
        }
        catch (Exception $e) {
            error_log($GLOBALS['db']->lastErrorMsg(), 0);
        }

        try {
            $query = "DROP TABLE Resource";
            $stmt = $GLOBALS['db']->prepare($query);
            $stmt->execute();
        }
        catch (Exception $e) {
            error_log($GLOBALS['db']->lastErrorMsg(), 0);
        }

        try {
            $query = "DROP TABLE UserRole";
            $stmt = $GLOBALS['db']->prepare($query);
            $stmt->execute();
        }
        catch (Exception $e) {
            error_log($GLOBALS['db']->lastErrorMsg(), 0);
        }

        try {
            $query = "DROP TABLE AccessRight";
            $stmt = $GLOBALS['db']->prepare($query);
            $stmt->execute();
        }
        catch (Exception $e) {
            error_log($GLOBALS['db']->lastErrorMsg(), 0);
        }
    //$GLOBALS['db']->close();

    //$GLOBALS['dbPath'] = 'bin/nginx/db/persistentconndb.sqlite';

    //if (file_exists($GLOBALS['dbPath'])) {
    //    unlink($GLOBALS['dbPath']);
    //}
    }
}