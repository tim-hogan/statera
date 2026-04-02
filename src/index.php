<?php
session_start();
require './includes/classSecure.php';
require dirname(__FILE__) . "/includes/classInputParam.php";
require_once "./includes/classRolling.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
$DB = new stateraDB($devt_environment->getDatabaseParameters());
$session = new Session($DB,"getSession","setSession");
$selff = trim($_SERVER["PHP_SELF"],"/");
$user = null;
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

$theme = $company->company_style_theme->raw();
error_log("Home page theme {$theme}");

$salecash = InputParam::encryptFromString("w=cash",$session->session_key);
$saleaccount = InputParam::encryptFromString("w=account",$session->session_key);

if (!isset($_SESSION["csrf_key"]))
	$_SESSION["csrf_key"] = $session->csrf_key;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
    <?php
    echo "<title>{$company->company_name->toHTML()}</title>";
    ?>
	<link rel="icon" type="image/x-icon" href="/images/precisetrees/treefavicon.ico" />
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
    <?php
    echo "<link href='css/{$theme}/index.css' rel='stylesheet' />";
    ?>
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
						<div class="link" href="MaintSel.php?t=account" onclick="sel(this)">NEW CUSTOMER</div>
						<div class="link" href="Sale.php?v=<?php echo $salecash;?>" onclick="sel(this)">CASH SALE</div>
						<div class="link" href="Sale.php?v=<?php echo $saleaccount;?>" onclick="sel(this)">SALE ON ACCOUNT</div>
						<div class="link" href="Expenses.php" onclick="sel(this)">EXPENSES</div>
						<div class="link" href="Quote.php" onclick="sel(this)">QUOTE</div>
					</div>
				</div>
				<div class="links">
					<h2>TRANSACTIONS</h2>
					<div class="linkscontainer">
						<div class="link" href="NewLoan.php" onclick="sel(this)">NEW LOAN</div>
						<div class="link" href="LoanTransaction.php" onclick="sel(this)">UPDATE LOAN</div>
						<div class="link" href="ReceiveInterest.php" onclick="sel(this)">RECEIVE INTEREST</div>
					</div>
				</div>
				<div class="links">
					<h2>ACCOUNTS</h2>
					<div class="linkscontainer">
						<div class="link" href="AccountsPayable.php" onclick="sel(this)">ACCOUNTS PAYABLE</div>
						<div class="link" href="AccountsReceivable.php" onclick="sel(this)">ACCOUNTS RECEIVABLE</div>
						<div class="link" href="BankAccounts.php" onclick="sel(this)">BANK ACCOUNTS</div>
						<div class="link" href="GSTReport.php" onclick="sel(this)">GST REPORT</div>
						<div class="link" href="EndofYear.php" onclick="sel(this)">END OF YEAR</div>
						<div class="link" href="FinancialYearSelect.php" onclick="sel(this)">FINANCIAL STATEMENTS</div>
						<div class="link" href="JournalDump.php" onclick="sel(this)">JOURNAL DUMP</div>
					</div>
				</div>
				<div class="links">
					<h2>FUNCTONS</h2>
					<div class="linkscontainer">
						<div class="link" href="MaintSel.php?t=account" onclick="sel(this)">CUSTOMERS</div>
						<div class="link" href="Invoices.php" onclick="sel(this)">INVOICES</div>
						<div class="link" href="TimeSheet.php" onclick="sel(this)">TIME SHEET ENTRY</div>
						<div class="link" href="PayWage.php" onclick="sel(this)">PAY WAGES</div>
						<div class="link" href="Statements.php" onclick="sel(this)">CUSTOMER STATEMENTS</div>
						<div class="link" href="ShareIssue.php" onclick="sel(this)">ISSUE SHARES</div>
						<div class="link" href="PayTax.php" onclick="sel(this)">PAY TAX</div>
						<div class="link" href="MonthlyChecklist.php" onclick="sel(this)">MONTHLY CHECKLIST</div>
					</div>
				</div>
				<div class="links">
					<h2>VIEW</h2>
					<div class="linkscontainer">
						<div class="link" href="ViewExpenses.php" onclick="sel(this)">VIEW EXPENSES</div>
						<div class="link" href="ViewQuotes.php" onclick="sel(this)">VIEW QUOTES</div>
						<div class="link" href="ViewQuoteRequests.php" onclick="sel(this)">VIEW QUOTE REQUESTS</div>
						<div class="link" href="PaySlips.php" onclick="sel(this)">VIEW PAY SLIPS</div>
						<div class="link" href="PayRuns.php" onclick="sel(this)">VIEW PAY RUNS</div>
						<div class="link" href="ViewFixedAssets.php" onclick="sel(this)">VIEW FIXED ASSETS</div>
						<div class="link" href="LoanAccounts.php" onclick="sel(this)">VIEW LOANS</div>
					</div>
				</div>
				<div class="links">
					<h2>TABLE MAINTENANCE</h2>
					<div class="linkscontainer">
						<div class="link" href="MaintSel.php?t=company" onclick="sel(this)">COMPANY</div>
						<div class="link" href="MaintSel.php?t=product" onclick="sel(this)">PRODUCTS AND SERVICES</div>
						<div class="link" href="MaintSel.php?t=taxclass" onclick="sel(this)">TAX TYPES</div>
						<div class="link" href="MaintSel.php?t=taxrate" onclick="sel(this)">TAX RATES</div>
						<div class="link" href="MaintSel.php?t=taxbracket" onclick="sel(this)">TAX BRACKETS</div>
						<div class="link" href="MaintSel.php?t=chart" onclick="sel(this)">CHART OF ACCOUNTS</div>
						<div class="link" href="MaintSel.php?t=shareholder" onclick="sel(this)">SHARE HOLDERS</div>
						<div class="link" href="ShareIssue.php" onclick="sel(this)">ISSUE SHARES</div>
						<div class="link" href="MaintSel.php?t=staff" onclick="sel(this)">STAFF</div>
					</div>
				</div>
				<div class="links">
					<h2>REPORTS</h2>
					<div class="linkscontainer">
						<div class="link" href="ExpenseSummary.php" onclick="sel(this)">EXPENSE SUMMARY</div>
						<div class="link" href="CashFlowReport.php" onclick="sel(this)">CASH FLOW</div>
						<div class="link" href="AuditLog.php" onclick="sel(this)">AUDIT LOG</div>
						<div class="link" href="Reconciliation.php" onclick="sel(this)">RECONCILIATION TOOL</div>
					</div>
				</div>
			</div>
		</div>
		<?php include ("./includes/footer.html");?>
	</div>
</body>
</html>

