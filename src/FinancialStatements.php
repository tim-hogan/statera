<?php
use Vtiful\Kernel\Format;
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
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

//We need to get prior years balances
$company = $DB->getCompany();
$startdate = new DateTime($company->company_start_date);
$startYear = intval($startdate->format("Y"));
$month = sprintf("%02d", $company->company_financialyear_start_month);
$d1 = new DateTime("2000-{$month}-01 00:00:00");
$d1 = $d1->sub(new DateInterval("P1M"));
$accountDate = new AccountDate(intval($d1->format("m")));
$fy = $accountDate->finacialYear($startdate);

$prevRetainedFunds = 0;
while ($fy[0]->format('Y-m-d') < $session->startdate)
{
	$prevRetainedFunds += $DB->retainedFunds($fy[0]->format('Y-m-d'), $fy[1]->format('Y-m-d'));
	error_log("Retained funds for {$fy[0]->format('Y-m-d')} {$fy[1]->format('Y-m-d')} {$prevRetainedFunds}");
	$fy[0]->add(new DateInterval("P1Y"));
	$fy[1]->add(new DateInterval("P1Y"));
}





$balance_sheet = $DB->financialreport($session->startdate, $session->enddate);
$o_company = $DB->getCompany();
$total_revenue = 0.0;
$net_profit = 0.0;
$total_equity = 0.0;
$total_current_assets = 0.0;
$total_liabilities = 0.0;
$cash_bank_account = 0.0;

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>FINANCIAL REPORT</title>
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Akshar:wght@300;400;700&display=swap');
		
		html, body {font-family: Arial, Helvetica, sans-serif;font-size: 10pt;margin: 0;height: 100%;}
		#nonprint {padding: 12px;margin-bottom: 12px;text-align: center;}
		#titlepage h1 {margin-bottom: 50px;}
		#titlepage p.p1 {margin-left: 50px;font-size: 14pt;margin-bottom: 50px;}
		#titlepage p.p2 {font-size: 18pt;font-weight: bold;margin-bottom: 75px;}
		#titlepage p.p3 {font-size: 16pt;font-weight: bold;}
		td.h1 {border-top: solid 1px black;font-weight: bold;}
		td.h2 {font-weight: bold;}
		td.blank1 {height: 1em;}
		td.td1 {min-width: 200px;}
		td.tot1 {border-bottom: solid 1px black;}
		td.tot2 {border-top: solid 1px black; border-bottom: double 2px black;}
		td.pad1 {padding-right: 10px;}
		#shareholding table {border-collapse: collapse;}
		#shareholding th {text-align: left;min-width: 16px;padding-right: 20px;}
		#shareholding th.th1 {border-bottom: solid 1px black;}
		#shareholding th.th2 {border-bottom: solid 1px black;text-align: right;padding-right: 0px;padding-left: 20px;}
		#shareholding td {font-size: 12pt;}
		#fixedassetts table {border-collapse: collapse;}
		#fixedassetts th {text-align: left;min-width: 16px;padding-right: 20px;}
		#fixedassetts th.th1 {border-bottom: solid 1px black;}
		#fixedassetts th.th2 {border-bottom: solid 1px black;text-align: right;padding-right: 0px;padding-left: 20px;}
		#fixedassetts td {font-size: 12pt;padding-left: 20px;}
		#fixedassetts td.td2 {font-size: 12pt;padding-left: 0px;text-align: left;min-width: auto;}
		#analysis table {border-collapse: collapse;}
		#analysis td {padding-right: 2em;}
		#analysis td.td1 {border-top: solid 1px black;padding-bottom: 10px; font-weight: bold;}
		#analysis td.space {height: 1em;}

		.l {text-align: left;}
		.r {text-align: right;}
		.red {color: red;}
		
		@media screen {
			.page {width: 800px; height: 1104px; margin: auto; margin-bottom: 10px;padding: 10px;padding-left: 32px;border: solid 1px #aaa; box-shadow: 10px 10px 5px #aaa;}
		}
		@media print {
			#nonprint {display: none;}
			#titlepage h1 {margin-bottom: 2cm;}
			#titlepage p.p1 {margin-left: 2cm;margin-bottom: 2cm;}
			#titlepage p.p2 {margin-bottom: 3cm};
			#titlepage p.p3 {}
			footer {page-break-after: always;}
			.page {width: 21cm; height: 29cm; margin: 2.5cm;}
			 td.td1 {min-width: 8cm;}
			 td.pad1 {padding-right: 0.5cm;}
			 #shareholding th {padding-right: 0.8cm;}
			 #shareholding th.th2 {padding-right: 0; padding-left: 0.8cm;}
			 #fixedassetts th {padding-right: 0.8cm;}
			 #fixedassetts th.th2 {padding-right: 0; padding-left: 0.8cm;}
		}
	</style>
</head>
<body>
	<div id="container">
		<!--<p><?php var_dump($balance_sheet);?></p>-->
		<div id="nonprint">
			<button onclick="window.print()">PRINT</button>
			<button onclick="location.href = '/';">HOME</button>
		</div>
		<div class="page">
			<div id="titlepage">
				<h1>FINANCIAL REPORT</h1>
				<p class="p1">for</p>
				<?php
				echo "<p class='p2'>{$o_company->company_name->toHTML()}</p>";
				$s1 = (new DateTime($session->startdate))->format("j/n/Y");
				$s2 = (new DateTime($session->enddate))->format("j/n/Y");
				echo "<p class='p3'>FROM {$s1} TO {$s2}</p>";
				?>
			</div>
		</div>
		<div class="page">
			<h2>CASH</h2>
			<div class="table">
				<table>
					<tr><td class="h1" colspan="6">CASH FLOWS FROM OPERATING ACTIVITIES</td></tr>
					<tr><td class='blank1' colspan='6'></td></tr>
					<?php
					echo "<tr><td></td><td class='h2' colspan='5'>RECEIVED</td></tr>";
					$sumR = 0;
					foreach($balance_sheet["cash"] ["received"] as $d)
					{
						$acctname = htmlspecialchars($d["name"]);
						$v = LedgerAmount::format1($d["gross"]);
						echo "<tr><td></td><td></td><td class='td1'>{$acctname}</td><td class='r'>{$v}</td></tr>";
						$sumR += $d["gross"];
					}
					$v = LedgerAmount::format1($sumR);
					echo "<tr><td></td><td></td><td>Total Received</td><td></td><td class='r'>{$v}</td></tr>";
					echo "<tr><td class='blank1' colspan='6'></td></tr>";
					echo "<tr><td></td><td class='h2' colspan='5'>SPENT</td></tr>";
					$sumS = 0;
					foreach($balance_sheet["cash"] ["spent"] as $d)
					{
						$acctname = htmlspecialchars($d["name"]);
						$v = LedgerAmount::format1(-$d["gross"]);
						echo "<tr><td></td><td></td><td class='td1'>{$acctname}</td><td class='r'>{$v}</td></tr>";
						$sumS -= $d["gross"];
					}
					$v = LedgerAmount::format1($sumS);
					echo "<tr><td></td><td></td><td>Total Spent</td><td></td><td class='r'>{$v}</td></tr>";
					echo "<tr><td class='blank1' colspan='6'></td></tr>";

					$v = LedgerAmount::format1($sumR-$sumS);
					echo "<tr><td></td><td></td><td>Net Cash</td><td></td><td></td><td class='r tot1'>{$v}</td></tr>";
					echo "<tr><td class='blank1' colspan='6'></td></tr>";

					echo "<tr><td class='h1' colspan='6'>CASH FLOWS FROM INVESTMENTS</td></tr>";
					echo "<tr><td class='blank1' colspan='6'></td></tr>";

					echo "<tr><td class='h1' colspan='6'>BANK ACCOUNTS</td></tr>";
					echo "<tr><td class='blank1' colspan='6'></td></tr>";
					foreach($balance_sheet["cash"] ["balance"] as $d)
					{
						echo "<tr><td></td><td></td><td colspan='4'>{$d["name"]}</td></tr>";
						$v = LedgerAmount::format1($d["start"] ["gross"]);
						echo "<tr><td></td><td></td><td></td><td></td><td class='r pad1'>Opening Balance</td><td class='r'>{$v}</td></tr>";
						echo "<tr><td class='blank1' colspan='6'></td></tr>";
						$v = LedgerAmount::format1($d["end"] ["gross"]);
						echo "<tr><td></td><td></td><td></td><td></td><td class='r pad1'>Closing Balance</td><td class='r'>{$v}</td></tr>";
					}

					?>
				</table>
			</div>
			<footer></footer>
		</div>
		<div class="page">
			<h2>PROFIT/LOSS</h2>
			<div class="table">
				<table>
					<tr><td class="h1" colspan="6">REVENUE</td></tr>
					<tr><td class='blank1' colspan='6'></td></tr>
					<?php
					$sumR = 0;
					foreach($balance_sheet["income"] ["sale"] as $d)
					{
						$v = LedgerAmount::format1(-$d["net"]);
						$desc = htmlspecialchars($d["name"]);
						echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						$sumR -= $d["net"];
					}
					$v = LedgerAmount::format1($sumR);
					$total_revenue = $sumR;
					echo "<tr><td></td><td></td><td>Total Revenue</td><td></td><td class='r'>{$v}</td></tr>";
					?>
					<tr><td class='blank1' colspan='6'></td></tr>
					<tr><td class="h1" colspan="6">COST OF SALE</td></tr>
					<tr><td class='blank1' colspan='6'></td></tr>
					<?php
					$sumcs = 0;
					foreach($balance_sheet["costofsale"] as $d)
					{
						$v = LedgerAmount::format1(-$d["net"]);
						$desc = htmlspecialchars($d["name"]);
						echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						$sumcs -= $d["net"];
					}
					$v = LedgerAmount::format1($sumcs);
					echo "<tr><td></td><td></td><td>Total Cost of sale</td><td></td><td class='r'>{$v}</td></tr>";
					echo "<tr><td class='blank1' colspan='6'></td></tr>";
					$grossProfit = $sumR - $sumcs;

					$v = LedgerAmount::format1($grossProfit);
					echo "<tr><td></td><td></td><td>Gross profit</td><td></td><td></td><td class='r tot1'>{$v}</td></tr>";

					?>
					<tr><td class='blank1' colspan='6'></td></tr>
					<tr><td class="h1" colspan="6">EXPENDITURE</td></tr>
					<tr><td class='blank1' colspan='6'></td></tr>
					<tr><td></td><td class="h2" colspan="5">OPERATING</td></tr>
					<tr><td class='blank1' colspan='6'></td></tr>
					<?php
					$sumE = 0;
					foreach($balance_sheet["expenditure"] ["operating"] as $d)
					{
						$v = LedgerAmount::format1(-$d["net"]);
						$desc = htmlspecialchars($d["name"]);
						echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						$sumE -= $d["net"];
					}
					$v = LedgerAmount::format1($sumE);
					echo "<tr><td></td><td></td><td>Total Expenditure</td><td></td><td class='r tot1'>{$v}</td></tr>";
					?>
					<tr><td class='blank1' colspan='6'></td></tr>
					<?php
					$ebitda = $grossProfit - $sumE;
					$v = LedgerAmount::format1($ebitda);
					echo "<tr><td></td><td></td><td>EBITDA</td><td></td><td></td><td class='r tot1'>{$v}</td></tr>";

					?>
					<tr><td class='blank1' colspan='6'></td></tr>
					<tr><td></td><td class="h2" colspan="5">FINANCIAL</td></tr>
					<tr><td class='blank1' colspan='6'></td></tr>

					<?php
					$sumE = 0;
					foreach ($balance_sheet["income"]["financial"] as $d)
					{
						$v = LedgerAmount::format1(-$d["net"]);
						$desc = htmlspecialchars($d["name"]);
						echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						$sumE -= $d["net"];
					}

					echo "<tr><td class='blank1' colspan='6'></td></tr>";

					$sumK = 0;
					foreach($balance_sheet["expenditure"]["financial"] as $d)
					{
						var_error_log($d, "expenditure financial");
						$v = LedgerAmount::format1($d["net"]);
						$desc = htmlspecialchars($d["name"]);
						echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						$sumK -= $d["net"];
					}
					$v = LedgerAmount::format1(($ebitda + $sumE) - $sumK);
					$net_profit = ($ebitda + $sumE) - $sumK;

					echo "<tr><td class='blank1' colspan='6'></td></tr>";
					echo "<tr><td></td><td></td><td>Net Profit</td><td></td><td></td><td class='r tot1'>{$v}</td></tr>";

					$retained_funds = ($ebitda + $sumE) - $sumK;

					//Add prior years retained funds
					$retained_funds += $prevRetainedFunds;

					?>




				</table>
			</div>
			<footer></footer>
		</div>
		<div class="page">
			<h2>BALANCE SHEET</h2>
			<div class="table">
				<table>
					<tr>
						<td class="h1" colspan="6">ASSETS</td>
					</tr>
					<tr>
						<td class='blank1' colspan='6'></td>
					</tr>
					<tr>
						<td></td><td class="h2" colspan="5">CURRENT ASSETS</td>
					</tr>
					<?php
					$sumca = 0;
					$sumassets = 0;
					$current_assets = $balance_sheet["assets"] ["current_assets"];
					foreach($current_assets as $d)
					{
						$v = LedgerAmount::format1($d["amt"]);
						$desc = htmlspecialchars($d["name"]);
						echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						$sumca += $d["amt"];
						if ($d["name"] == "Current Bank Account")
							$cash_bank_account = $d["amt"];
					}
					$v = LedgerAmount::format1($sumca);
					$total_current_assets = $sumca;
					echo "<tr><td></td><td></td><td>Total Current Assets</td><td></td><td class='r tot1'>{$v}</td></tr>";
					$sumassets += $sumca;
					?>
					<tr>
						<td></td><td class="h2" colspan="5">CURRENT ASSETS</td>
					</tr>
					<?php
					$sumca = 0;
					$fixed_assets = $balance_sheet["assets"] ["fixed_assets"];
					foreach($fixed_assets as $d)
					{
						$v = LedgerAmount::format1($d["amt"]);
						$desc = htmlspecialchars($d["name"]);
						echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						$sumca += $d["amt"];
					}
					$v = LedgerAmount::format1($sumca);
					echo "<tr><td></td><td></td><td>Total Fixed Assets</td><td></td><td class='r tot1'>{$v}</td></tr>";
					$sumassets += $sumca;

					$v = LedgerAmount::format1($sumassets);
					echo "<tr><td class='blank1' colspan='6'></td></tr>";
					echo "<tr><td></td><td></td><td>TOTAL ASSETS</td><td></td><td></td><td class='r tot2'>{$v}</td></tr>";


					?>

					<tr>
						<td class='blank1' colspan='6'></td>
					</tr>
					<tr>
						<td class="h1" colspan="6">LIABILITIES</td>
					</tr>
					<tr>
						<td class='blank1' colspan='6'></td>
					</tr>
					<tr>
						<td></td><td class="h2" colspan="5">CURRENT LIABILITIES</td>
					</tr>
					<?php
					$sumcl = 0;
					$current_liabilities = $balance_sheet["liabilities"] ["current_liabilities"];
					foreach($current_liabilities as $d)
					{
						$v = LedgerAmount::format1(-$d["amt"]);
						$desc = htmlspecialchars($d["name"]);
						echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						$sumcl -= $d["amt"];
					}

					$total_liabilities += $sumcl;
					$v = LedgerAmount::format1($total_liabilities);
					$total_current_liabilities = $total_liabilities;

					echo "<tr><td></td><td></td><td>Total Current Liabilities</td><td></td><td class='r tot1'>{$v}</td></tr>";

					?>
					<tr>
						<td></td><td class="h2" colspan="5">NON CURRENT LIABILITIES</td>
					</tr>

					<?php
					$total_non_current_liabilities = 0;
					$sumncl = 0;
					$non_current_liabilities = $balance_sheet["liabilities"] ["non_current_liabilities"];
					foreach ($non_current_liabilities as $d)
					{
						$v = LedgerAmount::format1(-$d["amt"]);
						$desc = htmlspecialchars($d["name"]);
						echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						$sumncl -= $d["amt"];
					}

					$total_non_current_liabilities =+ $sumncl;

					$total_liabilities += $sumncl;

					$v = LedgerAmount::format1($total_non_current_liabilities);
					echo "<tr><td></td><td></td><td>Total Non Current Liabilities</td><td></td><td class='r tot1'>{$v}</td></tr>";

					?>
					<tr>
						<td class='blank1' colspan='6'></td>
					</tr>
					<tr>
						<td></td><td class="h2" colspan="5">SHAREHOLDER CURRENT ACCOUNTS</td>
					</tr>
					<?php
					$sumsl = 0;
					$shareholder_liabilities = $balance_sheet["liabilities"] ["shareholder_current"];
					foreach($shareholder_liabilities as $d)
					{
						$v = LedgerAmount::format1(-$d["amt"]);
						$desc = htmlspecialchars($d["name"]);
						echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						$sumsl -= $d["amt"];
					}
					$v = LedgerAmount::format1($sumsl);
					echo "<tr><td></td><td></td><td>Total Shareholder current accounts</td><td></td><td class='r tot1'>{$v}</td></tr>";
					$total_liabilities += $sumsl;
					?>
					<tr>
						<td class='blank1' colspan='6'></td>
					</tr>
					<tr>
						<td class="h1" colspan="6">EQUITY</td>
					</tr>
					<tr>
						<td class='blank1' colspan='6'></td>
					</tr>
					<?php

						$total_liability_equity = $total_liabilities;

						$equity = $balance_sheet["equity"];

						$sume = 0;
						foreach($equity as $e)
						{
							$desc = htmlspecialchars($e['name']);
							$v = LedgerAmount::format1($e['amt']);
							$sume += floatval($e['amt']);
							echo "<tr><td></td><td></td><td class='td1'>{$desc}</td><td class='r'>{$v}</td></tr>";
						}

						$sume += $retained_funds;
						$v = LedgerAmount::format1($retained_funds);
						echo "<tr><td></td><td></td><td class='td1'>Retained Funds</td><td class='r'>{$v}</td></tr>";
						$v = LedgerAmount::format1($sume);
						$total_equity = $sume;
						echo "<tr><td></td><td></td><td>Total Equity</td><td></td><td class='r tot1'>{$v}</td></tr>";

						$total_liability_equity += $sume;
						$v = LedgerAmount::format1($total_liability_equity);

						echo "<tr><td class='blank1' colspan='6'></td></tr>";
						echo "<tr><td></td><td></td><td>TOTAL LIABILTIES PLUS EQUITY</td><td></td><td></td><td class='r tot2'>{$v}</td></tr>";
					?>
				</table>
			</div>
			<footer></footer>
		</div>
		<div class="page">
			<h2>SHAREHOLDINGS</h2>
			<div id="shareholding">
				<div class="table">
					<table>
						<tr>
							<th colspan="4">NAME</th>
						</tr>
						<tr>
							<th class="th1"></th><th class="th1">DATE</th><th class="th2">QUANTITY</th><th class="th2">PRICE</th><th class="th2">VALUE</th>
						</tr>
						<?php
							$lastname = "";
							$shares = $balance_sheet["shares"];
							foreach($shares as $s) 
							{
								if ($lastname != $s["name"])
								{
									$name = htmlspecialchars($s["name"]);
									echo "<tr><td colspan='4'>{$name}</td></tr>";
									$lastname != $s["name"];
								}
								$strDate = (new DateTime($s["date"]))->format("j M Y");
								$v = LedgerAmount::format1($s["price"]);
								$w = LedgerAmount::format1($s["price"] * $s["qty"]);
								echo "<tr><td></td><td>{$strDate}</td><td class='r'>{$s["qty"]}</td><td class='r'>{$v}</td><td class='r'>{$w}</td></tr>";
							}
						?>
					</table>
				</div>
			</div>
			<footer></footer>
		</div>
		<div class="page">
			<h2>FIXED ASSETS</h2>
			<div id="fixedassetts">
				<div class="table">
					<table>
						<tr>
							<th class="th1">ID</th><th class="th2 r">DATE</th><th class="th2">DESCRIPTION</th><th class="th2">AMOUNT</th><th class="th2">NET VALUE</th>
						</tr>
						<?php
						$r = $DB->allAssets();
						while ($asset=$r->fetch_object("asset"))
						{
							$js = $DB->everyAssetJournals($asset->idasset);
							$first = false;
							foreach ($js as $j)
							{
								$date = (new DateTime($j["journal_date"]))->format("j/n/Y");
								if (!$first)
								{
									$bal = $j["journal_net"];
									$strBal = LedgerAmount::format1($bal);
									echo "<tr><td class='td2'>{$asset->idasset}</td><td class='r'>{$date}</td><td>{$asset->asset_name->toHTML()}</td><td></td><td class='r'>{$strBal}</td></tr>";
									$first = true;
								}
								else
								{
									$bal = $bal + $j["journal_net"];
									$strBal = LedgerAmount::format1($bal);
									$strXtn = LedgerAmount::format1($j["journal_net"]);
									echo "<tr><td></td><td class='r'>{$date}</td><td>Depreciation</td><td class='r'>{$strXtn}</td><td class='r'>{$strBal}</td></tr>";
								}
							}
						}
						?>
					</table>
				</div>
			</div>
			<footer></footer>
		</div>
		<div class="page">
			<h2>ANALYSIS</h2>
			<div id="analysis">
				<table>
					<?php
						echo "<tr><td colspan='4' class='td1'>ORDERS</td></tr>";
						$DB->countAllAcceptedQuotes();
						$bl = "$ " . number_format($DB->netSumAllAcceptedQuotes(), 2);
						echo "<tr><td>QUOTES IN BACKLOG </td><td></td><td></td><td class='r'>{$DB->countAllAcceptedQuotes()}</td></tr>";
						echo "<tr><td>BACKLOG NET VALUE</td><td></td><td></td><td class='r'>{$bl}</td></tr>";
						echo "<tr><td colspan='4' class='space'></td></tr>";

						echo "<tr><td colspan='4' class='td1'>PROFITABILITY RATIOS</td></tr>";
						$bl = number_format(($net_profit / $total_revenue)*100.0, 1) . "%";
						echo "<tr><td>PROFIT MARGIN</td><td></td><td></td><td class='r'>{$bl}</td></tr>";

						$bl = number_format(($net_profit / $sumassets) * 100.0, 1 ) . "%";
						echo "<tr><td>RETURN ON ASSETS (ROA)</td><td></td><td></td><td class='r'>{$bl}</td></tr>";

						if ($total_equity < 0)
						{
							$bl = "LOSS";
							$class = 'r red';
						}
						else
						{
							$bl = number_format(($net_profit / $total_equity) * 100.0, 1) . "%";
							$class = 'r red';
						}
						echo "<tr><td>RETURN ON EQUITY (ROE)</td><td></td><td></td><td class='{$class}'>{$bl}</td></tr>";
						echo "<tr><td colspan='4' class='space'></td></tr>";


						echo "<tr><td colspan='4' class='td1'>LIQUIDITY</td></tr>";
						echo "<tr><td></td><td></td><td class='r'>CURRENT</td><td class='r'>TARGET</td></tr>";

						$bl = number_format($total_current_assets / $total_current_liabilities, 1);
						if ($total_current_assets / $total_current_liabilities < 1.5)
							$class = 'r red';
						 else
							$class = 'r';
						echo "<tr><td>LIQUIDITY RATIO CURRENT</td><td></td><td class='{$class}'>{$bl}</td><td class='r'>2.0</td></tr>";

						$bl = number_format($cash_bank_account / $total_current_liabilities, 1);
						if ($cash_bank_account / $total_current_liabilities < 1.5)
							$class = 'r red';
						 else
							$class = 'r';
						echo "<tr><td>LIQUIDITY RATIO CASH</td><td></td><td class='{$class}'>{$bl}</td><td class='r'>2.0</td></tr>";


						$bl = number_format(($total_non_current_liabilities / $sumassets) * 100.0,1) . "%";
						if ($total_non_current_liabilities / $sumassets > 0.8)
							$class = 'r red';
						else
							$class= 'r';
						echo "<tr><td>LOANS TO ASSET RATIO</td><td></td><td class='{$class}'>{$bl}</td><td class='r'>80.0%</td></tr>";

					?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>