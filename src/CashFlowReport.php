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
$labels_CR = ["shares" => "Shares", "shareholders" => "Shareholders Funds", "loan" => "Loan", "operating" => "GST ROUNDING", "cost of sale" => "Cost of Sale", "fixed_asset" => "Fixed Asset", "sale" => "Sales", "gst" => "GST Refunds", "financial" => "Interest Recvd"];
$labels_DR = ["shares" => "Shares", "shareholders" => "Shareholders Funds", "loan" => "Loan", "operating" => "Operating", "cost of sale" => "Cost of Sale", "fixed_asset" => "Fixed Asset", "sale" => "Sales", "gst" => "GST Paid", "fiancial" => "Interest Paid" ];
$full_date_cope = $DB->getJournalStartAndEndDates();
$balances = array();

//Get starting and ending balance;
$dtStart = (new DateTime($full_date_cope[0]));
$dtStart->sub(new DateInterval("P1D"));
$cashBalanceStart = $DB->cashBalanceAt($dtStart->format("Y-m-d"));
if (count($cashBalanceStart) == 0)
	$cashBalanceStart = 0.0;
else
{
	$v = 0.00;
	foreach ($cashBalanceStart as $c)
		$v += $c["end"]["gross"];
	$cashBalanceStart = $v;
}

$cashBalanceEnd = $DB->cashBalanceAt($full_date_cope[1]);
var_error_log($cashBalanceEnd, "cashBalanceEnd");

if (count($cashBalanceEnd) == 0)
	$cashBalanceEnd = 0.0;
else
{
	$v = 0.00;
	foreach ($cashBalanceEnd as $key => $c)
		$v += $c["end"]["gross"];
	$cashBalanceEnd = $v;
}

//Get the cash balacne at the end of each month

$a = $DB->cashFlow($full_date_cope[0], $full_date_cope[1]);
$data = $a["data"];
$range = $a["range"];
var_error_log($range, "range");

//Get the cash balacne at the end of each month
foreach($range as $key => $r)
{

	$dt = new DateTime(substr($key,0,4) . "-" . substr($key, 4, 2). "-01");
	$dt->sub(new DateInterval("P1D"));

	$a = $DB->cashBalanceAt($dt->format("Y-m-d"));

	if (count($a) == 0)
		$balances[$key] = 0.0;
	else
	{
		$v = 0;
		foreach ($a as $key2 => $c)
			$v += $c["end"]["gross"];
		$balances[$key] = $v;
	}
}

var_error_log($balances, "balances");
var_error_log($data, "data");

function displayEntry($a,$credit=true)
{
	global $labels_CR;
	global $labels_DR;
	global $range;

	$typename = "";
	$sum = array();

	$labels = ($credit) ? $labels_CR : $labels_DR;

	foreach ($a as $type => $b)
	{
		if (isset($labels[$type]))
			$typename = $labels[$type];
		else
			$typename = $type;


		echo "<tr><td></td><td></td><td>{$typename}</td>";
		foreach($range as $key => $r)
		{
			if (!isset($sum[$key]))
				$sum[$key] = 0;
			if (isset($b[$key]))
			{
				$v = $b[$key];
				$sum[$key] += $v;
				$v = LedgerAmount::format1(abs($v));
				echo "<td class='n'>{$v}</td>";
			}
			else
			{
				echo "<td></td>";
			}
		}
		echo "</tr>";
	}

	return $sum;
}

function buildTotalLine($desc, $sum, $neg=false)
{
	echo "<tr><td></td><td colspan='2'>{$desc}</td>";

	foreach ($sum as $s)
	{
		$v = ($neg) ? -$s : $s;
		$v = LedgerAmount::format1($v);
		echo "<td class='n'>{$v}</td>";
	}
	echo "</tr>";
}


function buildTotalLineBold($desc, $sum, $neg=false)
{
	echo "<tr><td></td><td class='b' colspan='2'>{$desc}</td>";

	foreach ($sum as $s) {
		$v = ($neg) ? -$s : $s;
		$v = LedgerAmount::format1($v);
		echo "<td class='n b'>{$v}</td>";
	}
	echo "</tr>";

}

function buildAccumulativeLine($desc, $sum)
{
	echo "<tr><td></td><td colspan='2' class='i'>{$desc}</td>";
	$ac = 0.0;

	foreach ($sum as $s) {
		$ac += $s;
		$v = LedgerAmount::format1($ac);
		echo "<td class='n i'>{$v}</td>";
	}
	echo "</tr>";
}

function sumArrays($a,$b)
{
	$ret = array();
	global $range;

	foreach ($range as $key => $x)
	{
		$ret[$key] = 0;
		if (isset($a[$key]))
			$ret[$key] += $a[$key];
		if (isset($b[$key]))
			$ret[$key] += $b[$key];
	}
	return $ret;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>CASH FLOW REPORT</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 20px;}
		#genheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		td.gap1 {height: 24px;}
		td.n {text-align: right; padding-right: 14px;}
		td.h {font-weight: bold; color: darkblue;border-top: solid 1px darkblue;}
		.i {font-style: italic;}
		.b {font-weight: bold;}
		.r {text-align: right;}
		.red {color: red;}
	</style>
</head>
<body>
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
			<div id="genheading">
				<h1>CASH FLOW REPORT</h1>
			</div>
			<div id="info">
				<table>
					<?php
					$strcashBalanceStart = LedgerAmount::format1($cashBalanceStart);
					$strcashBalanceEnd = LedgerAmount::format1($cashBalanceEnd);
					echo "<tr><td>CASH BALANCE START</td><td class='r'>{$strcashBalanceStart}</td></tr>";
					echo "<tr><td>CASH BALANCE END</td><td class='r'>{$strcashBalanceEnd}</td></tr>";
					?>
				</table>
				<table>
					<?php
					$sumA = array();
					$sumB = array();
					$sumC = array();
					$sumD = array();
					$sumE = array();
					$sumF = array();
					$sumG = array();
					$sumH = array();
					$sumI = array();
					$sumJ = array();
					$sumK = array();
					$sumM = array();
					$sumN = array();
					$Netsum = array();

					echo "<tr><td colspan='3'></td>";

					foreach($range as $key => $mnth)
					{
						echo "<td class='n b'>{$mnth}</td>";
						$sumA[$key] = 0.0;
						$sumB[$key] = 0.0;
						$sumC[$key] = 0.0;
						$sumD[$key] = 0.0;
						$sumE[$key] = 0.0;
						$sumF[$key] = 0.0;
						$sumG[$key] = 0.0;
						$sumH[$key] = 0.0;
						$sumI[$key] = 0.0;
						$sumJ[$key] = 0.0;
						$sumK[$key] = 0.0;
						$sumM[$key] = 0.0;
						$sumN[$key] = 0.0;
						$Netsum[$key] = 0.0;
					}


					echo "</tr>";

					echo "<tr><td colspan='3'>CASH START OF MONTH</td>";
					foreach ($balances as $balance)
					{
						$v = LedgerAmount::format1($balance);
						echo "<td class='n'>{$v}</td>";
					}
					echo "</tr>";
					echo "<tr><td class='gap1'></td></tr>";



					echo "<tr><td class='h' colspan='3'>CASH FLOW FROM OPERATIONS</td></tr>";
					echo "<tr><td></td><td colspan='2'>INCOME</td></tr>";

					if (isset($data["cr"]["income"]))
					{
						$sumA = displayEntry($data["cr"]["income"],true);
						unset($data["cr"]["income"]);
					}
					echo "<tr><td class='gap1'></td></tr>";
					buildTotalLine("TOTAL CASH FROM INCOME", $sumA);
					echo "<tr><td class='gap1'></td></tr>";

					echo "<tr><td></td><td colspan='2'>EXPENSES</td></tr>";

					if (isset($data["dr"]["expense"]))
					{
						$sumB = displayEntry($data["dr"]["expense"],false);
						unset($data["dr"]["expense"]);
					}
					echo "<tr><td class='gap1'></td></tr>";
					buildTotalLine("TOTAL CASH EXPENDITURE", $sumB,true);
					echo "<tr><td class='gap1'></td></tr>";

					$net = sumArrays($sumA, $sumB);
					$Netsum = sumArrays($Netsum, $net);

					buildTotalLineBold("NET CASH FROM OPERATIONS", $net);
					echo "<tr><td class='gap1'></td></tr>";

					buildAccumulativeLine("ACCUMLATIVE CASH FLOW", $net);
					echo "<tr><td class='gap1'></td></tr>";

					echo "<tr><td class='h' colspan='3'>CASH FLOW FROM FINANCE ACTIVITES</td></tr>";
					echo "<tr><td></td><td colspan='2'>INVESTMENT</td></tr>";

					if (isset($data["cr"]["equity"]))
					{
						$sumC = displayEntry($data["cr"]["equity"],true);
						unset($data["cr"]["equity"]);
					}
					if (isset($data["cr"]["liability"]))
					{
						$sumD = displayEntry($data["cr"]["liability"],true);
						unset($data["cr"]["liability"]);
					}
					if (isset($data["cr"]["non current liability"]))
					{
						$sumE = displayEntry($data["cr"]["non current liability"],true);
						unset($data["cr"]["non current liability"]);
					}

					//Combine sums
					$sumCE = sumArrays($sumC, $sumD);
					$sumCE = sumArrays($sumE, $sumCE);

					var_error_log($sumCE, "sumCE");

					echo "<tr><td class='gap1'></td></tr>";
					buildTotalLine("TOTAL FROM INVESTMENT", $sumCE);
					echo "<tr><td class='gap1'></td></tr>";

					echo "<tr><td></td><td colspan='2'>DIVESTMENT</td></tr>";

					if (isset($data["dr"]["non current liability"]))
					{
						$sumF = displayEntry($data["dr"]["non current liability"],false);
						unset($data["dr"]["non current liability"]);
					}
					echo "<tr><td class='gap1'></td></tr>";

					var_error_log($sumF, "sumF");

					buildTotalLine("TOTAL FROM DIVESTMENT", $sumF);


					echo "<tr><td class='gap1'></td></tr>";


					$net = sumArrays($sumCE, $sumF);

					var_error_log($net, "net");


					echo "<tr><td></td><td colspan='2'>TAX</td></tr>";
					if (isset($data["cr"]["tax"])) {
						$sumK = displayEntry($data["cr"]["tax"],true);
						unset($data["cr"]["tax"]);
					}

					if (isset($data["dr"]["tax"])) {
						$sumM = displayEntry($data["dr"]["tax"], false);
						unset($data["dr"]["tax"]);
					}

					var_error_log($sumK, "sumK");
					var_error_log($sumM, "sumM");

					$sumCF = sumArrays($sumK, $sumM);
					var_error_log($sumCF, "sumCF");

					if (isset($data["cr"]["expense"])) {
						$sumN = displayEntry($data["cr"]["expense"], true);
						unset($data["cr"]["expense"]);
					}

					echo "<tr><td class='gap1'></td></tr>";

					$sumCF = sumArrays($sumCF, $sumN);

					buildTotalLine("TOTAL FROM TAX", $sumCF);

					echo "<tr><td class='gap1'></td></tr>";

					var_error_log($sumCE, "sumCE");
					var_error_log($sumCF, "sumCF");

					$net = sumArrays($net, $sumCF);

					var_error_log($net, "net");
					var_error_log($Netsum, "Netsum");

					$Netsum = sumArrays($Netsum, $net);

					var_error_log($Netsum, "Netsum");

					buildTotalLineBold("NET CASH FROM FINANCE ACTIVITES", $net);
					echo "<tr><td class='gap1'></td></tr>";


					echo "<tr><td class='h' colspan='3'>CASH FLOW FROM FIXED ASSETS</td></tr>";

					echo "<tr><td></td><td colspan='2'>SOLD</td></tr>";
					if (isset($data["cr"]["asset"]))
					{
						$sumG = displayEntry($data["cr"]["asset"],true);
						unset($data["cr"]["asset"]);
					}
					echo "<tr><td class='gap1'></td></tr>";
					buildTotalLine("TOTAL FIXED ASSET SOLD", $sumG);
					echo "<tr><td class='gap1'></td></tr>";


					echo "<tr><td></td><td colspan='2'>PURCHASED</td></tr>";


					if (isset($data["dr"]["asset"]))
					{
						$sumH = displayEntry($data["dr"]["asset"],false);
						unset($data["dr"]["asset"]);
					}
					echo "<tr><td class='gap1'></td></tr>";
					buildTotalLine("TOTAL FIXED ASSET PURCHASED", $sumH,true);

					$net = sumArrays($sumG, $sumH);
					$Netsum = sumArrays($Netsum, $net);

					echo "<tr><td class='gap1'></td></tr>";
					buildTotalLineBold("NET CASH FROM FIXED ASSETS", $net);
					echo "<tr><td class='gap1'></td></tr>";


					echo "<tr><td class='h' colspan='3'>CASH FLOW SUMMARY</td></tr>";

					buildTotalLine("Total Net Cash", $Netsum);

					echo "<tr><td class='gap1'></td></tr>";

					$EndBalance = sumArrays($Netsum, $balances);

					buildTotalLineBold("CASH BALANCE END", $EndBalance);

					var_error_log($data, "data");

					if (count($data["cr"]) != 0)
					{
						echo "<tr><td class='red' colspan='4'>CREDITS NOT RECONSILED ARE: ";
						foreach($data["cr"] as $key => $v)
						{
							echo "{$key}, ";
						}
						echo "</td></tr>";
					}

					if (count($data["dr"]) != 0)
					{
						echo "<tr><td class='red' colspan='4'>DEBTORS NOT RECONSILED ARE: ";
						foreach ($data["dr"] as $key => $v) {
							echo "{$key}, ";
						}
						echo "</td></tr>";
					}
					?>
				</table>	
			</div>
		</div>
	</div>
</body>
</html>