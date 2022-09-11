<?php
require_once "DBConnector.php";

try {

    global $jsonArray;
    $CSInfo = new CSInfo(); //create course object

    //if previous search in CourseSearchControl.php found some data
    if (isset($_COOKIE['courseid']) && $_COOKIE['courseid'] != "-1") {

        if(isset($_COOKIE['courseid']) && $_COOKIE['courseid'] != " ")
            $CSInfo->SetCourseId($_COOKIE['courseid']);
        else
            $CSInfo->SetCourseId("defaultvalue!");

        if(isset($_COOKIE['coursename']) && $_COOKIE['coursename'] != " ")
            $CSInfo->SetCourseName($_COOKIE['coursename']);
        else
            $CSInfo->SetCourseName("defaultvalue!");
        
        
        if(isset($_COOKIE['semester']) && $_COOKIE['semester'] != " ")
            $CSInfo->SetSemester($_COOKIE['semester']);
        else
            $CSInfo->SetSemester("defaultvalue!");
        
        if(isset($_COOKIE['department']) && $_COOKIE['department'] != " ")
            $CSInfo->SetDepartment($_COOKIE['department']);
        else
            $CSInfo->SetDepartment("defaultvalue!");
        

        $results = DBConnector::searchCourse($CSInfo); //search course 

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
