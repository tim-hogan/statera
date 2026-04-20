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

$o_journal = null;

if (isset($_GET['v']))
{
	$inputParams = null;
	$inputParams = InputParam::load($_GET['v'], $session->session_key);

	var_error_log($inputParams, "inputparams");

	if (!$inputParams)
	{
		error_log("ERROR: {$selff} [" . __LINE__ . "] Unable to view expense key");
		header("Location: SecurityError.php");
		exit();
	}

	$journal_id = intval($inputParams->j);
	$j_pair = $DB->getJournalPair($journal_id);


	$o_journal = $DB->o_getJournal($journal_id);
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (!$session->checkCSRF())
	{
		$DB->createAudit("security", "{$selff} [" . __LINE__ . "] Invalid CSRF on form input");
		header("Location: SecurityError.php");
		exit();
	}

	if (isset($_POST["confirm"]))
	{
		if (isset($_POST["journal_src"]))
		{
			$key = InputParam::load($_POST["journal_src"], $session->session_key);
			$j_pair = $DB->getJournalPair(intval($key->j));

			//Check that the links align
			if (
				$j_pair[0]->idjournal == $j_pair[1]->journal_link &&
				$j_pair[1]->idjournal == $j_pair[0]->journal_link
			)
			{
				$DB->deleteAllJournalForSource($j_pair[0]->idjournal,$user->iduser);
				header("Location: ViewExpenses.php");
				exit();
			}
		}
	}
	else
	{
		header("Location: ViewExpenses.php");
		exit();
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>COMNFIRM</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 20px;}
		#form {margin-top: 24px;}
		h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		h2 {color: #6b6ba7;font-family: Akshar;font-weight: 300; font-size: 1.2em;}
		#cashxtns td {padding-right: 24px;}
	</style>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<h1>PLEASE CONFIRM</h1>
			<p>You are about to delete the following transaction from the journal:</p>
			<?php
			if ($j_pair)
			{
				echo "<h2>Expense transaction</h2>";

				$strDate = (new DateTime($j_pair[0]->journal_date))->format("j/n/Y");
				$strgross = LedgerAmount::format1($j_pair[0]->journal_gross);
				echo "<table>";
				echo "<tr><td>DATE</td><td>{$strDate}</td></tr>";
				echo "<tr><td>DESCRIPTION</td><td>{$j_pair[0]->journal_description->toHTML()}</td></tr>";
				echo "<tr><td>AMOUNT</td><td>{$strgross}</td></tr>";
				echo "</table>";

				$o_jSources = $DB->getAllForSource($j_pair[0]->idjournal);

				if (count($o_jSources) > 2)
				{
					echo "<div id='cashxtns'>";
					echo "<h2>Cash Transactions</h2>";
					echo "<table>";
					foreach($o_jSources as $s)
					{
						if ($s->idjournal != $j_pair[0]->idjournal && $s->idjournal != $j_pair[1]->idjournal)
						{
							$o_chart = $DB->getChart($s->journal_chart);
							if ($o_chart->chart_type->raw() == "cash")
							{
								$strDate = (new DateTime($s->journal_date))->format("j/n/Y");
								$strpaid = LedgerAmount::format1(-($s->journal_gross));
								echo "<tr><td>{$strDate}</td><td>PAID</td><td>{$strpaid}</td></tr>";
							}
						}
					}
					echo "</table>";
					echo "</div>";
				}

			}
			?>
			<div id="form">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				 <?php 
					echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>";
					$s1 = InputParam::encryptFromString("j={$o_journal->idjournal}", $session->session_key);
					echo "<input type='hidden' name='journal_src' value='{$s1}' />";
				 ?>
				<button type="submit" name="confirm">CONFIRM</button>
				<button type="submit" name="cancel">CANCEL</button>
				</form>
			</div>
		</div>
	</div>
</body>
</html>