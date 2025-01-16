<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classInputParam.php";
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

require dirname(__FILE__) . "/includes/commonSession.php";
$formfields = ["date" =>"","amnt" => "", "desc" => ""];
$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone("Pacific/Auckland"));
$formfields["date"] = $dtNow->format("Y-m-d");
$formfields["amnt"] = "$0.00";
$errmsg="";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

	$decription = "Bank Loan";
	$amnt = 0.0;

	if (isset($_POST["date"]))
		$formfields["date"] = $_POST["date"];
	else
		$errmsg = "ERROR: No Date specified.";

    if (isset($_POST["desc"]))
        $formfields["desc"] = FormList::getField("desc");

	if (isset($_POST["amnt"])) {
        $amnt = FormList::getCurrencyField("amnt");
		$formfields["amnt"] = sprintf("$%8.2f",$amnt);
	}

	if (strlen($errmsg) == 0)
	{
		$rslt = $DB->newLoan($formfields["date"],$amnt,$formfields["desc"]);
		if (!$rslt)
			$errmsg = "ERROR: Database error occured";
	}

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>NEW LOAN</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#newloanheading {margin-left: 20px;}
		#newloanheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;} 
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
			<div id="newloanheading">
				<h1>NEW LOAN DETAILS</h1>
			</div>
			<div id="form">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div class="form_field">
						<label for="date">DATE</label>
						<input id="date" name="date" type="date" value="<?PHP echo $formfields["date"]; ?>" />
					</div>
					<div class="form_field">
						<label for="amnt">LOAN AMOUNT</label>
						<input type="text" id="amnt" name="amnt" size="8" class="r" value="<?PHP echo $formfields["amnt"];?>" />
					</div>
					<div class="form_field">
						<label for="desc">LOAN ACCOUNT DESCRIPTION</label>
						<input type="text" id="desc" name="desc" size="40" value="<?PHP echo $formfields["desc"];?>" />
					</div>
					<button>CREATE LOAN</button>
				</form>
			</div>
		</div>
</body>
</html>