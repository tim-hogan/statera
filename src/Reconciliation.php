<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classTime.php";
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


function csvToArray($filename, $delimiter = ',')
{
	if (!file_exists($filename) || !is_readable($filename)) {
		return false;
	}

	$header = null;
	$data = array();

	// Open the file in read mode ('r')
	if (($handle = fopen($filename, 'r')) !== FALSE) {
		// Read each line (row) of the CSV file
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
			if (!$header) {
				// Use the first row as the header/keys
				$header = $row;
			} else {
				// Combine header keys with the current row's values to create an associative array
				if (count($header) == count($row)) {
					$data[] = array_combine($header, $row);
				}
			}
		}
		// Close the file handle
		fclose($handle);
	}

	return $data;
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

$strErr = "";
$errorList = array();
$totalerr1 = 0.0;
$totalerr2 = 0.0;

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (!$session->checkCSRF())
	{
		$DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
		header("Location: SecurityError.php");
		exit();
	}

	if (isset($_FILES["xtnfile"]) && isset($_FILES["xtnfile"] ["tmp_name"]) && strlen($_FILES["xtnfile"] ["tmp_name"]) > 0)
	{

		$data = csvToArray($_FILES["xtnfile"]["tmp_name"]);


		if ($_POST["date"] && strlen($_POST["date"]) > 0)
		{

			$fromDate = new DateTime($_POST["date"]);
			$line = 1;

			foreach ($data as $r)
			{
				//Parse the data
				$a = explode("/", $r["Date"]);
				$day = sprintf("%02d", intval($a[0]));
				$month = sprintf("%02d", intval($a[1]));
				$year = sprintf("%04d", intval($a[2]));
				$dt = new DateTime("{$year}-{$month}-{$day}");

				if ($dt->getTimestamp() >= $fromDate->getTimestamp())
				{
					//Look for the transaction in the journal
					$rslt = $DB->FindCashTransaction($dt, $r["Amount"]);
					if ($rslt == 0)
					{
						//Now check around this date for the exact amount (1 week either side).
						$j = $DB->FindCashTransactionNear($dt, $r["Amount"]);
						if ($j)
						{
							$xntDate = new DateTime($j["journal_date"]);
							$strxntDate = $xntDate->format("d/m/Y");

							$s = array();
							$s["date"] = $r["Date"];
							$s["details"] = $r["Details"];
							$s["particulars"] = $r["Particulars"];
							$s["code"] = $r["Code"];
							$s["amount"] = $r["Amount"];
							$s["near"] = true;
							$s["what"] = "not found in Precisetrees accounts for date, possible transaction on {$strxntDate}  [Line {$line} of inputed file]";
							$s["diffdate"] = true;

							//$s = "{$r["Date"]} Bank Transaction {$r["Details"]} {$r["Particulars"]} {$r["Code"]} for {$r["Amount"]} not found in Precisetrees accounts for date, possible transaction on {$strxntDate}  [Line {$line} of inputed file]";
							$totalerr2 += $r["Amount"];
						}
						else
						{
							$s = array();
							$s["date"] = $r["Date"];
							$s["details"] = $r["Details"];
							$s["particulars"] = $r["Particulars"];
							$s["code"] = $r["Code"];
							$s["amount"] = $r["Amount"];
							$s["near"] = false;
							$s["what"] = "not found in Precisetrees accounts  [Line {$line} of inputed file]";
							$s["diffdate"] = false;

							//$s = "{$r["Date"]} Bank Transaction {$r["Details"]} {$r["Particulars"]} {$r["Code"]} for {$r["Amount"]} not found in Precisetrees accounts  [Line {$line} of inputed file]";
							$totalerr1 += $r["Amount"];
						}
						$errorList[] = $s;
					}

					if ($rslt > 1)
					{
						$s = "{$r["Date"]} Bank Transaction {$r["Details"]} {$r["Particulars"]} {$r["Code"]} for {$r["Amount"]} more than one transaction for this date and amount found in Precisetrees accounts [Line {$line} of inputed file]";
						$errorList[] = $s;
					}

				}

				$line++;
			}
		} else
		{
			error_log("No date specified");
			$strErr = "You must specifiy a start date";
		}
	}
	else
	{
		$strErr = "You must select a bank transaction file";
	}
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>RECONCILATION TOOL</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
	h1 {margin-left: 20px;font-family: Akshar;font-weight: normal;color: #44F;}
	#entry {margin: 20px; padding: 20px; border: solid 1px #888; border-radius: 8px;}
	#entry input[type='submit'] {margin-top: 20px;}
	#entry a {color: blue; cursor:pointer}
	#entry label {display: block; font-size: 9pt;}
	#entry div.div_field {margin-bottom: 16px;}
	#results {margin: 20px; padding: 20px; border: solid 1px #888; border-radius: 8px;}
	#results tr.near {color: darkorange;}
	.r {text-align: right;}
	.b {font-weight: bold;}
	.err {color: red;}
	</style>
	<script>
	</script>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<h1>RECONCILATION TOOL</h1>
			<div id="entry">
				<div id="msg">
					<?php
					if (strlen($strErr) > 0)
					{
						echo "<p class='err'>{$strErr}</p>";
					}
					?>
				</div>
				<form method="post" enctype='multipart/form-data' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div class="div_field">
						<label for="date">ENTER START DATE</label>
						<input id="date" type="date" name="date" />
					</div>
					<div>
						<label for="xtnfile">SELECT THE DOWNLOADED CSV FILE FROM BANK</label>
						<input id="xtnfile" name="xtnfile" type="file" accept=".csv" />
					</div>
					<?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
					<input type="submit" name="analyze" value="ANALYZE" />
				</form>    
			</div>
			<div id="results">
				<table>
					<?php
						foreach($errorList as $l)
						{
							if ($l["near"])
								echo "<tr class='near'>";
							else
								echo "<tr>";
							echo "<td>{$l["date"]}</td>";
							echo "<td>{$l["details"]}</td>";
							echo "<td>{$l["particulars"]}</td>";
							echo "<td>{$l["code"]}</td>";
							if (! $l["diffdate"])
							{
								echo "<td class='r b'>{$l["amount"]}</td>";
								echo "<td></td>";
							}
							else
							{
								echo "<td></td>";
								echo "<td class='r b'>{$l["amount"]}</td>";
							}
							echo "<td>{$l["what"]}</td>";
							echo "</tr>";
						}
						if (count($errorList) > 0)
						{
							$totalerr1 = number_format($totalerr1, 2);
							$totalerr2 = number_format($totalerr2, 2);
							echo "<tr><td>Total Error</td><td></td><td></td><td></td><td class='r b'>{$totalerr1}</td><td class='r b'>{$totalerr2}</td></tr>";
						}
					?>
				</table>
			</div>
		</div>
	</div>
	<?php include ("./includes/footer.html");?>
</body>
</html>
