<?php

//checks that data does not contain a specific vulnerability
interface VerificationStrategy
{
    public function IsSafe($data);
}

?>