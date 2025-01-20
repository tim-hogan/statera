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

$formfields = ["date" => "","coa"=>0,"cr"=>"$0.00","dr"=>"$0.00","interest"=>"$0.00","desc"=>""];
$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone("Pacific/Auckland"));
$formfields["date"] = $dtNow->format("Y-m-d");
$errmsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$cr = 0.0;
	$dr = 0.0;
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

	if (isset($_POST["coa"]))
	{
		$coa = FormList::getIntegerField("coa");
		if ($coa == 0)
			$errmsg = "ERROR: No loan account selected";
	}

	if (isset($_POST["cr"])) 
	{
		$cr = FormList::getCurrencyField("cr");
		$formfields["cr"] = sprintf("$%8.2f", $cr);
	}

	if (isset($_POST["dr"])) 
	{
		$dr = FormList::getCurrencyField("dr");
		$formfields["dr"] = sprintf("$%8.2f", $dr);
	}

	if (isset($_POST["interest"])) 
	{
		$interest = FormList::getCurrencyField("interest");
		$formfields["interest"] = sprintf("$%8.2f", $interest);
	}

	
	$formfields["desc"] = FormList::getField("desc");
	

	//Check that we have one of cr dr or interest
	$sum = $cr + $dr + $interest;
	if ($cr != 0 && $cr != $sum)
		$errmsg = "ERROR: You msut ony select one of CREDIT LOAN DEBIT LOAN OR INTEREST";
	if ($dr != 0 && $dr != $sum)
		$errmsg = "ERROR: You msut ony select one of CREDIT LOAN DEBIT LOAN OR INTEREST";
	if ($interest != 0 && $interest != $sum)
		$errmsg = "ERROR: You msut ony select one of CREDIT LOAN DEBIT LOAN OR INTEREST";


	//Check that we have
	if (strlen($errmsg) == 0)
	{
		error_log("CR {$cr} DR {$cr} INTEREST {$interest}");
		$undo = new Undo("Loan transaction");
		if ($cr > 0) 
		{
			if (strlen($formfields["desc"]) == 0)
				$formfields["desc"] = "Loan principile credit";
			$xtn = $DB->LoanCrPrinciple($formfields["date"],$coa, $cr, $formfields["desc"]);
			$undo->add(new UndoAction("delete", "journal", "journal_xtn", $xtn));
			$undolist->push($undo);
			$DB->updateUndoList($user->iduser, $undolist->toJSON());
		}
		if ($dr > 0) 
		{
			if (strlen($formfields["desc"]) == 0)
				$formfields["desc"] = "Loan extended";
			$xtn = $DB->LoanDrPrinciple($formfields["date"], $coa, $dr, $formfields["desc"]);
			var_error_log($xtn, "xtn");
			
			
			$undo->add(new UndoAction("delete", "journal", "journal_xtn", $xtn));
			$undolist->push($undo);
			$DB->updateUndoList($user->iduser, $undolist->toJSON());
		}
		if ($interest > 0)
		{
			if (strlen($formfields["desc"]) == 0)
				$formfields["desc"] = "Interest accrued";
			$xtn = $DB->LoanInterest($formfields["date"], $coa, $interest, $formfields["desc"]);
			$undo->add(new UndoAction("delete", "journal", "journal_xtn", $xtn));
			$undolist->push($undo);
			$DB->updateUndoList($user->iduser, $undolist->toJSON());           
		}
	}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>LOAN TRANSACTION</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#loaneading {margin-left: 20px;}
		#loaneading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
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
			<div id="loaneading">
				<h1>LOAN TRANSACTIONS</h1>
			</div>
			<div id="form">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div class="form_field">
						<label for="date">DATE</label>
						<input id="date" name="date" type="date" value="<?PHP echo $formfields["date"]; ?>" />
					</div>
					<div class="form_field">
						<label for="coa">LOAN</label>   
						<select id="coa" name="coa">
							<option value="0">[SELECT WHICH LOAN]</option>
							<?php
								$allcharts = $DB->everyChartLoan();
								foreach($allcharts as $chart)
								{
									$selected = "";
									if ($formfields["coa"] == $chart["chart_code"])
										$selected = "selected";
									echo "<option value='{$chart["chart_code"]}' {$selected}>{$chart["chart_description"]}</option>";
								}
							?>
						</select>
					</div>
					<div class="form_field">
						<label for="desc">DESCRIPTION</label>   
						<input id="desc" type="text" name="desc" size="30" value="<?PHP echo $formfields["desc"]; ?>" />
					</div>
					<div class="form_field">
						<label for="loanacct">CREDIT LOAN (PAY BACK OFF LOAN)</label>   
						<input id="cr" type="text" class="r" name="cr" size="10" value="<?PHP echo $formfields["cr"]; ?>" />
					</div>
					 <div class="form_field">
						<label for="dr">DEBIT LOAN (EXTEND LOAN)</label>   
						<input id="dr" type="text" class="r" name="dr" size="10" value="<?PHP echo $formfields["dr"]; ?>"  />
					</div>
					 <div class="form_field">
						<label for="interest">DEBIT INTEREST (INTREST ACCRUED ON LOAN)</label>   
						<input id="interest" type="text" class="r" name="interest" size="10" value="<?PHP echo $formfields["interest"]; ?>" />
					</div>
					<button>ENTER</button>
					<input type="hidden" name="formtoken" value="<?php echo $session->csrf_key;?>" />

			   </form>
			</div>
		</div>
	</div>

</body>
</html>
