<?php
session_start();
header('Content-type: text/csv');
header("Content-Disposition: attachment; filename=\"Journal.csv\"");
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
require dirname(__FILE__) . "/includes/classFormList2.php";
$formdata = require("./config/StateraForm.php");

$DB = new stateraDB($devt_environment->getDatabaseParameters());
$session = new Session($DB,"getSession","setSession");
$selff = trim($_SERVER["PHP_SELF"],"/");
if ($session->isAvailable())
    $user = $DB->getUserByRandId($session->Id());
if (!$user)
{
    $session->redirect = $selff;
    header("Location: Signin.php");
    exit();
}
if ($session->session_key)
    $_SESSION["session_key"] = $session->session_key;

$doneone = false;
$r = $DB->allJournal("","order by journal_date");
while ($j = $r->fetch_assoc())
{
    if (!$doneone)
    {
        $keys = array_keys($j);
        $line = "";
        foreach($keys as $key)
            $line .= "\"{$key}\",";
        trim($line,",");
        $line .= "\r\n";
        echo $line;
        $doneone = true;
    }
    $line = "";
    foreach($j as $k => $l)
    {
        if ($k == "journal_description" || $k == "journal_vendor_name" || $k == "journal_vendor_tax_number")
            $line .= "\"";
        $line .= "{$l}";
        if ($k == "journal_description" || $k == "journal_vendor_name" || $k == "journal_vendor_tax_number")
            $line .= "\"";
        $line .= ",";
    }
    trim($line,",");
    $line .= "\r\n";
    echo $line;
}

?>