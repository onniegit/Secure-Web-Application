<?php
try {
    /*Get DB connection*/
    require_once "../src/DBConnector.php";

    /*Get information from the search (post) request*/
    $acctype = $_POST['acctype'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $studentyear = $_POST['studentyear'];
    $facultyrank = $_POST['facultyrank'];

    if($acctype==null)
    {throw new Exception("input did not exist");}

    //handle blank values
    if ($fname === "") {
        $fname = "defaultvalue!";
    }
    if ($lname === "") {
        $lname = "defaultvalue!";
    }
    if ($dob === "") {
        $dob = "defaultvalue!";
    }
    if ($email === "") {
        $email = "defaultvalue!";
    }
    if ($studentyear === "") {
        $studentyear = "defaultvalue!";
    }
    if ($facultyrank === "") {
        $facultyrank = "defaultvalue!";
    }


    //determine account type
    if($acctype=="Student") {
        $results = DBConnector::usersearchstudent($db,$studentyear,$fname,$lname,$dob,$email);
    }
    elseif($acctype=="Faculty"){
        $results = DBConnector::usersearchfaculty($db,$facultyrank,$fname,$lname,$dob,$email);
    }
    else{
        $results = DBConnector::gensearch($db,$fname,$lname,$dob,$email,$facultyrank);
    }


    global $jsonArray;

    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $jsonArray[] = $row;
    }

    echo json_encode($jsonArray);
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


//note: since no changes happen to the database, it is not backed up on this page
?>
