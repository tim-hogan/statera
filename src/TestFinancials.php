<?php
session_start();
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


//Create some trasnactions
//Sales

$date = new DateTime('2022-04-01');


$tax = $DB->getTaxRateForClassAndDate("GST",$date);
$amnt = LedgerAmount::createFromGoss(10.00,$tax->taxrate_rate);
if($xtn = $DB->saleCash('2022-04-01',"Cash sale",0,0,$amnt))
    $session->push("undo",$xtn);

$amnt = LedgerAmount::createFromGoss(76.00,$tax->taxrate_rate);
if ($xtn = $DB->saleOnAccount('2022-04-01',"Purchase goods",1,0,$amnt) )
    $session->push("undo",$xtn);

$balance_sheet = $DB->financialreport("2022-04-01","2023-03-31");

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>FORM</title>
</head>
<body>
    <div id="container">
        <h1>TEST FINACIALS</h1>
        <h2>DUMP OF BALANCE SHEET</h2>
        <p><?php var_dump($balance_sheet);?></p>
    </div>
</body>
</html>