<?php
session_start();
require './includes/classSecure.php';
require_once "./includes/classRolling.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
$DB = new stateraDB($devt_environment->getDatabaseParameters());

$session = new Session($DB,"getSession","setSession");
if ($session->isAvailable())
{
    $session->delete();
}
header("Location: Signin.php");
?>