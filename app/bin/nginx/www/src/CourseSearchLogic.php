<?php
require_once "DBConnector.php";

try {

    global $jsonArray;

    //if previous search in CourseSearchControl.php found some data
    if (isset($_COOKIE['courseid']) && $_COOKIE['courseid'] != "-1") {

        /*Get information from the cookie*/

        if(isset($_COOKIE['courseid']) && $_COOKIE['courseid'] != " ")
            $CourseId = $_COOKIE['courseid']; 
        else
            $CourseId = "defaultvalue!";

        if(isset($_COOKIE['coursename']) && $_COOKIE['coursename'] != " ")
            $CourseName = $_COOKIE['coursename'];
        else
            $CourseName = "defaultvalue!";
        
    
        if(isset($_COOKIE['semester']) && $_COOKIE['semester'] != " ")
            $Semester = $_COOKIE['semester'];
        else
            $Semester = "defaultvalue!";
        
        if(isset($_COOKIE['department']) && $_COOKIE['department'] != " ")
            $Department = $_COOKIE['department'];
        else
            $Department = "defaultvalue!";
        
        $data = array(Constants::$COURSE_SEARCH_TYPE,$CourseId, $CourseName, $Semester, $Department);

        $results = DBConnector::searchCourse($data); //search course 

        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $jsonArray[] = $row;
        }
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
