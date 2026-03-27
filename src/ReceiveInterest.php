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

$formfields = ["date" => "", "interest" => "$0.00", "desc" => ""];
$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone("Pacific/Auckland"));
$formfields["date"] = $dtNow->format("Y-m-d");
$errmsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$interest = 0.0;

	if (!$session->checkCSRF())
	{
		$DB->createAudit("security", "{$selff} [" . __LINE__ . "] Invalid CSRF on form input");
		header("Location: SecurityError.php");
		exit();
	}

	if (isset($_POST["date"]))
		$formfields["date"] = $_POST["date"];
	else
		$errmsg = "ERROR: No Date specified.";

	if (isset($_POST["interest"]))
	{
		$interest = FormList::getCurrencyField("interest");
		$formfields["interest"] = sprintf("$%8.2f", $interest);
	}


	$formfields["desc"] = FormList::getField("desc");

	if ($interest == 0.0)
	{
		$errmsg = "ERROR: You must enter an interest amount.";
	}

	if (strlen($errmsg) == 0)
	{
		$undo = new Undo("Interest received transaction");

		if (strlen($formfields["desc"]) == 0)
			$formfields["desc"] = "Interest received";
		$xtn = $DB->ReceivedInterest($formfields["date"], $interest, $formfields["desc"]);
		$undo->add(new UndoAction("delete", "journal", "journal_xtn", $xtn));
		$undolist->push($undo);
		$DB->updateUndoList($user->iduser, $undolist->toJSON());
		
		header("Location: /");
		exit();

	}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>INTEREST RECEIVED</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
			#inthd {margin-left: 20px;}
			#inthd h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#form {margin: 20px;padding: 20px; border: solid 1px #888;border-radius: 8px;}
		#form label {display: block;}
		#form div.form_field {margin-bottom: 20px;}
		.r {text-align: right;}
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
			<div id="inthd">
				<h1>INTEREST RECEIVED TRANSACTION</h1>
			</div>
			<div id="form">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div class="form_field">
						<label for="date">DATE</label>
						<input id="date" name="date" type="date" value="<?PHP echo $formfields["date"]; ?>" />
					</div>
					<div class="form_field">
						<label for="desc">DESCRIPTION</label>
						<input id="desc" type="text" name="desc" size="50" value="<?PHP echo $formfields["desc"]; ?>" />
					</div>
					<div class="form_field">
						<label for="interest">CREDIT INTEREST (INTEREST RECEIVED)</label>
						<input id="interest" type="text" class="r" name="interest" size="10" value="<?PHP echo $formfields["interest"]; ?>" />
					</div>
					<button>ENTER</button>
					<input type="hidden" name="formtoken" value="<?php echo $session->csrf_key;?>" />
				</form>
			</div>
		</div>
</body>
</html>