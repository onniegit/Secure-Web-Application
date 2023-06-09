<?php
require_once "DBConnector.php";

try {

    global $jsonArray;

    //if previous search in UserSearchControl.php found some data
    if (isset($_COOKIE['acctype']) && $_COOKIE['acctype'] != null) {
        /*Get search information from the cookie*/

        $AccType = $_COOKIE['acctype'];
        $Year = "";
        $Rank = "";

        if(isset($_COOKIE['email']))
            $Email = $_COOKIE['email'];
        else
            $Email ="";
        
        
        if(isset($_COOKIE['fname']))
            $FName = $_COOKIE['fname'];
        else
            $FName = "";
        
        if(isset($_COOKIE['lname']))
            $LName = $_COOKIE['lname'];
        else
            $LName = "";
        
        if(isset($_COOKIE['dob']))
            $DOB = $_COOKIE['dob'];
        else
            $DOB = "";

        if ($AccType == "Student") {
            $Year = $_COOKIE['studentyear']; //only if student
        }
        else {
            $Rank = $_COOKIE['facultyrank']; //only if faculty, ensure null otherwise
        }

        $userdata = array($Email, $AccType, $FName, $LName, $DOB, $Year, $Rank);
        $results = DBConnector::searchUser($userdata); //search user    

        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $jsonArray[] = $row;
            //error_log("reading row", 0);
        }
    }
    
    echo json_encode($jsonArray);
}
catch (Exception $e) 
{
    //prepare page for content
    include_once "ErrorHeader.php";

    //Display error information
    echo 'Caught exception: ', $e->getMessage(), "<br>";
    var_dump($e->getTraceAsString());
    echo 'in ' . 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "<br>";

    $allVars = get_defined_vars();
    debug_zval_dump($allVars);
}

//note: since no changes happen to the database, it is not backed up on this page
?>
