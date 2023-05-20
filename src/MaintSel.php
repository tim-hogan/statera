<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
$DB = new stateraDB($devt_environment->getDatabaseParameters());
$session = new Session($DB,"getSession","setSession");
if ($session->isAvailable())
    $user = $DB->getUserByRandId($session->Id());
if (!$user)
{
    $session->redirect = $selff;
    header("Location: Signin.php");
    exit();
}
$session->form_table = $_GET["t"];
header("Location: Maint.php");
?>