<?php
require_once "../src/DBController.php";
require_once "../src/InputValidator.php";
require_once "../src/User.php";

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
        $stmt->bindParam(':un',$un, SQLITE3_TEXT);
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
            $User->SetYear($userinfo[7]);
            $User->SetRank($userinfo[8]);
            $User->SetSQuestion($userinfo[9]);
            $User->SetSAnswer($userinfo[10]);

            return $User;
        }

        else
        {
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
        if ($exists)
        {
            return true;
        }

        else return false;

    }

    public static function UsernameExists($un) // returns true if provided username exists within the db
    {
        $query = "SELECT * FROM User WHERE Email=:un";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':un',$un, SQLITE3_TEXT);
        $result = $stmt->execute();
        $exists = array();

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $exists[] = $row;
        }

        if ($exists)
        {
            return true;
        }

        else return false;
    }

    public static function TempUser($un) // returns a User object
    {
        $query = "SELECT * 
                    FROM User
                    INNER JOIN UserRole ON User.UserID = UserRole.uid
                    WHERE Email=:un";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':un',$un, SQLITE3_TEXT);
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

        $GLOBALS['db']->backup($db, "temp", $GLOBALS['dbPath']);
    }
}