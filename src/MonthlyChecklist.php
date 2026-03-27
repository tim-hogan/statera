<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classInputParam.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
require dirname(__FILE__) . "/includes/classFormList2.php";

function var_error_log($object = null, $text = '')
{
	ob_start();
	var_dump($object);
	$contents = ob_get_contents();
	ob_end_clean();
	error_log("{$text} {$contents}");
}

require dirname(__FILE__) . "/includes/commonSession.php";
$errmsg = "";
$do_list = false;

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (!$session->checkCSRF())
	{
		$DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
		header("Location: SecurityError.php");
		exit();
	}

	$company = $DB->getCompany();


	$month = sprintf("%02d",FormList::getIntegerField("month"));
	$year = FormList::getIntegerField("year");

	$start = new DateTime("{$year}-{$month}-01");
	$end = clone ($start);
	$end->add(new DateInterval("P1M"));
	$end->sub(new DateInterval("P1D"));

	$cashbalance = $DB->cashBalanceAt($end->format("Y-m-d"));
	$loanbalance = $DB->loanBalacneAt($end->format("Y-m-d"));

	$sales_tax_due = false;
	$sales_tax_months = AccountDate::cadenceMonths($company->company_sales_tax_cadence, $company->company_sales_tax_first_month);
	if (in_array(strtoupper($start->format("M")), $sales_tax_months))
		$sales_tax_due = true;

	$accts = $DB->everyAccountsReceivable();
	var_error_log($accts, "accts");
	
	$do_list = true;
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>CHECKLIST</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#subheading {margin-left: 20px;}
		#subheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#form {margin: 20px;padding: 20px;border: solid 1px #888;border-radius: 8px;width: 300px;}
		#form h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;font-size: 14pt;}
		#form div.formfield {margin-bottom: 16px;}
		#list {display: none;margin: 20px;padding: 20px;border: solid 1px #888;border-radius: 8px;}
		.r {text-align: right;}
		.a {padding-left: 20px;}
	</style>
	<script src="/js/st.js"></script>
	<script>
		var do_list = <?php echo ($do_list) ? "true" : "false";?>;
		function start() {
			if (do_list) {
				st.ge("list").style.display = "block";
				st.ge("form").style.display = "none";
			}
		}
	</script>
</head>
<body onload="start()">
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<?php
			if (strlen($errmsg) > 0)
			{
				echo "<div id='msg'><p class='err'>{$errmsg}</p></div>";
			}
			?>
			<div id="subheading">
				<h1>CHECKLIST</h1>
				<div id="form">
					<form method="post" action="<?php echo "{$selff}";?>">
						<h1>SELECT MONTH</h1>
						<?php
						$dt = new DateTime();
						$dt->setTimezone(new DateTimeZone($user->user_timezone->raw()));
						$dt->sub(new DateInterval("P1M"));
						$selectMonth = intval($dt->format("m"));
						$selectYear = intval($dt->format("Y"));
						?>
						<div class="formfield">
							<label>MONTH</label>
							<select name="month">
								<?php
									for ($month = 1; $month < 13;$month++)
									{
										$selected = ($month == $selectMonth) ? "selected" : "";
										$strMonth = classTimeHelpers::shortMonths[$month];
										echo "<option value='{$month}' {$selected}>{$strMonth}</option>";
									}
								?>
							</select>
							<label>YEAR</label>
							<select name="year">
								<?php
									for ($year = $selectYear - 2; $year < $selectYear + 2; $year++)
									{
										$selected = ($year == $selectYear) ? "selected" : "";
										echo "<option value='{$year}' {$selected}>{$year}</option>";
									}
								?>
							</select>
						</div>
						<div class="formfield">
							<button>CREATE CHECKLIST</button>
						</div>
						<?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
					</form>
				</div>
				<div id="list">
					<table>
						<?php
						if ($do_list)
						{
							if ($accts && count($accts) > 0)
							{
								echo "<tr><td colspan='3'>CHECKING INVOICES NOT YET PAID</td><td><button onclick='location.href = \"AccountsReceivable.php\";'>ACCOUNTS RECEIVABLE</button></td></tr>";
								foreach($accts as $a)
								{
									$strInvoice = sprintf("%06d", $a["journal_invoice"]);
									$strAmount = LedgerAmount::format1($a["journal_gross"]);
									echo "<tr><td><input type='checkbox'></td><td>Have we received payment for invoice #{$strInvoice}</td><td class='r a'>$strAmount</td></tr>";
								}
							}
							echo "<tr><td colspan='3'>CHECKING BANK ACCOUNT BALANCES</td></tr>";
							foreach($cashbalance as $code => $a)
							{
								$strAmount = LedgerAmount::format1($a["end"]["gross"]);
								echo "<tr><td><input type='checkbox'></td><td>Check the bank balance of the {$a["name"]} is:</td><td class='r a'>$strAmount</td></tr>";
							}
							echo "<tr><td colspan='3'>CHECKING LOAN BALANCES</td></tr>";
							foreach($loanbalance as $code => $a)
							{
								$strAmount = LedgerAmount::format1($a["end"]["gross"]);
								echo "<tr><td><input type='checkbox'></td><td>Check loan balance of the {$a["name"]} is:</td><td class='r a'>$strAmount</td></tr>";
							}
							if ($sales_tax_due)
							{
								echo "<tr><td colspan='3'>TAX TASKS REQUIRED</td></tr>";
								echo "<tr><td><input type='checkbox'></td><td>GST RETURN IS DUE NOW</td><td><button>CALCULATE NOW</button></td></tr>";
							}
						}
						?>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>
</html>