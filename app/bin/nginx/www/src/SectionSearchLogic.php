<?php
require_once "DBConnector.php";

try {

    global $jsonArray;

    //if previous search in CourseEnrollControl.php found some data
    if(isset($_COOKIE['coursename']) && $_COOKIE['coursename'] != " ")
        $coursename = $_COOKIE['coursename'];

    if(isset($_COOKIE['semester']) && $_COOKIE['semester'] != " ")
        $semester = $_COOKIE['semester'];
            
    if(isset($_COOKIE['year']) && $_COOKIE['year'] != " ")
        $year = $_COOKIE['year'];
        
    $results = DBConnector::getSections($coursename, $semester, $year); //search course sections

    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $jsonArray[] = $row;
        //error_log("sections - getting row");
    }

    echo json_encode($jsonArray);
//note: since no changes happen to the database, it is not backed up on this page
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
?>
