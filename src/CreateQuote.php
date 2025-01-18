<?php
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

if (! $session->quote_num)
{
	$session->quote_num = $DB->getNextQuoteNumber();
}

$strQuote_num = sprintf("%05d", $session->quote_num);
$view_quote = false;
$errmsg = "";

$formfields = ["date" => "","num" =>"","addr1"=>"", "addr2" => "", "addr3" => "", "addr4" => "","addr5"=>""];
$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone("Pacific/Auckland"));
$formfields["date"] = $dtNow->format("Y-m-d");
$formfields["num"] = $strQuote_num;


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$o_quote = $DB->o_getQuote($session->quote_num);
	if (!$o_quote)
	{
		$o_quote = $DB->createNewQuote($session->quote_num);
	}

	$formfields["date"] = FormList::getField("date");
	$formfields["num"] = FormList::getIntegerField("num");
	$formfields["name"] = FormList::getField("name");
	$formfields["addr1"] = FormList::getField("addr1");
	$formfields["addr1"] = FormList::getField("addr1");
	$formfields["addr2"] = FormList::getField("addr2");
	$formfields["addr3"] = FormList::getField("addr3");
	$formfields["addr4"] = FormList::getField("addr4");
	$formfields["addr5"] = FormList::getField("addr5");

	$o_quote->quote_date = $formfields["date"];
	$o_quote->quote_number = $formfields["num"];
	$o_quote->quote_contact_name = $formfields["name"];

	//Now go through the lines
	$nlines = max(count($_POST["item"]), count($_POST["description"]));
	$nlines = max(count($_POST["cost"]), $nlines);

	for($idx = 0; $idx < $nlines; $nlines++)
	{
		$o_line = new $quote_line($DB);
		$o_line->quote_line_quote = $o_quote->idquote;
		$o_line->quote_line_item = getIntegerIndexField("item", $idx);
		$o_line->quote_line_descripton = getIntegerIndexField("description", $idx);
		$o_line->quote_line_qty =  getDecimalIndexField("qty",$idx);
		$o_line->quote_line_cost = getCurrencyIndexField("cost", $idx);

		$o_line->create();

	}


	$o_quote->update($DB);
	$view_quote = true;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>CREATE QUOTE</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		
		#newquoteheading {margin-left: 20px;}
		#newquoteheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#form {margin: 20px;padding: 20px; border: solid 1px #888;border-radius: 8px;}
		#form label {display: block;}
		#form div.form_field {margin-bottom: 20px;}
		#form input.b {display: block;}
		#form input.s {margin-bottom: 6px;}
		#printpage {margin: 20px; border: solid 1px #888;display: none;}
		#printarea img {width: 375px;}
		#printarea table {border-collapse: collapse; width: 100%;}
		#printarea td.td1 {width: 70%;}
		#printarea td.td2 {width: 15%;}
		#printarea td.td3 {width: 15%;}
		td.sz1 {font-size: 14pt; font-weight: bold;}
		.r {text-align: right;}
		@media print {
			#printpage {width: 210cm; height: 297cm; margin: 0;}
			#printarea {margin-top: 2.5cm; margin-bottom: 2.5cm; margin-left: 2cm; margin-right: 2cm;}
			#printarea img {width: 10cm;}
		}
	</style>
	<script src="/js/st.js"></script>
	<script>
		view_quote = <?php echo ($view_quote) ? "true" : "false";?>;
		function createTextInputField(parent, name, size, classs, defaultVal) {
			let input = st.cea("input", parent);
			st.sa(input,"name",name);
			st.sa(input, "size", size);
			if (st.def(classs) && classs.length > 0)
				st.sa(input, "class", classs);
			if (st.def(defaultVal))
				st.sa(input, "value", defaultVal);
		}

		function createTDTextInputField(parent, name, size,classs,defaultVal) {
			let td = st.cea("td", parent);
			createTextInputField(td, name,size,classs,defaultVal);
		}

		function addQuoteLine()
		{
			let tbl = st.ge("tblquotelines");
			let tr = st.cea("tr", tbl);
			createTDTextInputField(tr, "item[]", "5");
			createTDTextInputField(tr, "description[]", "70");
			createTDTextInputField(tr, "qty[]", "3","");
			createTDTextInputField(tr, "cost[]", "5","r","$0.00");
		}

		function start() {
			addQuoteLine();
			if (view_quote) {
				st.ge("printpage").style.display = "block";
				st.ge("conatainer").style.display = "none";
			}
		}
	</script>
</head>
<body onload="start()">
	<div id="conatainer">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<?php
			if (strlen($errmsg) > 0)
			{
				echo "<div id='msg'><p class='err'>{$errmsg}</p></div>";
			}
			?>
			<div id="newquoteheading">
				<h1>NEW QUOTE DETAILS</h1>
			</div>
			<div id="form">
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div class="form_field">
						<label for="num">QUOTE NUMBER</label>
						<input id="num" name="num" type="text" size="5" value="<?php echo $formfields["num"]; ?>" readonly />
					</div>
					<div class="form_field">
						<label for="date">DATE</label>
						<input id="date" name="date" type="date" value="<?php echo $formfields["date"]; ?>" />
					</div>
					<div class="form_field">
						<label for="name">NAME</label>
						<input id="name" name="name" type="text" size="30" value="<?php echo $formfields["name"]; ?>" />
					</div>
					<div class="form_field">
						<label for="addr1">TO</label>
						<input id="addr1" name="addr1" type="text" class="s" size="30" value="<?php echo $formfields["addr1"]; ?>" />
						<input id="addr2" name="addr2" type="text" class="b s" size="30" value="<?php echo $formfields["addr2"]; ?>" />
						<input id="addr3" name="addr3" type="text" class="b s" size="30" value="<?php echo $formfields["addr3"]; ?>" />
						<input id="addr4" name="addr4" type="text" class="b s" size="30" value="<?php echo $formfields["addr4"]; ?>" />
						<input id="addr5" name="addr5" type="text" class="b" size="30" value="<?php echo $formfields["addr5"]; ?>" />
					</div>

					<div class="form_field">
						<h2>QUOTE LINES</h2>
						<button type="button" onclick="addQuoteLine()" title="Add more lines">+</button>
						<table id="tblquotelines">
							<tr>
								<th>ITEM</th><th>DESCRIPTION</th><th>QTY</th><th>COST</th><th>
							</tr>
						</table>
					</div>


					<button>CREATE QUOTE</button>
					<input type="hidden" name="formtoken" value="<?php echo $session->csrf_key;?>" />
				</form>
			</div>
			<div id="printpage">
				<div id="printarea">
					<table>
						<tr><td class="td1"><img src="images/precisetrees/Logo1-Transparent.png" /></td><td colspan="2" class="sz1 r">QUOTE</td></tr>
						<?php
							$strQuote_num = sprintf("%05d", $o_quote->quote_number);
							$dt = new DateTime($o_quote->quote_date);
							$strdate = $dt->format("j/n/Y");
							$dt->add(new DateInterval("P30D"));
							$strdateexpire = $dt->format("j/n/Y");

							echo "<tr>";
								echo "<td class='td1'></td>";
								echo "<td class='td2'>QUOTE #</td>";
								echo "<td class='td3'>{$strQuote_num}</td>";
							echo "</tr>";

							echo "<tr>";
								echo "<td class='td1'></td>";
								echo "<td class='td2'>DATE</td>";
								echo "<td class='td3'>{$strdate}</td>";
							echo "</tr>";

							echo "<tr>";
								echo "<td class='td1'>TO:</td>";
								echo "<td class='td2'>VALID UNTIL</td>";
								echo "<td class='td3'>{$strdateexpire}</td>";
							echo "</tr>";

							echo "<tr>";
								echo "<td class='td1'>{$o_quote->quote_contact_name->toHTML()}</td>";
								echo "<td class='td2'></td>";
								echo "<td class='td3'></td>";
							echo "</tr>";


						?>
						
						
						
						
						<tr><td class="td1"></td><td class="td2">QUOTE #</td><td class="td3 r"><?php echo $strQuote_num;?></td></tr>
						<tr><td class="td1"></td><td class="td2">DATE</td><td class="td3 r"><?php echo $formfields["date"]; ?></td></tr>
						<tr><td class="td1"></td><td class="td2">VALID UNTIL</td><td class="td3 r"><?php echo $formfields["date"]; ?></td></tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>
</html>