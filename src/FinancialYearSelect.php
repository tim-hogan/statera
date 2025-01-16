<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classInputParam.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
require dirname(__FILE__) . "/includes/classFormList2.php";
require dirname(__FILE__) . "/includes/commonSession.php";

function var_error_log($object = null, $text = '')
{
	ob_start();
	var_dump($object);
	$contents = ob_get_contents();
	ob_end_clean();
	error_log("{$text} {$contents}");
}
$strerr = "";

$company = $DB->getCompany();
$startdate = new DateTime($company->company_start_date);
$startYear = intval($startdate->format("Y"));
$month = sprintf("%02d", $company->company_financialyear_start_month);
$d1 = new DateTime("2000-{$month}-01 00:00:00");
$d1 = $d1->sub(new DateInterval("P1M"));
$accountDate = new AccountDate(intval($d1->format("m")));
$fy = $accountDate->finacialYear($startdate);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

	$session->startdate = $_POST["fy"];
	$dt2 = new DateTime($_POST["fy"]);
	$dt2->add(new DateInterval("P1Y"));
	$dt2->sub(new DateInterval("P1D"));

	$session->enddate = $dt2->format("Y-m-d 00:00:00");
	
	header("Location: FinancialStatements.php");
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>SELECT REPORTING YEAR</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 10px; padding: 10px;}
		h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		label {display: block;}
		select {font-size: 12pt;}
		button {margin-top: 20px;font-size: 12pt;}
	</style>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<?php
		if (strlen($strerr) > 0)
		{
			echo "<div id='msg'>";
			echo "<p class='err'>{$strerr}</p>";
			echo "</div>";
		}
		?>
		<div id="main">
			<h1>FINANCIAL REPORTING</h1>
			<div id="form">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div>
						<label for="fy">SELECT FINANCIAL YEAR</label>
						<select id="fy" name="fy">
							<?php
							$start = clone $fy[0];
							$end = clone $fy[1];
							$today = new DateTime();
							$today_ts = $today->getTimestamp();
							while (intval($start->format("Y")) < intval($today->format("Y")) + 1)
							{
								$selected = "";

								if ($today_ts >= $start->getTimestamp() && $today_ts <= $end->getTimestamp())
									$selected = "selected";
								echo "<option value='{$start->format('Y-m-d')}' {$selected}>{$start->format('j/n/Y')} - {$end->format('j/n/Y')}</option>";
								$start->add(new DateInterval("P1Y"));
								$end->add(new DateInterval("P1Y"));
							}
							?>
						</select>
					</div>
					<button>REPORT</button>
				</form>
			</div>
		</div>
	</div>
	<?php include ("./includes/footer.html");?>
</body>
</html>