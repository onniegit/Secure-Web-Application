<?php
require_once "DBConnector.php";

try {

    global $jsonArray;
    $User = new User(); //create user object

    //if previous search in UserSearchControl.php found some data
    if (isset($_COOKIE['acctype']) && $_COOKIE['acctype'] != null) {
        /*Get search information from the cookie*/

        $User->SetAccType($_COOKIE['acctype']);

        if(isset($_COOKIE['email']))
            $User->SetEmail($_COOKIE['email']);
        else
            $User->SetEmail("");
        
        
        if(isset($_COOKIE['fname']))
            $User->SetFName($_COOKIE['fname']);
        else
            $User->SetFName("");
        
        if(isset($_COOKIE['lname']))
            $User->SetLName($_COOKIE['lname']);
        else
            $User->SetLName("");
        
        if(isset($_COOKIE['dob']))
            $User->SetDOB($_COOKIE['dob']);
        else
            $User->SetDOB("");

        if ($User->GetAccType() === "Student") {
            $User->SetYear($_COOKIE['studentyear']); //only if student
        }
        else {
            $User->SetRank($_COOKIE['facultyrank']); //only if faculty, ensure null otherwise
        }

        $results = DBConnector::searchUser($User); //search user    

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
