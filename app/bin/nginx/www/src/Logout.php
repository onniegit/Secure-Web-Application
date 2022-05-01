<?php
require_once "../src/LogoutController.php";
try {
    
        LogoutController::Logout();
}
catch(Exception $e)
{
    //prepare page for content
    include_once "ErrorHeader.php";

    //Display error information
    echo 'Caught exception: ',  $e->getMessage(), "<br>";
}

?>
