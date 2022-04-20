<?php
require_once "../src/SessionController.php";
require_once "../src/InputValidator.php";


class RequestController extends SessionController
{
    use InputValidator;
    // extend this controller to access InputValidator and SessionController methods
}