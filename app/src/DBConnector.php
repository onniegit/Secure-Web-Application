<?php
require_once "../src/DBController.php";

class DBConnector
{
    function GetUser($un,$pw)
    {
        $hashpassword = hash('ripemd256', $pw);

        //prevents SQL injection through use of prepared statements
        $query = "SELECT COUNT(*) as count FROM User WHERE Email=:un AND Password=:hashpassword";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->bindParam(':un', $un);
        $stmt->bindPAram(':hashpassword', $hashpassword);
        $count = $stmt->execute();

        if ($count >= 1)
        {
            $query = "SELECT * FROM User WHERE Email=:un AND Password=:hashpassword";
            $stmt = $GLOBALS['db']->prepare($query);
            $stmt->bindParam(':un', $un);
            $stmt->bindPAram(':hashpassword', $hashpassword);
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