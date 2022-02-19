<?php
require_once "../src/DBController.php";

class DBConnector
{
    function GetUser($un)
    {
        $query = "SELECT COUNT(*) as count FROM User WHERE Email='$un'";
        $count = $GLOBALS['db']->querySingle($query);

        if ($count >= 1)
        {
            $query = "SELECT * FROM User WHERE Email='$un'";
            $results = $GLOBALS['db']->query($query);

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