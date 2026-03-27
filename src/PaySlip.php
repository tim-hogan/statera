<?php
use Vtiful\Kernel\Format;
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

if (!isset($_GET['v']))
{
	error_log("ERROR: {$selff} [" .__LINE__. "] Call to payslip without parameter");
	header("Location: SecurityError.php");
	exit();
}


$s = Secure::sec_decryptParamPart($_GET['v'],base64_encode($session->session_key));
if (!$s || strlen($s) == 0)
{
	error_log("ERROR: {$selff} [" .__LINE__. "] Inavlid payslip key");
	header("Location: SecurityError.php");
	exit();
}

parse_str($s,$a);
$staffid = intval($a["s"]);
$xtn = intval($a["x"]);


$o_staff = $DB->o_getStaff($staffid);
$details = $DB->periodTimesheetForStaffAndXtn($staffid, $xtn);
$coa = ($DB->getChartFor("expense", "cost of sale", "wages"))->chart_code;
$transaction = $DB->o_journalGetTransactionForCOA($xtn, $coa);

$company = $DB->getCompany();
$month = sprintf("%02d", $company->company_financialyear_start_month);
$d1 = new DateTime("2000-{$month}-01 00:00:00");
$d1 = $d1->sub(new DateInterval("P1M"));
$accountDate = new AccountDate(intval($d1->format("m")));
$fy = $accountDate->finacialYear($transaction->journal_date);
$strKiwiSaveEmploee = "";


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>PAY SLIP</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<style>
		#container {margin: auto; width: 1000px;}
		#nonprint {padding: 12px; border: solid 1px #888; border-radius: 8px;margin-bottom: 12px;text-align: center;}
		#page {border: solid 1px #888;}
		#printarea {margin: 40px;}
		#pageheading h2 {text-align: right;}
		#slipheading {margin-top: 24px;}
		#slipbody {margin-top: 24px;}
		#slipbody th, td {padding-right: 16px;}
		#slipbody td.gap1 {height: 16px;}
		#tblHeading td{padding-right: 24px}
		#tblHeading td.tdb {font-weight: bold;}
		.l {text-align: left;}
		.r {text-align: right;}
		.b {font-weight: bold;}
		@media print {
			#container {margin: 0; padding: 0; width: 21cm; height: 29.7cm;}
			#heading {display: none;}
			#menu {display: none;}
			#nonprint {display: none;}
			#page {width: 21cm; height: 29.7cm;border: none;}
			#printarea {margin: 2.5cm;}
			#tblHeading td{padding-right: 1.5cm;}
			#slipheading {margin-top: 2cm;}
			#slipbody {margin-top: 2cm;}
			#slipbody th, td {padding-right: 1cm;}
			#slipbody td.gap1 {height: 1cm;}
		}
	</style>
</head>
<body>
	<div id="container">
		<div id="nonprint">
			<button onclick="window.print()">PRINT</button>
			<button onclick="location.href = '/';">HOME</button>
		</div>
		<div id="page">
			<div id="printarea">
				<div id="pageheading">
					<h1>PRECISE TREE CARE LTD</h1>
					<h2>PAY SLIP</h2>
				</div>
				<div id="slipheading">
					<table id="tblHeading">
						<?php
					echo "<tr><td class='tdb'>NAME:</td><td>{$o_staff->staff_name->toHTML()}</td></tr>";
					$stdtPaid = (new DateTime($details["date_paid"]))->format("D jS F Y");
					$periodStart = (new DateTime($details["date_first"]))->format("j/n/Y");
					$periodEnd = (new DateTime($details["date_last"]))->format("j/n/Y");
					echo "<tr><td class='tdb'>DATE:</td><td>{$stdtPaid}</td></tr>";
					if ($details["type"] == "hours")
						echo "<tr><td class='tdb'>PAY PERIOD:</td><td>From {$periodStart} to {$periodEnd}</td></tr>";
						?>
					</table>
				</div>
				<div id="slipbody">
					<table id="tblBody">
						<?php
						if ($details["type"] == "hours")
						{
							echo "<tr><th class='l'>HOURS</th><th class='r'>RATE</th><th class='r'>GROSS</th></tr>";
							$strHours = sprintf("%4.1f", $details["sum"]);
							$strRate = LedgerAmount::format1($o_staff->staff_hourly_rate1);
							$gross = round($details["sum"] * $o_staff->staff_hourly_rate1, 2);
							$tot = $gross;
							$strGross = LedgerAmount::format1($gross);
							echo "<tr><td class='l'>{$strHours}</td><td class='r'>{$strRate}</td><td class='r'>{$strGross}</td></tr>";
						}
						else
						{
							$gross = $details["gross"];
							$tot = $gross;
							$strGross = LedgerAmount::format1($gross);
							echo "<tr><td>GROSS PAY</td><td></td><td class='r'>{$strGross}</td></tr>";
						}
						if ($o_staff->staff_add_holiday_pay && $o_staff->staff_holiday_pay_rate != 0.0)
						{
							$hoildpay = round($gross * $o_staff->staff_holiday_pay_rate, 2);
							$tot += $hoildpay;
							$strHolidapay = LedgerAmount::format1($hoildpay);
							$strHPRate = sprintf("%2.1f%%", $o_staff->staff_holiday_pay_rate * 100);
							echo "<tr><td>HOLIDAY PAY</td><td class='r'>{$strHPRate}</td><td class='r'>{$strHolidapay}</td></tr>";
						}
						echo "<tr><td colspan='3' class='gap1'></td></tr>";
						$strTot = LedgerAmount::format1($tot);
						echo "<tr><td>TOTAL GROSS</td><td></td><td class='r'>{$strTot}</td></tr>";
						$strPAYE = LedgerAmount::format1($transaction->journal_wage_tax);
						echo "<tr><td colspan='3' class='gap1'></td></tr>";
						echo "<tr><td>LESS PAYE DEDUCTION</td><td></td><td class='r'>{$strPAYE}</td></tr>";
						echo "<tr><td colspan='3' class='gap1'></td></tr>";
						if ($transaction->journal_kiwisaver_employee > 0)
						{
							$strKiwiSaveEmploee = LedgerAmount::format1($transaction->journal_kiwisaver_employee);
							echo "<tr><td>LESS KIWI SAVER CONTRIBUTION</td><td></td><td class='r'>{$strKiwiSaveEmploee}</td></tr>";
							echo "<tr><td colspan='3' class='gap1'></td></tr>";
						}

						$strPaid = LedgerAmount::format1($transaction->journal_gross);
						echo "<tr><td>PAID TO ACCOUNT {$o_staff->staff_bank_acct_number->toHTML()}</td><td></td><td></td><td class='r b'>{$strPaid}</td></tr>";
						?>
					</table>
					<?php
					if ($transaction->journal_kiwisaver_employee > 0)
					{
						echo "<hr/>";
						echo "<h3>KIWI SAVER</h3>";
						echo "<table>";
						echo "<tr><td>YOUR CONTRIBUTION</td><td class='r'>{$strKiwiSaveEmploee}</td></tr>";
						$compname = strtoupper($company->company_name->toHTML());
						$strKiwiSaveEmployer = LedgerAmount::format1($transaction->journal_kiwisaver_employer);
						echo "<tr><td>{$compname} CONTRIBUTION</td><td class='r'>{$strKiwiSaveEmployer}</td></tr>";
						$strESCT = LedgerAmount::format1($transaction->journal_kiwisaver_esct_tax);
						echo "<tr><td>LESS ESCT</td><td class='r'>{$strESCT}</td></tr>";
						echo "<tr><td colspan='3' class='gap1'></td></tr>";
						$strKS = LedgerAmount::format1(($transaction->journal_kiwisaver_employee + $transaction->journal_kiwisaver_employer) - $transaction->journal_kiwisaver_esct_tax);
						echo "<tr><td>TOTAL KIWI SAVER</td><td></td><td class='r b'>{$strKS}</td></tr>";
						echo "</table>";
					}
					?>
					<hr />
					<h3>YEAR TO DATE</h3>
					<table>
						<tr><th>TOTAL PAY</th><th class="r">TAX</th><th class="r">NET PAY</th></tr>
						<?php
						$total_wages = $DB->wagesForPeriod($staffid, $fy[0]->format("Y-m-d"), $transaction->journal_date);
						$strGross = LedgerAmount::format1($total_wages["gross"]);
						$strTax = LedgerAmount::format1($total_wages["tax"]);
						$strNet = LedgerAmount::format1($total_wages["net"]);
						echo "<tr><td clas='r'>{$strGross}</td><td class='r'>{$strTax}</td><td class='r b'>{$strNet}</td></tr>";
						if ($total_wages["ks_employee"] > 0 || $total_wages["ks_employer"] > 0)
						{
							$strKS = LedgerAmount::format1(($total_wages["ks_employee"] + $total_wages["ks_employer"]) - $total_wages["ks_tax"]);
							echo "<tr><td>TOTAL KIWI SAVER</td><td></td><td class='r b'>{$strKS}</td></tr>";
						}
						?>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>
</html>