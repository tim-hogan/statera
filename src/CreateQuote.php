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

$formfields = ["date" => "","num" =>"","name" => "","email" => "","phone" => "", "addr1"=>"", "addr2" => "", "addr3" => "", "addr4" => "","city"=>""];
$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone("Pacific/Auckland"));
$formfields["date"] = $dtNow->format("Y-m-d");
$formfields["num"] = $strQuote_num;
$o_taxrate = $DB->getTaxRateForClassAndDate(1, $dtNow);


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    var_error_log($_POST, "POST");

	$o_quote = $DB->o_getQuoteByNum($session->quote_num);
	if (!$o_quote)
	{
		$o_quote = new quote();

		$formfields["date"] = FormList::getField("date");
		$formfields["num"] = FormList::getIntegerField("num");
		$formfields["name"] = FormList::getField("name");
		$formfields["email"] = FormList::getField("email");
		$formfields["email"] = FormList::getField("phone");
		$formfields["addr1"] = FormList::getField("addr1");
		$formfields["addr1"] = FormList::getField("addr1");
		$formfields["addr2"] = FormList::getField("addr2");
		$formfields["addr3"] = FormList::getField("addr3");
		$formfields["addr4"] = FormList::getField("addr4");
		$formfields["city"] = FormList::getField("city");

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

		//Now go through the lines
		$nlines = max(count($_POST["item"]), count($_POST["description"]));
		$nlines = max(count($_POST["cost"]), $nlines);

		$DB->BeginTransaction();

		if (isset($_POST["create_account"]) && $_POST["create_account"] == "on")
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
                $o_quote->quote_customer_account = $o_account->idaccount;
        }


		$o_quote = $o_quote->create($DB);

		for($idx = 0; $idx < $nlines; $idx++)
		{
			$o_line = new quote_line();
			$o_line->quote_line_quote = $o_quote->idquote;
			$o_line->quote_line_item = FormList::getIntegerIndexField("item", $idx);
			$o_line->quote_line_descripton = FormList::getIndexField("description", $idx);
			$o_line->quote_line_qty = FormList::getDecimalIndexField("qty",$idx);
			$o_line->quote_line_cost = FormList::getCurrencyIndexField("cost", $idx);

			$o_line->create($DB);

		}


		if ($DB->EndTransaction())
		{
			$v = "i={$o_quote->idquote}";
			$s = Secure::sec_encryptParam($v, base64_encode($session->session_key));
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

					<div class="form_field">
                        <label for="create_account">CREATE A NEW CUSTOMER ACCOUNT</label>
						<input id="create_account" name="create_account" type="checkbox" />
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
		</div>
	</div>
</body>
</html>