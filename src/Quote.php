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
$o_quote = null;
$session->quote_num = $DB->getNextQuoteNumber();


$strQuote_num = sprintf("%05d", $session->quote_num);
$errmsg = "";
$formedit = false;

$formfields = ["date" => "","num" =>"","name" => "","email" => "","phone" => "", "addr1"=>"", "addr2" => "", "addr3" => "", "addr4" => "","city"=>"", "no_cost_lines" => true];
$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone("Pacific/Auckland"));
$formfields["date"] = $dtNow->format("Y-m-d");
$formfields["num"] = $strQuote_num;
$o_taxrate = $DB->getTaxRateForClassAndDate(1, $dtNow);
$quoiteid = 0;

if (isset($_GET["v"]))
{
	//$s = Secure::sec_decryptParamPart($_GET["v"],base64_encode($session->session_key));
	$inputParams = InputParam::load($_GET["v"],$session->session_key);
	if ($inputParams)
	//if ($s && strlen($s) > 0)
	{
		$quoiteid = $inputParams->i;
		//parse_str($s,$a);
		if ($quoiteid && $quoiteid > 0)
		{
			//$quoiteid = intval($a["i"]);

			$o_quote = $DB->o_getQuoteById($quoiteid);
			$formfields["date"] = $o_quote->quote_date;
			$formfields["num"] = sprintf("%05d", $o_quote->quote_number);
			$formfields["addr1"] = $o_quote->quote_address1->toHTML();
			$formfields["addr2"] = $o_quote->quote_address2->toHTML();
			$formfields["addr3"] = $o_quote->quote_address3->toHTML();
			$formfields["addr4"] = $o_quote->quote_address4->toHTML();
			$formfields["city"] = $o_quote->quote_city->toHTML();
			$formfields["name"] = $o_quote->quote_contact_name->toHTML();
			$formfields["phone"] = $o_quote->quote_contact_phone->toHTML();
			$formfields["email"] = $o_quote->quote_contact_email->toHTML();
			$formfields["no_cost_lines"] = $o_quote->quote_option_no_item_cost;

			$o_lines = $DB->o_everyQuoteLine($quoiteid);
			$formfields["lines"] = array();
			foreach($o_lines as $o_line)
			{
				$line = array();
				$line["item"] = $o_line->quote_line_item;
				$line["desc"] = $o_line->quote_line_descripton->toHTML();
				$line["qty"] = $o_line->quote_line_qty;
				$line["cost"] = $o_line->quote_line_cost;
				$formfields["lines"] [] = $line;
			}

			$formedit = true;
		}
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	var_error_log($_POST, "POST");

	$mode = "create";
	$o_account = null;

	if (isset($_POST["editquote"]) && isset($_POST["qid"]))
	{
		$o_quote = $DB->o_getQuoteById(intval($_POST["qid"]));
		$mode = "edit";
	}
	else
		$o_quote = new quote();

	if ($o_quote)
	{
		$create_account_allowed = true;

		$formfields["date"] = FormList::getField("date");
		$formfields["num"] = FormList::getIntegerField("num");

		if (isset($_POST["account_select"]) && intval($_POST["account_select"]) != 0)
		{
			$o_account = $DB->getAccount(intval($_POST["account_select"]));
			$formfields["name"] = $o_account->account_name->toHTML();
			$formfields["email"] = $o_account->account_email->toHTML();
			$formfields["phone"] = $o_account->account_phone->toHTML();
			$formfields["addr1"] = $o_account->account_address1->toHTML();
			$formfields["addr2"] = $o_account->account_address2->toHTML();
			$formfields["addr3"] = $o_account->account_address3->toHTML();
			$formfields["addr4"] = $o_account->account_address4->toHTML();
			$formfields["city"] = $o_account->account_city->toHTML();
			$create_account_allowed = false;
		}
		else
		{
			$formfields["name"] = FormList::getField("name");
			$formfields["email"] = FormList::getField("email");
			$formfields["phone"] = FormList::getField("phone");
			$formfields["addr1"] = FormList::getField("addr1");
			$formfields["addr2"] = FormList::getField("addr2");
			$formfields["addr3"] = FormList::getField("addr3");
			$formfields["addr4"] = FormList::getField("addr4");
			$formfields["city"] = FormList::getField("city");

		}


		$formfields["no_cost_lines"] =  (FormList::getCheckboxField("no_cost_lines")) ? 1 : 0;

		$o_quote->quote_date = $formfields["date"];
		$o_quote->quote_number = $formfields["num"];
		$o_quote->quote_contact_name = $formfields["name"];
		$o_quote->quote_contact_phone = $formfields["phone"];

		$o_quote->quote_address1 = $formfields["addr1"];
		$o_quote->quote_address2 = $formfields["addr2"];
		$o_quote->quote_address3 = $formfields["addr3"];
		$o_quote->quote_address4 = $formfields["addr4"];
		$o_quote->quote_city = $formfields["city"];

		$o_quote->quote_contact_email = $formfields["email"];
		$o_quote->quote_option_no_item_cost = $formfields["no_cost_lines"];

		//Now go through the lines
		$nlines = max(count($_POST["item"]), count($_POST["description"]));
		$nlines = max(count($_POST["cost"]), $nlines);

		$DB->BeginTransaction();

		if (FormList::getCheckboxField("create_account") && $create_account_allowed)
		{
			$o_account = new account();
			$o_account->account_name = $o_quote->quote_contact_name->raw();
			$o_account->account_email = $o_quote->quote_contact_email->raw();
			$o_account->account_phone = $o_quote->quote_contact_phone->raw();
			$o_account->account_address1 = $o_quote->quote_address1->raw();
			$o_account->account_address2 = $o_quote->quote_address2->raw();
			$o_account->account_address3 = $o_quote->quote_address3->raw();
			$o_account->account_address4 = $o_quote->quote_address4->raw();
			$o_account->account_city = $o_quote->quote_city->raw();
			$o_account->account_sale_tax_class = 1;
			$o_account->account_contact_accounts = $o_quote->quote_contact_name->raw();
			$o_account = $o_account->create($DB);
			if ($o_account)
			{
				$DB->createAudit("customer", "Customer created # {$o_account->idaccount}",$user->iduser);
			}
		}

		if ($o_account)
			$o_quote->quote_customer_account = $o_account->idaccount;

		if ($mode == "edit")
			$o_quote->update($DB);
		else
			$o_quote = $o_quote->create($DB);

		//If this is an update thhen we need to delete all previous lines and add new.
		if ($mode == "edit")
			$DB->deleteAllQuoteLinesForQuote($o_quote->idquote);

		for($idx = 0; $idx < $nlines; $idx++)
		{
			$o_line = new quote_line();
			$o_line->quote_line_quote = $o_quote->idquote;
			$o_line->quote_line_item = FormList::getIntegerIndexField("item", $idx);
			$o_line->quote_line_descripton = FormList::getIndexField("description", $idx);
			$o_line->quote_line_qty = FormList::getDecimalIndexField("qty",$idx);
			$o_line->quote_line_cost = FormList::getCurrencyIndexField("cost", $idx);

			$quoteCreate = false;
			if ($o_line->quote_line_item != 0)
				$quoteCreate = true;
			if (strlen($o_line->quote_line_descripton->raw()) > 0)
				$quoteCreate = true;
			if ($o_line->quote_line_qty > 0)
				$quoteCreate = true;
			if ($o_line->quote_line_cost > 0)
				$quoteCreate = true;

			if ($quoteCreate)
				$o_line->create($DB);
		}


		if ($DB->EndTransaction())
		{
			if ($mode == "edit")
				$DB->createAudit("quote", "Quote edited # {$o_quote->quote_number}", $user->iduser);
			else
				$DB->createAudit("quote", "Quote created # {$o_quote->quote_number}", $user->iduser);
			$v = "i={$o_quote->idquote}";
			$s = InputParam::encryptFromString($v, $session->session_key);
			$session->quote_num = 0;
			header("Location: ViewQuote.php?v={$s}");
			exit();
		}
		else
			$errmsg = "ERROR:Database update error";
	}
	else
		$errmsg = "ERROR:Database quote number already exists";

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
		#printpage {margin: auto;display: none;max-width: 800px;}
		#printarea img {width: 375px;}
		#printarea table {border-collapse: collapse; width: 100%;}
		#printarea td.td1 {width: 70%;}
		#printarea td.td2 {width: 15%;}
		#printarea td.td3 {width: 15%;}
		#tabelline {width: 100%;}
		#gap {height: 20px;}
		td.sz1 {font-size: 14pt; font-weight: bold;}
		.r {text-align: right;}
		.l {text-align: left;}
		.lcol1 {width: 10%;vertical-align:top;}
		.lcol2 {width: 60%;vertical-align:top;}
		.lcol3 {width: 15%;vertical-align:top;}
		.lcol4 {width: 15%;vertical-align:top;}
		.tdgap1 {height: 15px;}
		.tdgap2 {height: 45px;}
		@media print {
			#printpage {width: 21cm; height: 29.7cm; margin: 0;border: none;}
			#printarea {margin-top: 2.5cm; margin-bottom: 2.5cm; margin-left: 2cm; margin-right: 2cm;}
			#printarea img {width: 10cm;}
			#gap {height: 1cm;}
			#feet p {margin-left: 1.5cm;margin-right: 1.5cm;}
			.tdgap1 {height: 0.5cm;}
			.tdgap2 {height: 1.5cm;}
		}
	</style>
	<script src="/js/st.js"></script>
	<script>
		var g_insertfirstline = <?php echo (isset($formfields["lines"]) && count($formfields["lines"]) > 0) ? "false" : "true";?>;
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
			if (g_insertfirstline)
				addQuoteLine();
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
					<?php
					if (!$formedit)
					{
						echo "<div class='form_field'>";
						echo "<label for='account_select'>SELECT DETAILS FROM CLIENT ACCOUNT</label>";
						echo "<select id='account_select' name='account_select'>";
						echo "<option value='0'></option>";
						$r = $DB->allAccounts("where account_deleted = 0", "order by account_name");
						while ($o_account = $r->fetch_object("account"))
						{
							echo "<option value='{$o_account->idaccount}'>{$o_account->account_name->toHTML()}</option>";
						}
						echo "</select>";
						echo "</div>";
					}
					?>
					<div class="form_field">
						<label for="name">NAME</label>
						<input id="name" name="name" type="text" size="30" value="<?php echo $formfields["name"]; ?>" />
					</div>
					<div class="form_field">
						<label for="addr1">ADDRESS</label>
						<input id="addr1" name="addr1" type="text" class="s" size="30" value="<?php echo $formfields["addr1"]; ?>" />
						<input id="addr2" name="addr2" type="text" class="b s" size="30" value="<?php echo $formfields["addr2"]; ?>" />
						<input id="addr3" name="addr3" type="text" class="b s" size="30" value="<?php echo $formfields["addr3"]; ?>" />
						<input id="addr4" name="addr4" type="text" class="b s" size="30" value="<?php echo $formfields["addr4"]; ?>" />
						<label for="city">CITY</label>
						<input id="city" name="city" type="text" class="b" size="30" value="<?php echo $formfields["city"]; ?>" />
					</div>

					<div class="form_field">
						<label for="email">EMAIL</label>
						<input id="email" name="email" type="email" size="50" value="<?php echo $formfields["email"]; ?>" />
					
					</div>
					
					<div class="form_field">
						<label for="phone">PHONE</label>
						<input id="phone" name="phone" type="text" size="18" value="<?php echo $formfields["phone"];?>" maxlength="21" />
					</div>
					<?php
					if (!$formedit)
					{
						echo "<div class='form_field'>";
						echo "<label for='create_account'>CREATE A NEW CUSTOMER ACCOUNT</label>";
						echo "<input id='create_account' name='create_account' type='checkbox' />";
						echo "</div>";
					}
					?>
					<div class="form_field">
						<h2>QUOTE LINES</h2>						
						<div class="form_field">
							<label for="no_cost_lines">DO NOT SHOW COST ON LINES</label>
							<?php
							$checked = "checked";
							if (!$formfields["no_cost_lines"])
								$checked = "";
							echo "<input id='no_cost_lines' name='no_cost_lines' type='checkbox' {$checked} />";
							?>
						</div>
						<button type="button" onclick="addQuoteLine()" title="Add more lines">+</button>
						<table id="tblquotelines">
							<tr>
								<th>ITEM</th><th>DESCRIPTION</th><th>QTY</th><th>COST</th><th>
							</tr>
							<?php
								if (isset($formfields["lines"]) && count($formfields["lines"]) > 0)
								{
									foreach($formfields["lines"] as $line)
									{
										echo "<tr>";
										echo "<td><input name='item[]' size='5' value='{$line["item"]}'/></td>";
										echo "<td><input name='description[]' size='70' value='{$line["desc"]}'/></td>";
										echo "<td><input name='qty[]' size='3' value='{$line["qty"]}'/></td>";
										echo "<td><input name='cost[]' size='5' class='r' value='{$line["cost"]}'/></td>";
										echo "</tr>";
									}
								}
							?>
						</table>
					</div>
					<?php
					if ($formedit)
					{
						echo "<input type='hidden' name='qid' value='{$quoiteid}' />";
						echo "<button name='editquote'>EDIT QUOTE</button>";
					}
					else
						echo "<button name='createquote'>CREATE QUOTE</button>";
					?>

					<input type="hidden" name="formtoken" value="<?php echo $session->csrf_key;?>" />
				</form>
			</div>
		</div>
	</div>
</body>
</html>