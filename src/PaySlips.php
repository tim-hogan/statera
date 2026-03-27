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

$o_allSTaff = $DB->allStaffReceivedPay();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>PAY SLIPS</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<style>
		#container {}
		#main {margin: 20px;}
		#genheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		.b {font-weight: bold;}
	</style>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<div id="list">
				<div id="genheading">
					<h1>List of payslips by emploee</h1>
				</div>
				<table>
				<?php
				foreach($o_allSTaff as $o_staff)
				{
					echo "<tr><td class='b'>{$o_staff->staff_name->toHTML()}</td></tr>";
					$wages = $DB->allWagesForStaff($o_staff->idstaff);
					foreach($wages as $wage)
					{
						$straDate = (new DateTime($wage["journal_date"]))->format("j/n/Y");
						$amt = LedgerAmount::format1($wage["journal_gross"]);
						$s = Secure::sec_encryptParam("s={$o_staff->idstaff}&x={$wage["journal_xtn"]}", base64_encode($session->session_key));
						echo "<tr><td><a href='PaySlip.php?v={$s}'>{$straDate}</a></td><td>{$amt}</td></tr>";
					}
				}
				?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>