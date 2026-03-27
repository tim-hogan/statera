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
$selff = trim($_SERVER["PHP_SELF"], "/");
$filters = ["open" => ["name" => "OPEN", "field" => "check_open"],
		   "accepted" => ["name" => "ACCEPTED", "field" => "check_accepted"],
		   "completed" => ["name" => "COMPLETED", "field" => "check_completed"],
		   "declined" => ["name" => "DECLINED", "field" => "check_declined"]
		   ];

if (isset($_SESSION["filters"]))
	$formfields = $_SESSION["filters"];
else
	$formfields = ["check_open" => true, "check_accepted" => false, "check_completed" => false, "check_declined" => false];

$_SESSION["filters"] = $formfields;

function BuildFilters()
{
	global $filters;
	global $formfields;

	foreach($filters as $key => $filter)
	{
		echo "<label for='{$filter["field"]}'>{$filter["name"]}</label>";
		$checked = "";
		if ($formfields[$filter["field"]])
			$checked = "checked";
		echo "<input type='checkbox' id='{$filter["field"]}' name='{$filter["field"]}' {$checked} />";
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

	if (isset($_POST["filter"]))
	{
		foreach ($filters as $key => $filter)
		{
			if (isset($_POST[$filter["field"]]) && $_POST[$filter["field"]] == "on")
				$formfields[$filter["field"]] = true;
			else
				$formfields[$filter["field"]] = false;
		}

		$_SESSION["filters"] = $formfields;

	}

	if (isset($_POST["accept"]))
	{
		$quoteid = intval($_POST["accept"]);
		$DB->markQuoteAccepted($quoteid);
	}
	if (isset($_POST["complete"]))
	{
		$quoteid = intval($_POST["complete"]);
		$DB->markQuoteCompleted($quoteid);
	}

	if (isset($_POST["decline"]))
	{
		$quoteid = intval($_POST["decline"]);
		$DB->markQuoteDeclined($quoteid);
	}



}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>QUOTES</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 20px;}
		#expheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#expheading h2 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#quoteselection {margin-bottom: 16px;padding: 20px;border: solid 1px #888;}
		#quoteselection label {margin-bottom: 0; font-size: 9pt;}
		#quoteselection input {margin-right: 28px;}
		#quoteselection h2 {margin-top: 0;}
		#list {padding: 20px;border: solid 1px #888;border-radius: 8px;}
		#list th,td {padding-right: 20px;}
		#list button {font-size: 9pt;}
		.r {text-align: right;}
		.b {font-weight: bold;}

	</style>
	<script src="/js/st.js"></script>
	<script>
		var selff = "<?php echo $selff;?>";

		function markAccepted(n) {
			let id = st.ga(n,"_qid");
			let form = st.cea("FORM", document.body);
			form.method = "POST";
			form.action = selff;
			let inp = st.cea("INPUT", form);
			inp.name = "accept";
			inp.value = id;
			form.submit();
		}

		function markComplete(n) {
			let id = st.ga(n,"_qid");
			let form = st.cea("FORM", document.body);
			form.method = "POST";
			form.action = selff;
			let inp = st.cea("INPUT", form);
			inp.name = "complete";
			inp.value = id;
			form.submit();
		}
		function markDeclined(n) {
			let id = st.ga(n,"_qid");
			let form = st.cea("FORM", document.body);
			form.method = "POST";
			form.action = selff;
			let inp = st.cea("INPUT", form);
			inp.name = "decline";
			inp.value = id;
			form.submit();
		}
	</script>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<div id="expheading">
				<h1>LIST OF QUOTES</h1>
				<div id="quoteselection">
					<h2>FILTER LIST</h2>
					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">						
					<?php
						BuildFilters();
					?>
					<button type="submit" name="filter" value="1">APPLY FILTER</button>
					</form>
				</div>
			</div>
			<div id="list">
				<table>
					<tr><th>QUOTE #</th><th>DATE</th><th>TO</th><th>STATUS</th><th class="r">NET AMMOUNT</th><th></th></tr>
					<?php
					$r = $DB->allQuotesDesc();
					while ($o_quote = $r->fetch_object("quote"))
					{
						$filter = $filters[$o_quote->quote_status->raw()];
						//var_error_log($filter, "filter");
						//var_error_log($formfields[$filter["field"]], "formfields for filter;");
						if ($formfields[$filter["field"]])
						{
							$netTotal = $DB->netSumQuoteLines($o_quote->idquote);
							$v1 = LedgerAmount::format1($netTotal);
							$strQuoteNum = sprintf("%05d",$o_quote->quote_number);
							$strDate = (new DateTime($o_quote->quote_date))->format("j/n/Y");
							$button = "";
							$button2 = "";
							$button3 = "";
							if ($o_quote->quote_status->raw() == "open")
								$button = "<button _qid='{$o_quote->idquote}' onclick='markAccepted(this)'>MARK AS ACCEPTED</button>";
							if ($o_quote->quote_status->raw() == "open" || $o_quote->quote_status->raw() == "accepted")
								$button3 = "<button _qid='{$o_quote->idquote}' onclick='markDeclined(this)'>MARK AS DECLINED</button>";
							if ($o_quote->quote_status->raw() == "accepted")
								$button2 = "<button _qid='{$o_quote->idquote}' onclick='markComplete(this)'>MARK AS COMPLETED</button>";
							$v = "i={$o_quote->idquote}";
							$s = InputParam::encryptFromString($v, $session->session_key);
							echo "<tr><td><a href='ViewQuote.php?v={$s}'>{$strQuoteNum}</a></td><td>$strDate</td><td>{$o_quote->quote_contact_name->toHTML()}</td><td>{$o_quote->quote_status->toHTML()}</td><td class='r'>{$v1}</td><td>{$button}</td><td>{$button3}</td><td>{$button2}</td><td><button onclick='location.href=\"Quote.php?v={$s}\";'>EDIT</button></td></tr>";
						}
					}
					?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>
