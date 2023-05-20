<?php
session_start();
require './includes/classSecure.php';
require dirname(__FILE__) . "/includes/classInputParam.php";
require_once "./includes/classRolling.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
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
$company= $DB->getCompany();
if (! $company)
{
    header("Location: Wizard.php");
    exit();
}

$salecash = InputParam::encryptFromString("w=cash",$session->session_key);
$saleaccount = InputParam::encryptFromString("w=account",$session->session_key);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>HOME</title>
    <link href="css/base.css" rel="stylesheet" />
    <link href="css/heading.css" rel="stylesheet" />
    <link href="css/footer.css" rel="stylesheet" />
    <style>
        #main {padding: 20px;}
        div.links {padding: 10px; border: solid 1px #aaa;margin-bottom: 10px;}
        .linkscontainer {display: flex;}
        div.links h2 {font-family: Akshar; font-weight: normal;margin-top: 0; color: #666;}
        div.link {margin-right: 12px;text-align: center; vertical-align: middle;padding: 10px; width: 100px; height: 40px; border: solid 1px #888; border-radius: 5px;color: white;background: linear-gradient(0deg, rgba(165,165,249,1) 0%, rgba(107,107,167,1) 100%);cursor: pointer;}
        div.link:hover {color: #ffff00;background: linear-gradient(0deg, rgba(107,107,167,1) 0%, rgba(165,165,249,1) 100%);}
        h1 {color: #6b6ba7;font-family: Akshar; font-weight: 300;}
    </style>
    <script>
        function sel(n) {
            window.location = n.getAttribute("href");
        }
    </script>
</head>
<body>
    <div id="container">
        <?php include ("./includes/heading.html");?>
        <div id="main">
            <h1>HOME - IT ALL STARTS HERE</h1>
            <div id="links">
                <div class="links">
                    <h2>QUICK LINKS</h2>
                    <div class="linkscontainer">
                        <div class="link" href="Sale.php?v=<?php echo $salecash;?>" onclick="sel(this)">CASH SALE</div>
                        <div class="link" href="Sale.php?v=<?php echo $saleaccount;?>" onclick="sel(this)">SALE ON ACCOUNT</div>
                        <div class="link" href="Expenses.php" onclick="sel(this)">EXPENSES</div>
                    </div>
                </div>
                <div class="links">
                    <h2>ACCOUNTS</h2>
                    <div class="linkscontainer">
                        <div class="link" href="AccountsPayable.php" onclick="sel(this)">ACCOUNTS PAYABLE</div>
                        <div class="link" href="AccountsReceivable.php" onclick="sel(this)">ACCOUNTS RECEIVABLE</div>
                        <div class="link" href="BankAccounts.php" onclick="sel(this)">BANK ACCOUNTS</div>
                        <div class="link" href="GSTReport.php" onclick="sel(this)">GST REPORT</div>
                        <div class="link" href="FinancialStatements.php" onclick="sel(this)">FINANCIAL STATEMENTS</div>
                        <div class="link" href="JournalDump.php" onclick="sel(this)">JOURNAL DUMP</div>
                    </div>
                </div>
                <div class="links">
                    <h2>FUNCTONS</h2>
                    <div class="linkscontainer">
                        <div class="link" href="MaintSel.php?t=account" onclick="sel(this)">CUSTOMERS</div>
                        <div class="link" href="Invoices.php" onclick="sel(this)">INVOICES</div>
                        <div class="link" href="ShareIssue.php" onclick="sel(this)">ISSUE SHARES</div>
                        <div class="link" href="PayTax.php" onclick="sel(this)">PAY TAX</div>
                    </div>
                </div>
                <div class="links">
                    <h2>TABLE MAINTENANCE</h2>
                    <div class="linkscontainer">
                        <div class="link" href="MaintSel.php?t=company" onclick="sel(this)">COMPANY</div>
                        <div class="link" href="MaintSel.php?t=product" onclick="sel(this)">PRODUCTS AND SERVICES</div>
                        <div class="link" href="MaintSel.php?t=taxclass" onclick="sel(this)">TAX TYPES</div>
                        <div class="link" href="MaintSel.php?t=taxrate" onclick="sel(this)">TAX RATES</div>
                        <div class="link" href="MaintSel.php?t=chart" onclick="sel(this)">CHART OF ACCOUNTS</div>
                        <div class="link" href="MaintSel.php?t=shareholder" onclick="sel(this)">SHARE HOLDERS</div>
                        <div class="link" href="MaintSel.php?t=staff" onclick="sel(this)">STAFF</div>
                    </div>
                </div>
                <div class="links">
                    <h2>REPORTS</h2>
                    <div class="linkscontainer">
                        <div class="link" href="ExpenseSummary.php" onclick="sel(this)">EXPENSE SUMMARY</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include ("./includes/footer.html");?>
</body>
</html>

