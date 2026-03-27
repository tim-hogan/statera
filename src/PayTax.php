<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classInputParam.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
require dirname(__FILE__) . "/includes/classFormList2.php";

function var_error_log( $object=null , $text='')
{
	ob_start();
	var_dump( $object );
	$contents = ob_get_contents();
	ob_end_clean();
	error_log( "{$text} {$contents}" );
}

require dirname(__FILE__) . "/includes/commonSession.php";
$company = $DB->getCompany();
$cadence = $company->company_sales_tax_cadence;
$startmonth = $company->company_sales_tax_first_month;

$saleTaxMonths = AccountDate::cadenceMonths($cadence,$startmonth);

$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone($user->user_timezone->raw()));

$formfields = array();
$strerr = "";
$firstJournal = $DB->firstJournal();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	var_error_log($_POST,"_POST");

	if (!$session->checkCSRF())
	{
		$DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
		header("Location: SecurityError.php");
		exit();
	}

	if (isset($_POST["paysalestax"]))
	{
		$formfields["date"] = $_POST["date"];
		if (! isset($_POST["month"]))
			$strerr = "You must spcifiy a month";
		if (! isset($_POST["year"]))
			$strerr = "You must spcifiy a year";

		if (! isset($_POST["amt"]))
			$strerr = "You must specifiy an amount";

		$input_amt = floatval($_POST["amt"]);
		if ($input_amt == 0.0)
			$strerr = "You must specifiy an amount";


		if (strlen($strerr) == 0)
		{

			$undo = new Undo("PAY {$company->company_sales_tax_name->raw()}");
			error_log("Input into cadenceRangeMonths Year {$_POST["year"]} Month {$_POST["month"]}");
			$a = AccountDate::cadenceRangeMonths($_POST["year"], $_POST["month"], $company->company_sales_tax_cadence,false);
			var_error_log($a,"a");

			$report = $DB->gstReport($a[0],$a[1]);
			$lines = $report["lines"];
			$amt = $lines[15];
			$jrnl = $lines[16];
			$roundoff = $lines[17];



			if (($input_amt - $amt) > 0.004)
			{
				$v1 = LedgerAmount::format1($input_amt);
				$v2 = LedgerAmount::format1($amt);
				$strerr = "The amount entered of {$v1} is different form that on the GST report of {$v2}";
			}
			else
			{

				$rslt = $DB->PaySalesTax($formfields["date"],$amt,$roundoff,$company->company_sales_tax_name->raw(),$a[1]);
				if ($rslt)
				{

					$undo->add(new UndoAction("delete","journal","journal_xtn",$rslt[0]) );
					if ($rslt[1])
						$undo->add(new UndoAction("delete","journal","journal_xtn",$rslt[1]) );
					$undolist->push($undo);
					$DB->updateUndoList($user->iduser,$undolist->toJSON());
					header("Location: {$selff}");
					exit();
				}
			}

		}
	}
	if (isset($_POST["paysalestaxcombined"]))
	{
		error_log("Pay combined");

		$formfields["date"] = $_POST["date"];
		//Now we check that we have more than one month year ajnd amt
		if (  count($_POST["year"]) == count($_POST["month"])  && count($_POST["amt"]) == count($_POST["year"]))
		{
			if ( count($_POST["year"]) >= 2)
			{
				foreach($_POST["amt"] as $v)
				{
					if (floatval($v) == 0.0)
						$strerr = "You must specifiy an amount";
				}
			}
			else
				$strerr = "Only a single tax period was specified for a PAY COMBINED option.";
		}
		else
			$strerr = "Input counts of year, month adn ammount are not equal.";

		if (!isset($_POST["month"]))
			$strerr = "You must spcifiy a month";
		if (! isset($_POST["year"]))
			$strerr = "You must spcifiy a year";

		if (! isset($_POST["amt"]))
			$strerr = "You must specifiy an amount";

		$input_amt = floatval($_POST["amt"]);
		if ($input_amt == 0.0)
			$strerr = "You must specifiy an amount";


		if (strlen($strerr) == 0)
		{
			$folio = $DB->getLastFolio() + 1;
			for ($idx = 0; $idx < count($_POST["year"]); $idx++)
			{
				$input_amt = floatval($_POST["amt"] [$idx]);

				$a = AccountDate::cadenceRangeMonths($_POST["year"] [$idx], $_POST["month"] [$idx], $company->company_sales_tax_cadence, false);
				$report = $DB->gstReport($a[0], $a[1]);
				$lines = $report["lines"];
				$amt = $lines[15];
				$jrnl = $lines[16];
				$roundoff = $lines[17];

				if (($input_amt - $amt) > 0.004)
				{
					$v1 = LedgerAmount::format1($input_amt);
					$v2 = LedgerAmount::format1($amt);
					$strerr = "The amount entered of {$v1} is different form that on the GST report of {$v2}";
				}
				else
				{
					$rslt = $DB->PaySalesTax($formfields["date"], $amt, $roundoff, $company->company_sales_tax_name->raw(), $a[1], $folio);
					if (!$rslt)
					{
						$strerr = "Error occured paying tax or tax refund";
					}
				}

			}

			if (strlen($strerr))
			{
				header("Location: {$selff}");
				exit();
			}


		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>PAY TAX</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		h1 {margin-top: 0;color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		h2 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#main {margin: 20px; padding: 20px;}
		#paydate {margin-bottom: 16px;}
		#paydate label {margin-right: 12px;}
		#salestax {padding: 12px; border: solid 1px #888; border-radius: 8px;}
		#salestax td{padding-right: 20px;}
		#taxlist {margin-bottom: 16px;}
		#taxlist table {border-collapse: collapse;}
		#taxlist th {text-align: left;}
		#taxlist th,td {padding-right: 20px;}
		#combine {padding: 16px; background-color: #eeeeff; border: solid 1px #888; border-radius: 8px;}
		p.err {font-size: 12pt; color: red;}
		.r {text-align: right;}
	</style>
	<script src="/js/st.js"></script>
	<script>
		function payit(n) {
			let form = st.ce("FORM");
			document.body.appendChild(form);
			form.method = "POST";
			form.action = "<?php echo $selff;?>";
			st.ci("date", st.ge("date").value, form);
			st.ci("year", st.ga(n, "_y"), form);
			st.ci("month", st.ga(n, "_m"), form);
			st.ci("amt", st.ga(n,"_v"), form);
			st.ci("paysalestax","PAY",form);
			st.ci("formtoken", "<?php echo $session->csrf_key;?>", form);
			form.submit();
		}
		function lineSelect(n) {
			let tot = 0;
			let l = document.getElementsByClassName("lineselect");
			for (let b of l) {
				if (b.checked) {
					tot += parseFloat(st.ga(b, "_v"));
				}
			}
			st.ge("combined_amount").value = st.format_currency(tot);
		}
		function payCombined(n) {
			let form = st.ce("FORM");
			document.body.appendChild(form);
			form.method = "POST";
			form.action = "<?php echo $selff;?>";
			st.ci("date", st.ge("date").value, form);
			let l = document.getElementsByClassName("lineselect");
			let cnt = 0;
			for (let b of l) {
				if (b.checked) {
					st.ci("year[]", st.ga(b, "_y"), form);
					st.ci("month[]", st.ga(b, "_m"), form);
					st.ci("amt[]", st.ga(b,"_v"), form);
				}
			}
			st.ci("paysalestaxcombined","PAYCOMBINED",form);
			st.ci("formtoken", "<?php echo $session->csrf_key;?>", form);
			form.submit();
		}

	</script>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<?php
				if (strlen($strerr) > 0)
				{
					echo "<div id='msg'>";
					echo "<p class='err'>{$strerr}</p>";
					echo "</div>";
				}
			?>
			<h1>PAY TAX</h1>
			<div id="alltypes">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<div id="salestax">
					<?php
						$report = null;
						$strDate = (isset($formfields["date"]) ) ? $formfields["date"] : $dtNow->format("Y-m-d");
						echo "<h2>{$company->company_sales_tax_name->raw()}</h2>";
						echo "<div id='paydate'>";
						echo "<label for='date'>PAYMENT DATE</label>";
						echo "<input id='date' type='date' name='date' value='{$strDate}' autofocus />";
						echo "</div>";
					?>
					<div id="taxlist">
						<table>
							<tr><th>FROM</th><th>TO</th><th>PAY TAX</th><th>REFUND</th><th>ROUND OFF</th></tr>
							<?php
							$dnow = new DateTime();
							$dStart = new DateTime($firstJournal["journal_date"]);
							$cnt = 0;
							while ($dStart < $dnow && $cnt < 12)
							{
								$se = AccountDate::startEndForDate($dStart->format("Y-m-d"),$cadence,$startmonth);
								$report = $DB->gstReport($se[0],$se[1]);
								$strPeriodStart = classTimeHelpers::timeFormat($se[0],"j M Y");
								$strPeriodEnd = classTimeHelpers::timeFormat($se[1],"j M Y");

								//Now find if we have already paid for it
								$paid = $DB->getSalesTaxPaid($se[1]);

								$lines = $report["lines"];
								$pay = "";
								$refund = "";
								$roundoff = "";
								if ($lines[15] > 0)
									$pay = LedgerAmount::format1($lines[15]);
								if ($lines[15] < 0)
									$refund = LedgerAmount::format1(-$lines[15]);
								if ($lines[17] != 0)
									$roundoff = LedgerAmount::format1($lines[17]);

								$v = "p={$se[1]}";
								$s = Secure::sec_encryptParam($v,base64_encode($session->session_key));
								echo "<tr><td><a href='GSTReport.php?v={$s}'>{$strPeriodStart}</a></td><td>{$strPeriodEnd}</td><td class='r'>{$pay}</td><td claSS='r'>{$refund}</td><td class='r'>{$roundoff}</td>";
								if (($lines[16] - $paid) == 0.0)
								{
									if (strlen($refund) > 0) 
										echo "<td>REFUNDED</td>";
									else
										echo "<td>PAID</td>";
								}
								else
								{
									$de = new DateTime($se[1]);
									$_m = intval($de->format("m"));
									//$_m = strtoupper($de->format("M"));
									$_y = $de->format("Y");
									if ($lines[15] > 0)
										echo "<td>TO PAY</td><td><button type='button' _y='{$_y}' _m='{$_m}' _v='{$lines[15]}' onclick='payit(this)'>PAY</button></td>";
									else
										echo "<td>REFUND DUE</td><td><button type='button' _y='{$_y}' _m='{$_m}' _v='{$lines[15]}' onclick='payit(this)'>RECEIVED REFUND</button></td>";
								}
								echo "</tr>";

								$dStart->add(new DateInterval("P{$cadence}M"));
								$cnt++;
							}
							?>
						</table>
					</div>
					<div id="combine">
						<h2>CHOOSE FOLLOWING ONLY IF YOU ARE TO COMBINE MUTIPLE RETURNS INTO SINGLE PAYMENT OR REFUND</h2>
						<p>SELECT WHICH RETURNS YOU WANT TO COMBINE, THEN PRESS <b>PAY COMBINED</b></p>
						<table>
							<?PHP
							$dnow = new DateTime();
							$dStart = new DateTime($firstJournal["journal_date"]);
							$cnt = 0;
							while ($dStart < $dnow && $cnt < 12)
							{
								$se = AccountDate::startEndForDate($dStart->format("Y-m-d"),$cadence,$startmonth);
								$report = $DB->gstReport($se[0],$se[1]);
								$strPeriodStart = classTimeHelpers::timeFormat($se[0],"j M Y");
								$strPeriodEnd = classTimeHelpers::timeFormat($se[1],"j M Y");

								//Now find if we have already paid for it
								$paid = $DB->getSalesTaxPaid($se[1]);

								$lines = $report["lines"];
								$pay = "";
								$refund = "";
								$roundoff = "";
								if ($lines[15] > 0)
									$pay = LedgerAmount::format1($lines[15]);
								if ($lines[15] < 0)
									$refund = LedgerAmount::format1(-$lines[15]);
								if ($lines[17] != 0)
									$roundoff = LedgerAmount::format1($lines[17]);

								$v = "p={$se[1]}";
								$s = Secure::sec_encryptParam($v,base64_encode($session->session_key));
								if (($lines[16] - $paid) != 0.0)
								{
									$de = new DateTime($se[1]);
									$_m = intval($de->format("m"));
									$_y = $de->format("Y");
									echo "<tr><td><input type='checkbox' class='lineselect' onclick='lineSelect(this)' _y='{$_y}' _m='{$_m}' _v='{$lines[15]}' /></td><td><a href='GSTReport.php?v={$s}'>{$strPeriodStart}</a></td><td>{$strPeriodEnd}</td><td class='r'>{$pay}</td><td claSS='r'>{$refund}</td><td class='r'>{$roundoff}</td>";
									echo "</tr>";
								}


								$dStart->add(new DateInterval("P{$cadence}M"));
								$cnt++;
								}
							?>
						</table>
						<table>
						<tr><td>PAYMENT / (REFUND) AMOUNT</td></tr>
						<tr><td><input id="combined_amount" type='text' class='r' size="10" name='combined_amount' readonly/></td></tr>
						<tr><td><button type="button" onclick="payCombined(this)">PAY COMBINED</button></td></tr>
						</table>


					</div>
				</div>
				<div id="companytax">
				</div>
				<?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
				</form>
			</div>
		</div>
	 </div>
	<?php include ("./includes/footer.html");?>
</body>
</html>
