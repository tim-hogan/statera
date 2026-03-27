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

$o_company = $DB->getCompany();
$errmsg = "";
//Get all staff with unpaid timesheet hours

$o_toPayStaff = $DB->allStaffWithUnpaidTime();
$staffid = 0;
$direct_gross = 0;
$period = "";
$tax_calc_multiplier = 0;

$formfields = ["tax" => "","net_pay" =>"","employee_contribution" => "","employer_contribution" => "","employer_esct_tax" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	var_error_log($_POST,"post");
	if (isset($_POST["calc"]) )
	{
		$s = Secure::sec_decryptParamPart(urldecode($_POST["calc"]), base64_encode($session->session_key));
		parse_str($s, $a);
		$staffid = intval($a["s"]);
		$tax_calc_multiplier = 52;
	}

	if (isset($_POST["direct"]) && $_POST["direct"] = "1" && isset($_POST["staff"]))
	{
		if ($_POST["staff"] != "0") 
		{
			$s = Secure::sec_decryptParamPart(urldecode($_POST["staff"]), base64_encode($session->session_key));
			parse_str($s, $a);
			$staffid = intval($a["s"]);

			$period = Formlist::getField("period");
			switch ($period) 
			{
				case 'weekly':
					$tax_calc_multiplier = 52;
					break;
				case 'fortnightly':
					$tax_calc_multiplier = 26;
					break;
				case 'monthly':
					$tax_calc_multiplier = 12;
					break;
				case 'adhoc':
					$tax_calc_multiplier = 52;
					break;
				default: //Assume a week
					$tax_calc_multiplier = 52;
					break;
			}

			$direct_gross = Formlist::getCurrencyField("direct_gross");
			if ($direct_gross == 0.00)
			{
				$errmsg = "ERROR: You must enter a gross amount to pay.";
				$staffid = 0;
			}
		}
		else
		{
			$errmsg = "ERROR: You must select a staff member for direct pay.";
			$staffid = 0;
		}
	}

	if (isset($_POST["paytax"]))
	{
		$folio = $DB->getLastFolio() + 1;
		$gross = Formlist::getCurrencyField("gross");
		
		$b_paydirect = false;
		if (isset($_POST["paydirect"]) && $_POST["paydirect"] == 1)
			$b_paydirect = true;
		if (isset($_POST["period"]))
			$period = $_POST["period"];

		if (strlen(Formlist::getField("tax")) == 0)
			$errmsg = "ERROR: You must enter a tax amount, if no tax then enter 0.00";
		else
		{
			$tax = Formlist::getCurrencyField("tax");
			$formfields["tax"] = LedgerAmount::format1($tax);
		}

		$date = null;
		if (strlen(Formlist::getField("date")) == 0)
			$errmsg = "ERROR: You must enetr a valid date";
		else
			$date = Formlist::getDateField("date");

		$employee_contribution = Formlist::getCurrencyField("employee_contribution");
		if (!$employee_contribution)
			$employee_contribution = 0.00;

		$employer_contribution = Formlist::getCurrencyField("employer_contribution");
		if (!$employer_contribution)
			$employer_contribution = 0.00;

		$employer_esct_tax = Formlist::getCurrencyField("employer_esct_tax");
		
		if (!$employer_esct_tax)
			$employer_esct_tax = 0.00;



		$staffid = Formlist::getIntegerField("staffid");
		$o_staff = $DB->o_getStaff($staffid);


		$net = (($gross * 100)-($tax*100))/100.0;
		$net = $net - $employee_contribution;

		$o_journal = new journal();

		//First we do the pay to bloggs
		$coa2 = ($DB->getChartFor('current liability',"accounts payable",null,SEARCH_FIRST))->chart_code;
		$coa1 = ($DB->getChartFor("expense","cost of sale","wages"))->chart_code;

		$rec = array();
		$rec["journal_date"] = $date;
		$rec["journal_description"] = "Wages for {$o_staff->staff_name->raw()} [{$o_staff->staff_bank_acct_number->raw()}]";
		$rec["journal_source_chart"] = $coa1;
		$rec["journal_folio"] = $folio;
		$rec["journal_gross"] = $net;
		$rec["journal_net"] = $net;
		$rec["journal_tax"] = 0.00;
		$rec["journal_wage_tax"] = $tax;
		$rec["journal_staff"] = $staffid;
		$rec["journal_kiwisaver_employee"] = $employee_contribution;
		$rec["journal_kiwisaver_employer"] = $employer_contribution;
		$rec["journal_kiwisaver_esct_tax"] = $employer_esct_tax;

		if (strlen($errmsg) == 0)
		{
			$DB->BeginTransaction();
			$xtn1 = $DB->createPair($rec, $coa1, $coa2, 0, false);

			$rec["journal_description"] = "IRD PAY DED TAX for {$o_company->company_tax_number->raw()}";
			$rec["journal_folio"] = $folio + 1;
			$rec["journal_gross"] = $tax + $employee_contribution + $employer_contribution;
			$rec["journal_net"] = $tax + $employee_contribution + $employer_contribution;
			$rec["journal_tax"] = 0.00;
			$rec["journal_wage_tax"] = null;
			$rec["journal_tax_type"] = "PAYE";

			$xtn2 = $DB->createPair($rec, $coa1, $coa2, 0, false);

			//Create the pay direct pay timeshhet entry
			if ($b_paydirect)
				$DB->createPayDirectEntry($staffid, $date, $gross, $period,$user->iduser);

			//Need to mark timesheet entries as all paid
			$DB->markAllTimesheetPaidFor($staffid, $xtn1, $date);


			if ($DB->EndTransaction())
			{
				$DB->createAudit("paywage", "Paid wage for {$staffid} date {$date}", $user->iduser);
				$s = Secure::sec_encryptParam("s={$staffid}&x={$xtn1}", base64_encode($session->session_key));
				header("Location: PaySlip.php?v={$s}");
				exit();
			}
			else
			{
				$errmsg = "ERROR: Transaction failed";
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
	<title>PAY WAGES</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 20px;}
		#genheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#listpending {padding: 12px; border: solid 1px #888; border-radius: 8px;}
		#listdirect {padding: 12px; border: solid 1px #888; border-radius: 8px;}
		th,td {padding-right: 16px;}
		#calculation {margin-top: 20px;padding: 12px; border: solid 1px #888; border-radius: 8px;}
		h2 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#calculation td.gap {height: 18px;}
		#msg {margin-left: 20px;}
		#msg p.err {color: red;font-weight: bold;font-size: 14pt;}
		table.slip {max-width : 1000px;}
		.r {text-align: right;}
		.hlighht {background-color: beige;}
		.red {color: red;}
	</style>
	<script src="/js/st.js"></script>
	<script src="/js/noEnter.js"></script>
	<script>
		function taxchange(n) {
			let paye = st.getCurrency(n.value);
			let gross = st.getCurrency(st.ge("f_gross").value);
			let inp_f_eec = st.ge("f_ee_c");

			let employee_con = 0.0;
			if (inp_f_eec)
				employee_con = st.getCurrency(st.ge("f_ee_c").value);
			st.ge("net_pay").value = st.format_currency((gross - paye) - employee_con);
		}
	</script>
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
				<h1>PAY WAGES</h1>
			</div>

			<div id="listpending">
				<h2>UNPAID TIME IN TIMESHEET</h2>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<?php
					if (count($o_toPayStaff) > 0)
					{
						echo "<table>";
						echo "<tr><th>PAYEE NAME</th><th class='r'>HOURS</th><th>PERIOD</th></tr>";
						foreach($o_toPayStaff as $s)
						{
							$data = $DB->totalUnpaidTimesheetHours($s->idstaff);
							$strHours = sprintf("%4.1f", $data["sum"]);
							$strFrom = (new DateTime($data["date_first"]))->format("D M jS Y");
							$strTo  = (new DateTime($data["date_last"]))->format("D M jS Y");
							$v = Secure::sec_encryptParam("s={$s->idstaff}", base64_encode($session->session_key));

							echo "<tr><td>{$s->staff_name->toHTML()}</td><td class='r'>{$strHours}</td><td>{$strFrom} to {$strTo}</td><td><button name='calc' value='$v'>CALCULATE</button></td></tr>";
						}
						echo "</table>";
					}
					else
						echo "<p>THERE ARE NO OUTSTANDING WAGES DUE FOR PAY</p>";
					?>
				</form>
			</div>
			<div id="listdirect">
				<h2>DIRECT AMOUNT</h2>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div>
						<table>
							<tr><td>SELECT STAFF</td><td>GROSS PAY</td><td>WAGE PERIOD</td></tr>
						
						<tr><td>
						<select id="staff" name="staff">
							<option value="0">[SELECT STAFF]</option>
							<?php
							$o_stff = $DB->o_everyStaff();
							foreach($o_stff as $s)
							{
								$v = Secure::sec_encryptParam("s={$s->idstaff}", base64_encode($session->session_key));
								echo "<option value='{$v}'>{$s->staff_name->toHTML()}</option>";
							}
							?>
						</select></td>
						<td><input type="text" class="r" name="direct_gross" size="8" /></td>
						<td><select name="period"><option value="weekly">WEEKLY</option><option value="fortnightly">FORTNIGHTLY</option><option value="monthly">MONTHLY</option><option value="adhoc">AD HOC</option></select></td>
						<td><button name="direct" value="1">PAY DRIECT</button></td>
						</tr>
							</table>
					</div> 	
				</form>
			</div>
			<div id="calculation">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<?php
					if ($staffid)
					{
						error_log("Have staffid --- 1");

						$strDate = classTimeHelpers::timeFormat(new DateTime(),"Y-m-d",$user->user_timezone->raw());
						$data = $DB->totalUnpaidTimesheetHours($staffid);
						$o_staff = $DB->o_getStaff($staffid);
						$strName = $o_staff->staff_name->toHTML();
						$strHours = sprintf("%4.1f", $data["sum"]);
						$strRate = LedgerAmount::format1($o_staff->staff_hourly_rate1);
						$flag_esct = false;
						if ($direct_gross != 0.0)
							$gross = $direct_gross;
						else
							$gross = round($data["sum"] * $o_staff->staff_hourly_rate1,2);
						$tot = $gross;
						$strNet = LedgerAmount::format1($gross);
						$contribution_employee = 0.00;
						echo "<h2>PAY DETAILS FOR {$o_staff->staff_name->toHTML()}</h2>";
						echo "<input type='hidden' name='staffid' value='{$o_staff->idstaff}' />";
						if ($direct_gross != 0)
						{
							echo "<input type='hidden' name='paydirect' value='1' />";
							echo "<input type='hidden' name='period' value='{$period}' />";
						}
						
						echo "<table class='slip'>";
						echo "<tr><td></td><td></td><td></td><td></td><td><input type='date' name='date' value='{$strDate}' /></td></tr>";
						echo "<tr><td class='gap'></td></tr>";
						if ($direct_gross == 0.0)
						{

							echo "<tr><th></th><th class='r'>HOURS</th><th class='r'>RATE</th><th class='r'>TOTAL</th></tr>";
							echo "<tr><td></td><td class='r'>{$strHours}</td><td class='r'>{$strRate}</td><td class='r'>{$strNet}</td></tr>";
							echo "<tr><td class='gap'></td></tr>";
						}
						if ($o_staff->staff_add_holiday_pay)
						{
							$holdpay = round($gross * $o_staff->staff_holiday_pay_rate, 2);
							$tot += $holdpay;
							$strHolidapay = LedgerAmount::format1($holdpay);
							$strHPRate = sprintf("%2.1f%%", $o_staff->staff_holiday_pay_rate * 100);
							echo "<tr><td></td><td>HOLIDAY PAY</td><td class='r'>{$strHPRate}</td><td class='r'>{$strHolidapay}</td></tr>";
							echo "<tr><td class='gap'></td></tr>";
						}

						if ($o_staff->staff_has_kiwi_saver)
						{
							$temp_gross = $tot;


							$contribution_employee = round($temp_gross * $o_staff->staff_kiwi_save_employee_rate, 2);
							$str_employee_rate = sprintf("%2.1f%%", $o_staff->staff_kiwi_save_employee_rate * 100.0);
							$str_contribution_employee = LedgerAmount::format1($contribution_employee);


							$contribution_employer = round($temp_gross * $o_staff->staff_kiwi_save_employer_rate,2);
							$str_employer_rate = sprintf("%2.1f%%", $o_staff->staff_kiwi_save_employer_rate * 100.0);
							$str_contribution_employer = LedgerAmount::format1($contribution_employer);

							if ($o_staff->staff_use_esct_tax)
							{
								// We need to calculate whole dollars only on employer contribution
								$v = floor($contribution_employer);
								$contribution_employer_esct = round($v * $o_staff->staff_esct_tax_rate, 2);
								$contribution_employer_net = $contribution_employer - $contribution_employer_esct;
								$strcontribution_employer_net = LedgerAmount::format1($contribution_employer_net);
								$strescttax = sprintf("%2.1f%%", $o_staff->staff_esct_tax_rate * 100.0);
								if ($o_staff->staff_esct_tax_rate == 0)
									$flag_esct = true;

							}

							//$tot += $contribution_employee;


						}

						$strTotalGross = LedgerAmount::format1($tot);
						$taxnum = $o_staff->staff_tax_number->toHTML();
						$taxcode = $o_staff->staff_tax_code->toHTML();

						//Do a tax estimate
						$marginaltax = $DB->calcMarginalTax($strDate, $tot * $tax_calc_multiplier, $tot);
						var_error_log($marginaltax, "Marginal tax");
						$AccRate = ($DB->getTaxRateForClassAndDate("ACC", $strDate))->taxrate_rate;
						

						$levy = $tot * $AccRate;
						$paye_calc = "";
						if (isset($marginaltax["current"]) && $marginaltax["current"] != 0.0) 
						{
                            $paye_calc = $marginaltax["current"] + $levy;
                            $paye_calc = floor($paye_calc * 100) / 100;
							$strPayeCalc = LedgerAmount::format1($paye_calc);
							$formfields["tax"] = $strPayeCalc;

							$net_value = ($tot - $paye_calc) - $contribution_employee;
							$formfields["net_pay"] = LedgerAmount::format1($net_value);
						}

						echo "<tr><td></td><td></td><td class='r'></td><td>TOTAL GROSS</td><td class='r'><input id='f_gross' type='text' class='r' name='gross' size='8' value='{$strTotalGross}' /></td></tr>";
						echo "<tr><td class='gap'></td></tr>";
						echo "<tr><td></td><td></td><td class='r'></td><td>IRD NUMBER</td><td class='r'><input type='text' class='r' name='irdnum' size='12' value='{$taxnum}'/></td></tr>";
						echo "<tr><td class='gap'></td></tr>";
						echo "<tr><td></td><td></td><td class='r'></td><td>TAX CODE</td><td class='r'><input type='text' class='r' name='irdcode' size='3' value='{$taxcode}'/></td></tr>";
						echo "<tr><td class='gap'></td></tr>";
						echo "<tr><td></td><td></td><td class='r'></td><td>TAX</td><td class='r'><input type='text' class='r hlighht' name='tax' size='8' value='{$formfields["tax"]}' onchange='taxchange(this)' autofocus /></td></tr>";
						echo "<tr><td class='gap'></td></tr>";


						if ($o_staff->staff_has_kiwi_saver)
						{
							$formfields["employee_contribution"] = $str_contribution_employee;
							echo "<tr><td></td><td>KIWI SAVER EMPLOYEE DEDUCTION</td><td class='r'>{$str_employee_rate}</td><td></td><td class='r'><input id='f_ee_c' type='text' class='r' name='employee_contribution' size='8' value='{$formfields["employee_contribution"]}'  /></td></tr>";
							echo "<tr><td class='gap'></td></tr>";
						}

						echo "<tr><td></td><td>NET PAY</td><td class='r'></td><td></td><td class='r'><input id='net_pay' type='text' class='r' name='net_pay' size='8' value='{$formfields["net_pay"]}'  /></td></tr>";
						echo "<tr><td class='gap'></td></tr>";


						if ($o_staff->staff_has_kiwi_saver)
						{
							$formfields["employer_contribution"] = $str_contribution_employer;
							echo "<tr><td></td><td>KIWI SAVER EMPLOYER DEDUCTION</td><td class='r'>{$str_employer_rate}</td><td></td><td class='r'><input id='f_er_c' type='text' class='r' name='employer_contribution' size='8' value='{$formfields["employer_contribution"]}'  /></td></tr>";
							echo "<tr><td class='gap'></td></tr>";

							if ($o_staff->staff_use_esct_tax)
							{
								$formfields["employer_esct_tax"] = $contribution_employer_esct;
								echo "<tr><td></td><td>LESS EMPLOYER TAX</td><td class='r'>{$strescttax}</td><td></td><td class='r'><input id='f_esct_tax' type='text' class='r' name='employer_esct_tax' size='8' value='{$formfields["employer_esct_tax"]}'  /></td></tr>";
								if ($flag_esct)
									echo "<tr><td class='red' colspan='4'>WARNING: NO ESCT TAX RATE CONFIGIURED FOR {$o_staff->staff_name->toHTML()}</td></tr>";
								echo "<tr><td class='gap'></td></tr>";
							}
						}

						echo "<tr><td></td><td></td><td class='r'></td><td></td><td class='r'><button name='paytax' value='paytax'>PAY</button></td></tr>";
						echo "</table>";

					}
					?>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
