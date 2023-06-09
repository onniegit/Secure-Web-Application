<?php

//checks that data matches its specification
interface ValidationStrategy
{
    public function IsValid($data);
}

?>