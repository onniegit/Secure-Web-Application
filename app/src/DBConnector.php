<?php
require_once "../src/DBController.php";

class DBConnector
{
    function GetUser($un)
    {
        //prevents SQL injection through use of prepared statements
        $query = "SELECT COUNT(*) as count FROM User WHERE Email=:un";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':un', $un);
        $count = $stmt->execute();

        if ($count >= 1)
        {
            $query = "SELECT * FROM User WHERE Email=:un";
            $stmt = $GLOBALS['db']->prepare($query);
            $stmt->bindParam(':un', $un);
            $results = $stmt->execute();

            if ($results != false)
            {
                $userinfo = $results->fetchArray();
                $User = new User($userinfo[1],$userinfo[2],$userinfo[3]);
            }

            return $User;
        }

        else
        {
            header("Location: ../public/LoginForm.php?login=fail");
        }
    }
}