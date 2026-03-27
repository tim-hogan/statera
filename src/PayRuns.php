<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classInputParam.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
require dirname(__FILE__) . "/includes/classFormList2.php";
require dirname(__FILE__) . "/includes/classIrFiling.php";

function var_error_log($object = null, $text = '')
{
	ob_start();
	var_dump($object);
	$contents = ob_get_contents();
	ob_end_clean();
	error_log("{$text} {$contents}");
}

require dirname(__FILE__) . "/includes/commonSession.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	var_error_log($_POST,"post");
	if (isset($_POST["create"]) )
	{
		$date = $_POST["create"];
		$o_journals = $DB->o_allJournalsForPayRun($date);
		$filing = new IRFFiling();
		$o_company = $DB->getCompany();
		foreach($o_journals as $j)
		{
			$o_staff = $DB->o_getStaff($j->journal_staff);
			$gross = $j->journal_gross + $j->journal_wage_tax + $j->journal_kiwisaver_employee;
			$paye = $j->journal_wage_tax;
			$o_ts = $DB->getTimeSheetsForXtn($j->journal_xtn, $j->journal_staff);
			$firstdate = "";
			$lastdate = "";
			$pay_cadence = "";
			$total_hours = 0;
			if (count($o_ts) == 1 &&  $o_ts[0]->timesheet_pay_cadence != "adhoc")
			{
				$pay_cadence = $o_ts[0]->timesheet_pay_cadence;
				switch ($o_ts[0]->timesheet_pay_cadence)
				{
					case "weekly":
						//Calculate the Monday before
						$dt = new DateTime($date);
						while (intval($dt->format("N")) != 1)
							$dt->sub(new DateInterval("P1D"));
						$firstdate = $dt->format("Y-m-d");
						$dt->add(new DateInterval("P6D"));
						$lastdate = $dt->format("Y-m-d");
						$total_hours = 40;
						break;
					case "fortnightly":
						$dt = new DateTime($date);
						$dt->sub(new DateInterval("P7D"));
						while (intval($dt->format("N")) != 1)
							$dt->sub(new DateInterval("P1D"));
						$firstdate = $dt->format("Y-m-d");
						$dt->add(new DateInterval("P6D"));
						$lastdate = $dt->format("Y-m-d");
						$total_hours = 80;
						break;
					case "monthly":
						$dt = new DateTime($date);
						$dt = new DateTime("{$dt->format("Y")}-{$dt->format("m")}-01");
						$firstdate = $dt->format("Y-m-d");
						$dt->add(new DateInterval("P1M"));
						$dt->sub(new DateInterval("P1D"));
						$lastdate = $dt->format("Y-m-d");
						$total_hours = 0;
						break;
				}
			}
			else
			{
				$pay_cadence = "adhoc";
				foreach($o_ts as $ts)
				{
					if (strlen($firstdate) == 0)
						$firstdate = $ts->timesheet_date;
					$lastdate = $ts->timesheet_date;
					$total_hours += $ts->timesheet_hours;
				}
			}


			$filing->addEmployeePay($o_staff->staff_tax_number->raw(), $gross, $paye,$j->journal_kiwisaver_employee, ($j->journal_kiwisaver_employer - $j->journal_kiwisaver_esct_tax), $j->journal_kiwisaver_esct_tax,$o_staff->staff_name->raw(), $o_staff->staff_tax_code->raw(), $o_staff->staff_start_date, $pay_cadence, $firstdate,$lastdate, $total_hours);

		}
		$name = $user->user_firstname->raw() . " " . $user->user_lastname->raw();
		$name = trim($name);
		$phone = trim($user->user_phone->raw());
		$email = trim($user->user_email->raw());

		$filing->addEmployer($o_company->company_tax_number->raw(), $date, $name, $phone, $email);

		$filename = "{$date}-IR-PAYE.txt";

		$filing->createExtract("/var/statera/irfilings/{$filename}");

		header('Content-type: text/txt');
		header("Content-Disposition: attachment; filename=\"{$filename}\"");

		echo file_get_contents("/var/statera/irfilings/{$filename}");
		exit();
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>PAY RUNS</title>
	<link rel="icon" type="image/x-icon" href="/images/precisetrees/treefavicon.ico" />
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<style>
		#container {}
		#main {margin: 20px;}
		#genheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
	.r {text-align: right;}
	</style>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<div id="list">
				<div id="genheading">
					<h1>List of payruns</h1>
				</div>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<table>
						<tr>
							<th>DATE</th><th>NUMBER OF STAFF PAID</th>
						</tr>
						<?php
					$runs = $DB->everyPayRun();
					foreach ($runs as $run)
					{
						$strDate = (new DateTime($run["journal_date"]))->format("j/n/Y");
						echo "<tr><td>{$strDate}</td><td class='r'>{$run["CNT"]}</td><td><button type='submit' name='create' value='{$run["journal_date"]}'>CREATE IR FILING FILE</button></td></tr>";
					}
						?>
					</table>
				</form>
			</div>
		</div>
	</div>
</body>
</html>