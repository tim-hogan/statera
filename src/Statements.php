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
$company = $DB->getCompany();
$firstDate = $DB->getJournalFirstDate();
$dtFirst = new DateTime(substr($firstDate,0,7) . "-01");
$lastDate = $DB->getJournalLastDate();
$dtLast = new DateTime(substr($lastDate, 0, 7) . "-01");
$errmsg = "";


if ($_SERVER["REQUEST_METHOD"] == "POST")
{

	if (!$session->checkCSRF())
	{
		$DB->createAudit("security", "{$selff} [" . __LINE__ . "] Invalid CSRF on form input", $user->iduser);
		header("Location: SecurityError.php");
		exit();
	}

	$startDate = FormList::getDateField("monthend");
	$enddate = new DateTime($startDate);
	$enddate->add(new DateInterval("P1M"));
	$lastday = clone $enddate;
	$endDate = $enddate->format("Y-m-d");

	$lastday->sub(new DateInterval("P1D"));
	$strLastDay = $lastday->format("jS M Y");
	$strLastDay2 = $lastday->format("j/n/Y");

	$strStartDate = (new DateTime(FormList::getDateField("monthend")))->format("j/n/Y");

	$statement = array();
	$r = $DB->allAccounts();
	while ($account = $r->fetch_object("account"))
	{
		$balanceStart = $DB->custAccountBalance($account->idaccount, $startDate);
		$balanceEnd = $DB->custAccountBalance($account->idaccount, $endDate);
		$xtns = $DB->allCustomerTransaction2($account->idaccount, $startDate, $endDate);
		if ($balanceStart != 0.00 || $balanceEnd != 0 || count($xtns) > 0)
		{
			$statement[$account->idaccount] = array();
			$statement[$account->idaccount] ["account"] = $account;
			$statement[$account->idaccount] ["start"] = $balanceStart;
			$statement[$account->idaccount] ["end"] = $balanceEnd;
			$statement[$account->idaccount] ["xtns"] = $xtns;
		}
	}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>STATEMENTS</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 20px;}
		#main h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
        #controls {width: 1000px; margin: auto;text-align: center;}
		.printarea {width: 1000px; margin: auto;}
		.printarea h2 {text-align: right;}
        div._divledger {margin-left: 30px;}
		
		table.ledger {width: 100%;}
		td.gap1 {height: 20px;}
		td.gap2 {height: 15px;}
		td.gap3 {height: 10px;}
		
		p.note1 {margin-top: 20px;}

		.r {text-align: right;}
		.b {font-weight: bold;}
		@media print {
			body {font-size: 14pt;}
			#heading {display: none;}
			#menu {display: none;}
			#main {display: none;}
			#controls {display: none;}
			.printpages {width: 21cm;}
			td.gap1 {height: 2cm;}
			td.gap2 {height: 1.5cm;}
			td.gap3 {height: 1cm;}
			p.note1 {margin-top: 1.5cm;}
			footer {page-break-after: always;}
			.printarea {margin-left: 1.5cm; margin-right: 1.5cm;}
			div._divledger {margin-left: 3cm;}
		}
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
			<h1>CUSTOMER STATEMENTS</h1>
			<div id="form">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div class="divinput">
						<label for="monthend">ENTER MONTH END</label>
						<select id="monthend" name="monthend">
							<?php
							$dt = clone $dtFirst;
							while ($dt <= $dtLast)
							{
								$strDate1 = $dt->format("Y M");
								$strDate2 = $dt->format("Y-m-d");
								echo "<option value='{$strDate2}'>{$strDate1}</option>";
								$dt->add(new DateInterval("P1M"));
							}
							?>
						</select>						
					</div>
					<div>
						<button name="create" value="1">CREATE</button>
						<input type="hidden" name="formtoken" value="<?php echo $session->csrf_key;?>" />
					</div>
				</form>
			</div>
		</div>
		
			
        <?php
				if (isset($statement))
				{
					echo "<div id='controls'><button onclick='window.print()'>PRINT</button><button onclick='location.href=\"/\"'>HOME</button></div>";
                }
				if (isset($statement))
				{
					$chart1 = ($DB->getChartFor("cash", null, null, SEARCH_FIRST))->chart_code;
					$chart2 = ($DB->getChartFor("income", "sale", null, SEARCH_ONEONLY))->chart_code;

					foreach($statement as $accountid => $stmnt)
					{
						echo "<div class='printpages'>";
						echo "<div class='printarea'>";



						echo "<h1>PRICE TREE CARE LTD</h1>";

						echo "<h2>MONTHLY STATEMENT</h2>";

						echo "<table>";
						echo "<tr><td class='b'>DATE</td><td class='r b'>{$strLastDay}</td></tr>";
						$strAccountId = sprintf("%05d", $accountid);
						echo "<tr><td colspn='2' class='gap2'></td></tr>";

						echo "<tr><td colspan='2'>{$stmnt["account"]->account_name->toHTML()}</td></tr>";

						if (strlen($stmnt["account"]->account_address1->raw()) > 0)
							echo "<tr><td colspan='2'>{$stmnt["account"]->account_address1->toHTML()}</td></tr>";
						if (strlen($stmnt["account"]->account_address2->raw()) > 0)
							echo "<tr><td colspan='2'>{$stmnt["account"]->account_address2->toHTML()}</td></tr>";
						if (strlen($stmnt["account"]->account_address3->raw()) > 0)
							echo "<tr><td colspan='2'>{$stmnt["account"]->account_address3->toHTML()}</td></tr>";
						if (strlen($stmnt["account"]->account_address4->raw()) > 0)
							echo "<tr><td colspan='2'>{$stmnt["account"]->account_address4->toHTML()}</td></tr>";
						if (strlen($stmnt["account"]->account_city->raw()) > 0)
							echo "<tr><td colspan='2'>{$stmnt["account"]->account_city->toHTML()}</td></tr>";
						echo "<tr><td>ACCOUNT</td><td>{$strAccountId}</td></tr>";

						echo "<tr><td colspan='2' class='gap1'></td></tr>";

						echo "</table>";

						echo "<div class='_divledger'>";
						echo "<table class='ledger'>";
						$strStartBalance = LedgerAmount::format1($stmnt["start"]);

						$balance = $stmnt["start"];

						echo "<tr><td></td><td></td><td class='r'>CREDIT</td><td class='r'>DEBIT</td><td class='r'>BALANCE</td></tr>";
						echo "<tr><td colspn='5' class='gap2'></td></tr>";

						echo "<tr><td>{$strStartDate}</td><td>STARTING BALANCE</td><td></td><td></td><td class='r'>{$strStartBalance}</td></tr>";
						echo "<tr><td colspn='5' class='gap2'></td></tr>";

						//We need to combine folios for bank transactions
						foreach ($stmnt["xtns"] as $j)
						{
							$strDate = (new DateTime($j->journal_date))->format("j/n/Y");
							$gross = $j->SUMGROSS;
							$balance += $gross;
							if ($gross < 0)
								$strGross = LedgerAmount::format1(-($j->SUMGROSS));
							else
								$strGross = LedgerAmount::format1($j->SUMGROSS);

							$cr = "";
							if ($balance < 0)
								$strBalance =  LedgerAmount::format1(-($balance));
							else
							{
								$strBalance = LedgerAmount::format1($balance);
								if ($balance > 0)
									$cr = "CREDIT";
							}


							$desc = "";

							if ($j->journal_chart == $chart2)
							{
								$o_inv = $DB->getInvoice($j->journal_invoice);
								$strInvNum = sprintf("%05d",$o_inv->invoice_number);
								$desc = "INVOICE {$strInvNum}";
							}

							if ($j->journal_chart == $chart1)
							{
								$desc = "PAYMENT THANK YOU";
							}

							if ($gross > 0)
								echo "<tr><td>{$strDate}</td><td>{$desc}</td><td class='r'>{$strGross}</td><td class='r'></td><td class='r'>{$strBalance}</td>";
							else
								echo "<tr><td>{$strDate}</td><td>{$desc}</td><td class='r'></td><td class='r'>{$strGross}</td><td class='r'>{$strBalance}</td>";
							if (strlen($cr) > 0)
								echo "<td>{$cr}</td>";
							echo "</tr>";
							echo "<tr><td colspn='5' class='gap3'></td></tr>";

						}

						$cr = "";
						if ($stmnt["end"] < 0)
							$strEndBalance = LedgerAmount::format1(-($stmnt["end"]));
						else
						{
							$strEndBalance = LedgerAmount::format1($stmnt["end"]);
							if ($stmnt["end"] > 0)
								$cr = "CREDIT";
						}
						echo "<tr><td colspn='5' class='gap2'></td></tr>";
						echo "<tr><td>{$strLastDay2}</td><td>FINAL BALANCE</td><td></td><td></td><td class='r'>{$strEndBalance}</td>";
						if (strlen($cr) > 0)
							echo "<td>{$cr}</td>";

						echo "</tr>";
						echo "</table>";
						echo "</div'>";

						echo "<hr/>";

						echo "<div class='_divbottom'>";
							if ($stmnt["end"] == 0)
							{
								echo "<p class='note1'>Your account is up to date, no action required</p>";
							}
							if ($stmnt["end"] > 0)
							{
								echo "<p class='note1'>Your account is in credit, no payment required</p>";
							}

							if ($stmnt["end"] < 0)
							{
								echo "<p class='note2'>REMITTANCE ADVICE</p>";
								echo "<table>";
								echo "<tr><td>Please pay to account:</td><td>{$company->company_bank_acct_name->toHTML()}</td></tr>";
								echo "<tr><td>Bank Account:</td><td>{$company->company_bank_acct_number->toHTML()}</td></tr>";
								echo "</table>";
							}
						echo "</div>";

						echo "<footer></footer>";

						echo "</div>";
						echo "</div>";

					}
				}
        ?>
		
	</div>
	 
</body>
</html>
