<?php
require_once "User.php";
require_once "Constants.php";

/*Ensures the database was initialized and obtain db link*/

$GLOBALS['dbPath'] = '../../db/persistentconndb.sqlite';
global $db;
$db = new SQLite3($GLOBALS['dbPath'], $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $encryptionKey = "");

$GLOBALS['backupDbPath'] = '../../db/backup.sqlite';
global $backupDb;
$backupDb = new SQLite3($GLOBALS['backupDbPath'], $flags = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $encryptionKey = "");

class DBConnector
// all database interaction is handled within this class
// prepared statements are used throughout to prevent SQL injection
{
    
    public static function updateUser($userdata)
    {
        $email = $userdata[0];
        $password = hash(Constants::$PASSWORD_HASH, $userdata[1]); //convert password to 80 byte hash using ripemd256 before saving
        $acctype = $userdata[2];
        $fname = $userdata[3];
        $lname = $userdata[4];
        $dob = $userdata[5];
        $studentyear = $userdata[6]; 
        $facultyrank = $userdata[7]; 
        $squestion = $userdata[8];
        $sanswer = $userdata[9];
        $prevemail = $userdata[10];
        
        /*Update the database with the new info*/
        $query = "UPDATE User 
        SET Email = :email, Password = :password, FName = :fname, LName = :lname, DOB = :dob, Year = :studentyear, Rank = :facultyrank, SQuestion = :squestion, SAnswer = :sanswer 
        WHERE Email = :prevemail";
        $stmt = $GLOBALS['db']->prepare($query); //prevents SQL injection by escaping SQLite characters
        $stmt->bindParam(':email', $email, SQLITE3_TEXT);
        $stmt->bindParam(':password', $password, SQLITE3_TEXT);
        $stmt->bindParam(':fname', $fname, SQLITE3_TEXT);
        $stmt->bindParam(':lname', $lname, SQLITE3_TEXT);
        $stmt->bindParam(':dob', $dob, SQLITE3_TEXT);
        $stmt->bindParam(':studentyear', $studentyear, SQLITE3_INTEGER);
        $stmt->bindParam(':facultyrank', $facultyrank, SQLITE3_TEXT);
        $stmt->bindParam(':squestion', $squestion, SQLITE3_TEXT);
        $stmt->bindParam(':sanswer', $sanswer, SQLITE3_TEXT);
        $stmt->bindParam(':prevemail', $prevemail, SQLITE3_TEXT);
        $results = $stmt->execute();

        if($results){//query to User table is successful
            $query = "SELECT * FROM User 
                    WHERE Email = :email";
            $stmt = $GLOBALS['db']->prepare($query); //prevents SQL injection by escaping SQLite characters
            $stmt->bindParam(':email', $email, SQLITE3_TEXT);
            $results = $stmt->execute();
        }

        if(($userinfo = $results->fetchArray()) !== null){//checks if rows exist
            $query = "UPDATE UserRole 
                    SET AccType = :acctype
                    WHERE :userid = uid";
            $stmt = $GLOBALS['db']->prepare($query); //prevents SQL injection by escaping SQLite characters
            $stmt->bindParam(':acctype', $acctype, SQLITE3_INTEGER);
            $stmt->bindParam(':userid', $userinfo[0], SQLITE3_INTEGER);
            $results = $stmt->execute();
        }

        //is true on success and false on failure
        if ($results)
        {
            //backup database
            $GLOBALS['db']->backup($GLOBALS['backupDb'], "temp", $GLOBALS['backupDbPath']);
        }

        return $results;
    }

    public static function GetAccount($data) // returns a User object from the database
    {
        $un = $data[0];
        //error_log($un, 0);
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

        if ($userinfo) 
        {
            $User = new User();
            $User->SetEmail($userinfo[1]);
            $User->SetAccType($userinfo[2]);
            $User->SetPassword($userinfo[3]);
            $User->SetFName($userinfo[4]);
            $User->SetLName($userinfo[5]);
            $User->SetDOB($userinfo[6]);
            
            if($User->GetAccType() == Constants::$STUDENT_TYPE)
            {
                $User->SetYear($userinfo[7]);
                $User->SetRank(" ");
            }
            elseif($User->GetAccType() == Constants::$FACULTY_TYPE)
            {
                $User->SetYear(" ");
                $User->SetRank($userinfo[8]);
            }
            
            $User->SetSQuestion($userinfo[9]);
            $User->SetSAnswer($userinfo[10]);

            return $User;
        }

        return null; 
    }

    public static function GetRights($un, $res) // returns true if provided username has access rights for requested resource
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
            return $exists;
        }

        else
            return null;

    }

    public static function IsSafeUser($un) // returns true if provided username exists within the db
    {
        $query = "SELECT * FROM User WHERE Email=:un";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':un', $un, SQLITE3_TEXT);
        $result = $stmt->execute();
        $exists = array();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $exists[] = $row;
        }

        if ($exists)
            return true;
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

    public static function UpdatePassword($un, $pw) // updates a user's password, backs up db

    {
        $query = "UPDATE User SET Password=:pw WHERE Email =:un";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':pw', $pw);
        $stmt->bindParam(':un', $un);
        $results = $stmt->execute();

        $GLOBALS['db']->backup($GLOBALS['backupDb'], "temp", $GLOBALS['backupDbPath']);
    }

    public static function SaveGrade($data) // input has been validated before this method is called
    {
        $crn = $data[0];

        $handle = fopen(($_FILES['file']['tmp_name']), "r"); //sets a read-only pointer at beginning of file
        $path = pathinfo($_FILES['file']['name']); //path info for file


        while (($data = fgetcsv($handle, 9001, ",")) !== FALSE) {
            $query = "INSERT INTO Grade VALUES ('$crn', '$data[0]', '$data[1]')"; //create query for db
            $GLOBALS['db']->exec($query);
        }

        $GLOBALS['db']->backup($GLOBALS['backupDb'], "temp", $GLOBALS['backupDbPath']);

        fclose($handle);
    }

    public static function usersearchstudent($studentyear, $fname, $lname, $dob, $email)
    {
        //send back student type search results

        $query = "SELECT * FROM User
                INNER JOIN UserRole ON User.UserID = UserRole.uid WHERE AccType=3 AND 
                (Fname LIKE :fname OR :fname = 'defaultvalue!') AND
                (Lname LIKE :lname OR :lname = 'defaultvalue!') AND
                (DOB LIKE :dob OR :dob = 'defaultvalue!') AND
                (Email LIKE :email OR :email = 'defaultvalue!') AND
                (Year LIKE :studentyear OR :studentyear = 'defaultvalue!')";
        $stmt = $GLOBALS['db']->prepare($query); //prevents SQL injection by escaping SQLite characters
        $stmt->bindParam(':studentyear', $studentyear, SQLITE3_INTEGER);
        $stmt->bindParam(':fname', $fname, SQLITE3_TEXT);
        $stmt->bindParam(':lname', $lname, SQLITE3_TEXT);
        $stmt->bindParam(':dob', $dob, SQLITE3_TEXT);
        $stmt->bindParam(':email', $email, SQLITE3_TEXT);
        return $results = $stmt->execute();
    }
    public static function usersearchfaculty($facultyrank, $fname, $lname, $dob, $email)
    {
        //send back faculty type search results

        $query = "SELECT * FROM User 
                INNER JOIN UserRole ON User.UserID = UserRole.uid WHERE AccType=2 AND 
                (Fname LIKE :fname OR :fname = 'defaultvalue!') AND
                (Lname LIKE :lname OR :lname = 'defaultvalue!') AND
                (DOB LIKE :dob OR :dob = 'defaultvalue!') AND
                (Email LIKE :email OR :email = 'defaultvalue!') AND
                (Rank LIKE :facultyrank OR :facultyrank = 'defaultvalue!')";
        $stmt = $GLOBALS['db']->prepare($query); //prevents SQL injection by escaping SQLite characters
        $stmt->bindParam(':facultyrank', $facultyrank, SQLITE3_TEXT);
        $stmt->bindParam(':fname', $fname, SQLITE3_TEXT);
        $stmt->bindParam(':lname', $lname, SQLITE3_TEXT);
        $stmt->bindParam(':dob', $dob, SQLITE3_TEXT);
        $stmt->bindParam(':email', $email, SQLITE3_TEXT);
        return $results = $stmt->execute();
    }

    public static function gensearch($fname, $lname, $dob, $email, $facultyrank)
    {
        //send back a general search (may change to exclude admins)

        $query = "SELECT * FROM User
                INNER JOIN UserRole ON User.UserID = UserRole.uid WHERE
                (Fname LIKE :fname OR :fname = 'defaultvalue!') AND
                (Lname LIKE :lname OR :lname = 'defaultvalue!') AND
                (DOB LIKE :dob OR :dob = 'defaultvalue!') AND
                (Email LIKE :email OR :email = 'defaultvalue!') AND
                (Rank LIKE :facultyrank OR :facultyrank = 'defaultvalue!')";
        $stmt = $GLOBALS['db']->prepare($query); //prevents SQL injection by escaping SQLite characters
        $stmt->bindParam(':fname', $fname, SQLITE3_TEXT);
        $stmt->bindParam(':lname', $lname, SQLITE3_TEXT);
        $stmt->bindParam(':dob', $dob, SQLITE3_TEXT);
        $stmt->bindParam(':email', $email, SQLITE3_TEXT);
        $stmt->bindParam(':facultyrank', $facultyrank, SQLITE3_TEXT);
        return $results = $stmt->execute();
    }

    public static function getSecQuestion($email)
    {
            /*Obtain security question from db*/
            $query = "SELECT SQuestion FROM User WHERE Email = '$email'";
            $secQ = $GLOBALS['db']->querySingle($query);

            return $secQ;
        
    }

    public static function getSecAnswer($email)
    {
            /*Obtain security answer from db*/
            $query = "SELECT SAnswer FROM User WHERE Email = '$email'";
            $secA = $GLOBALS['db']->querySingle($query);

            return $secA;
        
    }

    public static function enroll($email, $data)
    {
        try{

            $sectionId = $data[0];
            
            /*Obtain UserID from db*/
            $query = "SELECT UserID FROM User WHERE Email = '$email'";
            $userid = $GLOBALS['db']->querySingle($query);
        
            /*Enroll user into course*/
            $query = "INSERT INTO Enrollment
                            VALUES ('$sectionId','$userid')";
            $results = $GLOBALS['db']->exec($query);
        
            //is true on success and false on failure
            if($results)
            {
                //backup database
                $GLOBALS['db']->backup($GLOBALS['backupDb'], "temp", $GLOBALS['backupDbPath']);
            }

            return $results;
        }
        catch(Exception $e)
        {
            //prepare page for content
            include_once "ErrorHeader.php";
        
            //Display error information
            echo 'Caught exception: ',  $e->getMessage(), "<br>";
            var_dump($e->getTraceAsString());
            echo 'in '.'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']."<br>";
        
            $allVars = get_defined_vars();
            debug_zval_dump($allVars);
        }
    }

    public static function getSections($courseData)
    {
        try {

                $coursename = $courseData[0];
                $semester = $courseData[1];
                $year = $courseData[2];

                $query = "SELECT *
                    FROM Section
                    CROSS JOIN Course ON Section.Course = Course.Code
                    INNER JOIN User ON Section.Instructor = User.UserID
                    WHERE CourseName = '$coursename' AND Semester = '$semester' AND Section.Year = '$year'";
            
                $results = $GLOBALS['db']->query($query);
              
                return $results;
        }
        catch(Exception $e)
        {
            //prepare page for content
            include_once "ErrorHeader.php";
        
            //Display error information
            echo 'Caught exception: ',  $e->getMessage(), "<br>";
            var_dump($e->getTraceAsString());
            echo 'in '.'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']."<br>";
        
            $allVars = get_defined_vars();
            debug_zval_dump($allVars);
        }
    }

    public static function searchCourse($courseInfo)
    {
        try 
        {
                /*Get information from the search request*/
                $courseid = $courseInfo[0];
                $coursename = $courseInfo[1];
                $semester = $courseInfo[2];
                $department = $courseInfo[3]; 

                if($courseid == "")
                    $courseid = "defaultvalue!"; 
                if($coursename == "")
                    $coursename = "defaultvalue!";
                if($semester == "")
                    $semester = "defaultvalue!"; 
                if($department == "")
                    $department = "defaultvalue!";

                $query = "	SELECT Section.CRN, Course.CourseName, Section.Year, Section.Semester, User.Email, Section.Location
                FROM Section
                CROSS JOIN Course ON Section.Course = Course.Code
                INNER JOIN User ON Section.Instructor = User.UserID
                WHERE (CRN LIKE '$courseid' OR '$courseid'='defaultvalue!') AND
                        (Semester LIKE '$semester' OR '$semester'='defaultvalue!') AND
                        (Course LIKE '$department' OR '$department'='defaultvalue!') AND
                        (CourseName LIKE '$coursename' OR '$coursename' = 'defaultvalue!')";

                $results = $GLOBALS['db']->query($query);
                
                return $results;
        }
        catch(Exception $e)
        {
            //prepare page for content
            include_once "ErrorHeader.php";
        
            //Display error information
            echo 'Caught exception: ',  $e->getMessage(), "<br>";
            var_dump($e->getTraceAsString());
            echo 'in '.'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']."<br>";
        
            $allVars = get_defined_vars();
            debug_zval_dump($allVars);
        }
    }

    public static function searchUser($userdata)
    {
        try {
            /*Get information from the search request*/
            $email = $userdata[0];
            $acctype = $userdata[1];
            $fname = $userdata[2];
            $lname = $userdata[3];
            $dob = $userdata[4]; //is already UTC
            $studentyear = $userdata[5]; //only if student, ensure null otherwise (must be a number)
            $facultyrank = $userdata[6]; //only if faculty, ensure null otherwise

            if($acctype==null)
            {
                error_log("no search input", 0);
                return;
            }

            //error_log($acctype, 0);

            //handle blank values
            if ($fname == "") {
                $fname = "defaultvalue!";
            }
            if ($lname == "") {
                $lname = "defaultvalue!";
            }
            if ($dob == "") {
                $dob = "defaultvalue!";
            }
            if ($email == "") {
                $email = "defaultvalue!";
            }
            if ($studentyear == "") {
                $studentyear = "defaultvalue!";
            }
            if ($facultyrank == "") {
                $facultyrank = "defaultvalue!";
            }

            //determine account type
            if($acctype=="Student") {
                $results = DBConnector::usersearchstudent($studentyear,$fname,$lname,$dob,$email);
            }
            elseif($acctype=="Faculty"){
                error_log($facultyrank, 0);
                error_log($fname, 0);
                error_log($lname, 0);
                error_log($dob, 0);
                error_log($email, 0);
                $results = DBConnector::usersearchfaculty($facultyrank,$fname,$lname,$dob,$email);
            }
            else{
                $results = DBConnector::gensearch($fname,$lname,$dob,$email,$facultyrank);
            }
              
            return $results;
        }
        catch(Exception $e)
        {
            //prepare page for content
            include_once "ErrorHeader.php";
        
            //Display error information
            echo 'Caught exception: ',  $e->getMessage(), "<br>";
            var_dump($e->getTraceAsString());
            echo 'in '.'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']."<br>";
        
            $allVars = get_defined_vars();
            debug_zval_dump($allVars);
        }
    }
    
    public static function createUser($data)
    {
        try 
        {
            /*Get information from the search request*/
            $email = $data[0];
            $password = hash(Constants::$PASSWORD_HASH, $data[1]); //convert password to 80 byte hash using ripemd256 before saving
            $acctype = $data[2];
            $fname = $data[3];
            $lname = $data[4];
            $dob = $data[5]; //is already UTC
            $studentyear = $data[6]; //only if student, ensure null otherwise (must be a number)
            $facultyrank = $data[7]; //only if faculty, ensure null otherwise
            $squestion = $data[8];
            $sanswer = $data[9];
        
            /*Check for a valid UserID to use. Assumes Users count in order*/
            $rows = $GLOBALS['db']->query("SELECT COUNT(*) as count FROM User");
            $row = $rows->fetchArray();
            $newUserID = $row['count'] + 927000000; //must always be 1 higher than previous
        
            /*Check if user already exists*/
            $query = "SELECT Email FROM User WHERE Email = :email";
            $stmt = $GLOBALS['db']->prepare($query); //prevents SQL injection by escaping SQLite characters
            $stmt->bindValue(':email', $email);
            $results = $stmt->execute();
        
            if ($results) //user doesn't already exist
            {
                /*Update the database with the new info*/
                $query = "INSERT INTO User VALUES (:newUserID, :email, :password, :fname, :lname, :dob, :studentyear, :facultyrank, :squestion, :sanswer)";
                $stmt = $GLOBALS['db']->prepare($query); //prevents SQL injection by escaping SQLite characters
                $stmt->bindParam(':newUserID', $newUserID, SQLITE3_INTEGER);
                $stmt->bindParam(':email', $email, SQLITE3_TEXT);
                $stmt->bindParam(':password', $password, SQLITE3_TEXT);
                $stmt->bindParam(':fname', $fname, SQLITE3_TEXT);
                $stmt->bindParam(':lname', $lname, SQLITE3_TEXT);
                $stmt->bindParam(':dob', $dob, SQLITE3_TEXT);
                $stmt->bindParam(':studentyear', $studentyear, SQLITE3_INTEGER);
                $stmt->bindParam(':facultyrank', $facultyrank, SQLITE3_TEXT);
                $stmt->bindParam(':squestion', $squestion, SQLITE3_TEXT);
                $stmt->bindParam(':sanswer', $sanswer, SQLITE3_TEXT);
                global $results;
                $results = $stmt->execute();
        
                if($results){//query to User table is successful
                $query = "INSERT INTO UserRole VALUES (:newUserID, :acctype)";
                $stmt = $GLOBALS['db']->prepare($query); //prevents SQL injection by escaping SQLite characters
                $stmt->bindParam(':newUserID', $newUserID, SQLITE3_INTEGER);
                $stmt->bindParam(':acctype', $acctype, SQLITE3_INTEGER);
                $results = $stmt->execute();
                }
            }
        
            //is true on success and false on failure (can fail in either query)
            if (!$results) {
                throw new Exception("Create account failed");
            } else {
                //backup database
                $GLOBALS['db']->backup($GLOBALS['backupDb'], "temp", $GLOBALS['backupDbPath']);
                //redirect
                header("Location: ../public/dashboard.php");
            }
        }
        catch(Exception $e)
        {
            //prepare page for content
            include_once "ErrorHeader.php";
        
            //Display error information
            echo 'Caught exception: ',  $e->getMessage(), "<br>";
            var_dump($e->getTraceAsString());
            echo 'in '.'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']."<br>";
        
            $allVars = get_defined_vars();
            debug_zval_dump($allVars);
        }
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
    }
}