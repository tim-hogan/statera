<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
require dirname(__FILE__) . "/includes/classFormList2.php";
require dirname(__FILE__) . "/includes/classInputParam.php";

function var_error_log( $object=null , $text='')
{
	ob_start();
	var_dump( $object );
	$contents = ob_get_contents();
	ob_end_clean();
	error_log( "{$text} {$contents}" );
}

require dirname(__FILE__) . "/includes/commonSession.php";
$filters = [
	"oustanding" => ["name" => "OUTSTANDING", "field" => "check_oustanding"],
	"paid" => ["name" => "PAID", "field" => "check_paid"],
	"overdue" => ["name" => "OVERDUE", "field" => "check_overdue"]
];

$formfields = ["check_oustanding" => true, "check_paid" => false, "check_overdue" => true];

function BuildFilters()
{
	global $filters;
	global $formfields;

	foreach ($filters as $key => $filter) {
		echo "<label for='{$filter["field"]}'>{$filter["name"]}</label>";
		$checked = "";
		if ($formfields[$filter["field"]])
			$checked = "checked";
		echo "<input type='checkbox' id='{$filter["field"]}' name='{$filter["field"]}' {$checked} />";
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	var_error_log($_POST, "_POST");

	if (isset($_POST["filter"]))
	{
		foreach ($filters as $key => $filter)
		{
			if (isset($_POST[$filter["field"]]) && $_POST[$filter["field"]] == "on")
				$formfields[$filter["field"]] = true;
			else
				$formfields[$filter["field"]] = false;
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>CASH SALE</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
	#main {margin: 20px;}
	#list {padding: 20px;}
	h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
	h2 {color: #6b6ba7;font-family: Akshar;font-weight: 300;margin-top: 0;}
	#selection {margin-bottom: 16px;padding: 20px;border: solid 1px #888;}
	#selection label {margin-bottom: 0; font-size: 9pt;}
	#selection input {margin-right: 28px;}
	#list td, th {padding-right: 20px;}
	.l {text-align: left;}
	.r {text-align: right;}
	.overdue {color: red; font-weight: bold;}
	</style>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<h1>INVOICES</h1>
			<div id="selection">
				<h2>FILTER LIST</h2>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<?php
						BuildFilters();
					?>
					<button type="submit" name="filter" value="1">APPLY FILTER</button>
				</form>
			</div>
			<div id="list">
					<table>
						<tr>
							<th class="l">NUMBER</th><th class="l">DATE</th><th class="l">ACCOUNT</th><th class="r">VALUE (Inc TAX)</th><th class="r">OUTSTANDING</th>
						</tr>
                        <?php
					$r = $DB->allInvoices();
					while ($invoice = $r->fetch_object("invoice"))
					{
						$paid = false;
						$overdue = false;
						$v = "i={$invoice->idinvoice}";
                        $s = InputParam::encryptFromString($v, $session->session_key);

						$o = $DB->journalInvoiceOutstanding($invoice->idinvoice);
						if ($o["GROSS"] == 0.00)
							$paid = true;
						else
						{
							if ((new DateTime())->getTimestamp() - (new DateTime($invoice->invoice_date))->getTimestamp() > 864000)
								$overdue = true;
							}

							if (($formfields["check_oustanding"] && !$paid) || ($formfields["check_paid"] && $paid) || ($formfields["check_overdue"] && $overdue))
						{
							echo "<tr>";
							$in = sprintf("%06d", $invoice->invoice_number);
							echo "<td><a href='Invoice.php?v={$s}'>{$in}</a></td>";
							$strDate = (new DateTime($invoice->invoice_date))->format('jS M Y');
							echo "<td>{$strDate}</td>";
							if ($invoice->invoice_cash_sale)
								echo "<td>CASH SALE</td>";
							else
								echo "<td>{$invoice->invoice_account_name->toHTML()}</td>";


							$gross = $DB->sumInvoiceLines($invoice->idinvoice)['GROSS'];
							$strV = "$" . number_format($gross, 2);

							echo "<td class='r'>{$strV}</td>";
							if ($o["GROSS"] == 0.00)
								echo "<td class='r'>PAID</td>";
							else {
								$outstanding = "$" . number_format($o["GROSS"], 2);
								echo "<td class='r'>{$outstanding}</td>";
							}

							if ($overdue)
							{
								echo "<td class='overdue'>OVERDUE</td>";
							}
							echo "</tr>";
						}
					}
                        ?>
					</table>
				</div>
		</div>
	</div>
</body>
</html>
