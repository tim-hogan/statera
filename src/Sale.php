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

$DB->updateProductOrder();

$vParam = "";
if (isset($_GET["v"]))
	$vParam = $_GET["v"];
$salestaxrate = 0.0;
$salestaxname = "";
$errmsg = "";
$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone($user->user_timezone->raw()));
$formfields = array();

$company = $DB->getCompany();
$account = null;
$xtn = 0;

if ($company)
{
	$taxclass = $DB->getTaxClassByName($company->company_sales_tax_name->raw());
	if ($taxclass)
	{
		$tax = $DB->getTaxRateForClassAndDate($taxclass->idtaxclass,new DateTime());
		if ($tax)
		{
			$salestaxrate = $tax->taxrate_rate;
			$salestaxname = $company->company_sales_tax_name->raw();
		}
	}
}

error_log("In sale [". __LINE__ ."]----- session_key = " . base64_encode($session->session_key) );


$inputParams = null;
if (isset($_GET["v"]))
{
    error_log("v = {$_GET["v"]}");
	$inputParams = InputParam::load($_GET["v"],$session->session_key);
}

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
	if (!$inputParams)
	{
		error_log("{$selff} [".__LINE__."] Invalid parameter inputs");
		header("Location: SecuirtyError.php");
		exit();
	}
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	var_error_log($_POST,"Sale.php _POST");

	if (!$session->checkCSRF())
	{
		$DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
		header("Location: SecurityError.php");
		exit();
	}

	if (isset($_POST["quote"]))
	{
		$idquote = intval($_POST["quote"]);
		if ($idquote)
		{
			$o_quote = $DB->o_getQuoteById($idquote);
			if ($o_quote->quote_customer_account)
			{
				$o_account = $DB->getAccount($o_quote->quote_customer_account);
				if ($o_account)
				{
					$formfields["account"] = $o_account->idaccount;
					$o_lines = $DB->o_everyQuoteLine($idquote);
					foreach($o_lines as $o_line)
					{
						$line = array();
						$line["desc"] = $o_line->quote_line_descripton->toHTML();
						$line["qty"] = $o_line->quote_line_qty;
                        $line["net"] = intval($o_line->quote_line_cost * 100);
                        $line["tax"] = intval($o_line->quote_line_cost * $salestaxrate * 100);
                        $line["gross"] = $line["net"] + $line["tax"];
                        $line["unitdesc"] = "";
						$line["unit"] = "";
						$line["prodid"] = 0;

                        var_error_log($line, "line");
						$formfields["lines"][] = $line;
					}
				}
			}
		}
	}

	else
	{

		$accountid = 0;
		$date = null;

		if ( count($_POST["desc"]) == count($_POST["desc"]) &&
			 count($_POST["desc"]) == count($_POST["qty"]) &&
			 count($_POST["desc"]) == count($_POST["unit"]) &&
			 count($_POST["desc"]) == count($_POST["prodid"]) &&
			 count($_POST["desc"]) == count($_POST["gross"]))
		{
			$date = null;
			if (isset($_POST["date"]))
				$date = $_POST["date"];
			if (isset($_POST["account"]))
			{
				$accountid = intval($_POST["account"]);
				$account = $DB->getAccount($accountid);
			}

			if (!isset($_POST["mode"]))
			{
				error_log("No mode set ");
				$errmsg = "ERROR: Internal error - no mode set";
			}

			if ($_POST["mode"] == "account" && $accountid == 0)
				$errmsg = "ERROR: You must specifiy an account";

			$description = FormList::getField($_POST["description"]);

			//Save off all the form fields
			$formfields["date"] = $date;
			$formfields["description"] = htmlspecialchars($description);
			$formfields["account"] = $accountid;
			$formfields["mode"] = $_POST["mode"];
			$formfields["lines"] = array();

			for($idx = 0; $idx < count($_POST["desc"]);$idx++)
			{
				$line = array();
				$line["desc"] = $_POST["desc"] [$idx];
				$line["qty"] = $_POST["qty"] [$idx];
				$line["unitdesc"] = $_POST["unitdesc"] [$idx];
				$line["unit"] = $_POST["unit"] [$idx];
				$line["prodid"] = $_POST["prodid"] [$idx];
				$line["net"] = $_POST["net"][$idx];
				$line["tax"] = $_POST["tax"][$idx];
				$line["gross"] = $_POST["gross"] [$idx];

				$formfields["lines"] [] = $line;
			}

			if (strlen($errmsg) == 0)
			{


				//Switch based on mode
				switch ($_POST["mode"])
				{
					case "cash":
						$undo = new Undo("Cash sale");

						$DB->BeginTransaction();

						$invoice = $DB->createCashSaleInvoice($accountid,$date);
						if ($invoice)
						{
							$undo->add(new UndoAction("delete","invoice","idinvoice",$invoice->idinvoice) );

							//Create lines
							$net = 0.0;
							$sum_net = 0.0;
							$sum_tax = 0.0;
							$sum_gross = 0.0;

							for ($idx = 0; $idx < count($_POST["desc"]);$idx++)
							{
								//Create an invoice lines
								$productid = intval($_POST["prodid"] [$idx]);
								$linedesc = $_POST["desc"] [$idx];
								$qty = floatval($_POST["qty"] [$idx]);
								$unitdesc = $_POST["unitdesc"] [$idx];
								$unit = floatval($_POST["unit"] [$idx]);

								$net = 0.0;
								$tax = 0.0;
								$gross = 0.0;
								
								if ($unit == 0 || $qty = 0)
								{
									$net = floatval($_POST["net"][$idx]) / 100.0;
									$tax = floatval($_POST["tax"][$idx]) / 100.0;
									$gross = floatval($_POST["gross"][$idx]) / 100.0;
								}
								else
								{
									$net = round($qty * $unit, 2);
								}

								$line = $DB->createInvoiceLine($invoice->idinvoice,$productid,$linedesc,$qty,$unitdesc,$unit,$net,$tax,$gross);

								$sum_net += $net;
								$sum_tax += $tax;
								$sum_gross += $gross;

								if ($line)
								{
									$undo->add(new UndoAction("delete","invoice_line","idinvoice_line",$line->idinvoice_line) );
									//$net += $total;
								}
								else
									$DB->TransactionError();
							}

							//Create cash sale hjournal entries
							$ledgerAmount = new LedgerAmount($sum_net, $sum_tax, $sum_gross);

							//if ($account)
							//{
							//    if ($account->account_sale_tax_class)
							//    {
							//        $tax_class_account = $DB->getTaxClass($account->account_sale_tax_class);
							//        $tax = $DB->getTaxRateForClassAndDate($tax_class_account->idtaxclass,new DateTime());
							//        $taxrate = 0.0;
							//        if ($tax)
							//            $taxrate = $tax->taxrate_rate;
							//        $ledgerAmount = LedgerAmount::createFromNet($net,$taxrate);
							//    }
							//    else
							//        $ledgerAmount = LedgerAmount::createFromNet($net,0.0);
							//}
							//else
							//    $ledgerAmount = LedgerAmount::createFromNet($net,$salestaxrate);
							$xtn = $DB->saleCash($date,$description,$accountid,$invoice->idinvoice,$ledgerAmount,0,0,false);
							$undo->add(new UndoAction("delete","journal","journal_xtn",$xtn) );


							$undolist->push($undo);

						}
						else
							$DB->TransactionError();

						if ( $DB->EndTransaction() )
						{
							$DB->createAudit("transaction", "Cash sale: Transaction # {$xtn}", $user->iduser);

                            $v = "i={$invoice->idinvoice}";
                            $s = InputParam::encryptFromString($v, $session->session_key);
							$DB->updateUndoList($user->iduser, $undolist->toJSON());
							header("Location: Invoice.php?v={$s}");
							exit();
						}
						else
						{
							error_log("Database transaction failed");
							$errmsg = "ERROR: Database transaction failed - call support";
						}
						break;

					case "account":
						$undo = new Undo("Account sale");

						$DB->BeginTransaction();

						$invoice = $DB->createAccountSaleInvoice($accountid,$date);
						if ($invoice)
						{
							$undo->add(new UndoAction("delete","invoice","idinvoice",$invoice->idinvoice) );

							//Create lines
							$net = 0.0;
							$sum_net = 0.0;
							$sum_tax = 0.0;
							$sum_gross = 0.0;
							
							for ($idx = 0; $idx < count($_POST["desc"]);$idx++)
							{
								//Create an invoice lines
								$productid = intval($_POST["prodid"] [$idx]);
								$linedesc = $_POST["desc"] [$idx];
								$qty = floatval($_POST["qty"] [$idx]);
								$unitdesc = $_POST["unitdesc"] [$idx];
								$unit = floatval($_POST["unit"] [$idx]);
								$net = 0.0;
								$tax = 0.0;
								$gross = 0.0;

								if ($unit == 0 || $qty = 0) {
									$net = floatval($_POST["net"][$idx]) / 100.0;
									$tax = floatval($_POST["tax"][$idx]) / 100.0;
									$gross = floatval($_POST["gross"][$idx]) / 100.0;
								} else {
									$net = round($qty * $unit, 2);
								}

								$line = $DB->createInvoiceLine($invoice->idinvoice, $productid, $linedesc, $qty, $unitdesc, $unit, $net, $tax, $gross);
								$sum_net += $net;
								$sum_tax += $tax;
								$sum_gross += $gross;

								if ($line)
								{
									$undo->add(new UndoAction("delete","invoice_line","idinvoice_line",$line->idinvoice_line) );
									//$net += $total;
								}
								else
									$DB->TransactionError();
							}

							$ledgerAmount = new LedgerAmount($sum_net,$sum_tax,$sum_gross);
							//Create account sale journal entries
							//if ($account->account_sale_tax_class)
							//{
							//    $tax_class_account = $DB->getTaxClass($account->account_sale_tax_class);
							//    $tax = $DB->getTaxRateForClassAndDate($tax_class_account->idtaxclass,new DateTime());
							//    $taxrate = 0.0;
							//    if ($tax)
							//        $taxrate = $tax->taxrate_rate;
							//    $ledgerAmount = LedgerAmount::createFromNet($net,$taxrate);
							//}
							//else
							//    $ledgerAmount = LedgerAmount::createFromNet($net,0.0);

							$xtn = $DB->saleAccount($date,$description,$accountid,$invoice->idinvoice,$ledgerAmount,0,0,false);
							$undo->add(new UndoAction("delete","journal","journal_xtn",$xtn) );


							$undolist->push($undo);
						}
						else
							$DB->TransactionError();

						if ( $DB->EndTransaction() )
						{
							$DB->createAudit("transaction", "Account sale: Transaction # {$xtn}", $user->iduser);
							$v = "i={$invoice->idinvoice}";
                            $s = InputParam::encryptFromString($v, $session->session_key);
                            $DB->updateUndoList($user->iduser, $undolist->toJSON());
							header("Location: Invoice.php?v={$s}");
							exit();
						}
						else
						{
							error_log("Database transaction failed");
							$errmsg = "ERROR: Database transaction failed - call support";
						}
						break;
				}
			}
		}
		else
		{
			error_log("Invalid form data input");
			$errmsg = "Invalid input";
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
		#saleheading {margin-left: 20px;}
		#saleheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#tools {margin: 20px;padding: 20px;border: solid 1px #888;border-radius: 8px;}
		#tools h2 {color: #6b6ba7;font-family: Akshar;font-weight: 300;font-size: 1.5em;}
		#form {margin: 20px;padding: 20px;border: solid 1px #888;border-radius: 8px;}
		#form label {display: block;margin-top: 12px;}
		#form label.first {margin-top: 0;}
		#sale {margin-top: 30px;padding: 20px;border: solid 1px #888;}
		#sale h3 {margin-top:0; color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#sale td.td0 {height: 30px;min-width: 450px;}
		#sale td.td1 {min-width: 60px;text-align: right;}
		#sale td.td2 {min-width: 60px;text-align: right;}
		#sale td.td3 {min-width: 60px;text-align: right;}
		#msg {margin-left: 20px;}
		#msg p.err {color: red;font-weight: bold;font-size: 14pt;}
		.r {text-align: right;}
	</style>
	<script src="/js/st.js"></script>
	<script>
												<?PHP
		echo "var g_useSalesTax='{$salestaxname}';\n";
		echo "var g_salesTaxName='{$salestaxname}';\n";
		echo "var g_salesTaxRate={$salestaxrate};\n";
		$json_formfields = json_encode($formfields);
		echo "var g_formdata=JSON.parse('{$json_formfields}');";
										?>
		
		class Line {
			constructor(seq,desc,qty,unitdesc,unit,net,tax,gross,prodid) {
				this.seq = seq;
				this.desc = desc;
				this.qty = qty;
				this.unitdesc = unitdesc;
				this.unit = unit;
				this.net = net;
				this.tax = tax;
				this.gross = gross;
				this.prodid = prodid;
			}
		}

		class Lines {
			constructor() {
				this.lines = [];
				this.extraFields = [];
			}

			add(line) {
				this.lines.push(line);
			}

			addSubmitField(n, v) {
				this.extraFields.push({name: n,value: v});
			}

			delete(seq) {
				let i = 0;
				for (let l of this.lines) {
					if (l.seq == seq) {
						this.lines.splice(i, 1);
					}
					i++;
				}
			}

			maxseq() {
				let m = 0;
				for (let l of this.lines) {
					if (l.seq > m)
						m = l.seq;
				}
				return m;
			}

			createInput(name, value, form) {
				let i = st.cea("input", form);
				i.type = "text";
				i.name = name;
				i.value = value;
				return i;
			}

			submit() {
				let form = st.ce("FORM");
				document.body.appendChild(form);

				form.method = "POST";
				form.action = "<?php echo $selff . '?v=' . $vParam;?>";

				for (let f of this.extraFields) {
					this.createInput(f.name,f.value,form);
				}

				for (let l of this.lines) {
					this.createInput("desc[]",l.desc,form);
					this.createInput("qty[]",l.qty,form);
					this.createInput("unitdesc[]",l.unitdesc,form);
					this.createInput("unit[]",l.unit,form);
					this.createInput("prodid[]",l.prodid,form);
					this.createInput("net[]", l.net, form);
					this.createInput("tax[]", l.tax, form);
					this.createInput("gross[]", l.gross, form);
				}
					
				form.submit();
			}
		}

		var items = new Lines();

		function accountChange(n) {
			g_useSalesTax = g_salesTaxName;
			let l = n.getElementsByTagName("OPTION");
			for (let o of l) {
				if (o.value == n.value) {
					g_useSalesTax = st.ga(o, "_taxname");
				}
			}
			displayLines();
		}

		function clearEntryFields() {
			st.ge("product").value = "";
			st.ge("quantity").value = "";
			st.ge("unitcost").value = "";
			st.ge("totalcost").value = "";
		}

		function displayLines() {
			let sum_gross = 0;
			let sum_net = 0;
			let sum_tax = 0;
			let tbl = st.ge("saleslist");
			st.removeAllChildren(tbl);
			for (let l of items.lines) {
				let newqty = (l.qty == 0) ? "" : l.qty;
				let vlinenet = (l.net / 100).toFixed(2);
				st.trow(tbl, l.desc, newqty, "$" + l.unit, "$" + vlinenet,"<button onclick='deleteLine("+ l.seq +")'>DEL</button>");
				sum_gross += l.gross;
				sum_net += l.net;
				sum_tax += l.tax
			}
			st.trow(tbl, "", "", "", "")
			if (g_useSalesTax.length > 0) {
				let vtax = (sum_tax / 100).toFixed(2);
				st.trow(tbl, "", "", g_useSalesTax, "$" + vtax)
				st.trow(tbl, "", "", "", "")
			}
			let vgross = (sum_gross / 100).toFixed(2);
			st.trow(tbl, "", "", "TOTAL", "$" + vgross);
		}

		function addLine() {
			calcLine();
			let m = items.maxseq() + 1;
			let product_id = 0;
			let p = st.ge("product");
			if (st.def(p._productid)) {
				product_id = p._productid;
			}

			let strunitcost = String(st.ge("unitcost").value);
			let unitcost = st.parseCurrency(strunitcost);
			let qty = parseInt(st.ge("quantity").value);

			if (isNaN(unitcost)) {
				unitcost = "";
			}

			if (isNaN(qty)) {
				qty = 0;
			}

			let inctax = st.ge("incltax");

			//Remove $ signs and commas
			let strtot = String(st.ge("totalcost").value);
			let tot = st.parseCurrency(strtot);

			//Now we need to woprk back
			let gross = 0;
			let net = 0;
			let tax = 0;
			if (inctax) {
				gross = tot;
				net = parseFloat((gross / (1 + g_salesTaxRate)).toFixed(2));

				gross = parseInt(Math.round(gross * 100));
				net = parseInt(Math.round(net * 100));
				tax = gross - net;
			}
			else {
				gross = tot;
				net = gross;
				gross = parseInt(Math.round(gross * 100));
				net = parseInt(Math.round(net * 100));
			}


			let l = new Line(m,
				st.ge("product").value,
				qty,
				st.ge("unitdesc").value,
				unitcost,
				net,
				tax,
				gross,
				product_id
			);

			items.add(l);
			clearEntryFields();
			displayLines();
		}

		function deleteLine(seq) {
			items.delete(seq);
			displayLines();
		}

		function calcLine() {
			let uc = parseFloat(st.ge("unitcost").value);
			let qy = parseFloat(st.ge("quantity").value);
			if (!isNaN(uc) && !isNaN(qy)) {
				let t = uc * qy;
				st.ge("totalcost").value = t.toFixed(2);
			}
		}

		function prodchange(n) {
			let p = st.ge("products");
			let l = p.childNodes;
			let ud = st.ge("unitdesc");
			ud.innerHTML = "";
			for (let i = 0; i < l.length; i++) {
				if (l[i].nodeName == "OPTION") {
					if (n.value == l[i].value) {
						let unitcost = parseFloat(st.ga(l[i],"unit"));
						st.ge("unitcost").value = unitcost.toFixed(2);
						ud.value = st.ga(l[i],"unitdesc");
						n._productid = parseInt(st.ga(l[i],"productid"));
						calcLine();
					}
				}
			}
		}

		function finalise() {
			items.addSubmitField("date", st.ge("date").value);
			items.addSubmitField("account", st.ge("account").value);
			items.addSubmitField("mode", st.ge("mode").value);
			items.addSubmitField("description", st.ge("description").value);
			items.addSubmitField("formtoken", "<?php echo $session->csrf_key;?>");
			items.submit();
		}

		function recoverFields() {
			if (st.def(g_formdata.lines)) {
				for (fl of g_formdata.lines) {
					let m = items.maxseq() + 1;
					let l = new Line(m, fl.desc, fl.qty, fl.unitdesc, fl.unit, fl.net,fl.tax,fl.gross, fl.prodid);
					items.add(l);
				}
				clearEntryFields();
				displayLines();
			}
		}
	</script>

</head>
<body onload="recoverFields()">
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
			<div id="saleheading">
				<?php
				if ($inputParams->w == "cash")
					echo "<h1>CASH SALE</h1>";
				elseif ($inputParams->w == "account")
					echo "<h1>SALE ON ACCOUNT</h1>";
				?>
			</div>
			<div id="tools">
				<h2>GENERATE FROM QUOTE</h2>
				<form method="post" action="<?php echo "{$selff}?v={$_GET["v"]}";?>">
					<select name="quote">
						<option value="0">[SELECT QUOTE]</option>
						<?php
							$r = $DB->allAcceptedQuotesWithAccountDesc();
							while ($o_quote = $r->fetch_object("quote"))
							{
								$strNum = sprintf("%05d",$o_quote->quote_number);
								echo "<option value='{$o_quote->idquote}'>{$strNum} {$o_quote->quote_contact_name->toHTML()}</option>";    
							}
						?>
					</select>
					<?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
					<button>GENERATE</button>
				</form>
			</div>
			<div id="form">
				<div class="selectdiv">
					<table>

						<tr>
							<td>
								<label for="date" class="first">DATE *</label>
							</td>
						</tr>
						<?php
						if (isset($formfields["date"]))
							$strFormDate = $formfields["date"];
						else
							$strFormDate = $dtNow->format("Y-m-d");
						?>
						<tr>
							<td>
								<input id="date" type="date" value="<?PHP echo $strFormDate; ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="description">DESCRIPTION</label>
							</td>
						</tr>
						<tr>
							<td>
								<?php
								if ($inputParams->w == "cash")
									echo "<input id='description' type='text' size='50' value='CASH SALE' />";
								else
									echo "<input id='description' type='text' size='50' placeholder='ENTER DESCRIPTION' />";
								?>
							</td>
						</tr>
						<?php
						if ($inputParams->w == "account")
							echo "<tr><td><label for='account'>ACCOUNT *</label></td></tr>";
						else
							echo "<tr><td><label for='account'>ACCOUNT (Optional)</label></td></tr>";
						?>
						<tr>
							<td>
								<select id="account" name="account" onchange="accountChange(this)">
									<?php
									echo "<option value='0' _taxname='{$salestaxname}'></option>";
									 $r = $DB->allAccounts("where account_deleted = 0","order by account_name");
									 while ($account = $r->fetch_object("account"))
									 {
										 $select = "";
										 if (isset($formfields["account"]) && intval($formfields["account"]) == $account->idaccount)
											$select = "selected";
										 echo "<option value='{$account->idaccount}' _taxname='{$account->taxclass_name->raw()}' {$select} >{$account->account_name->toHTML()}</option>";
									 }
									?>
								</select>
							</td>
						</tr>

						<tr>
							<td>
								<label for="product">PRODUCT OR SERVICE</label>
							</td><td class="r">
								<label for="quantity">QTY</label>
							</td><td></td><td class="r">
								<label for="unitcost">UNIT COST</label>
							</td>
							<td class="r">
								<label for="incltax">GST INCLUDED</label>
							</td>
							<td class="r">
								<label for="totalcost">TOTAL COST</label>
							</td>
						</tr>
						<tr>
							<td>
								<input list="products" name="product" id="product" size="60" onchange="prodchange(this)" />
							</td><td>
								<input class="r" id="quantity" name="quantity" size="2" onchange="calcLine()" />
							</td><td>
								<input class="r" id="unitdesc" name="unitdesc" size="3" />
							</td><td>
								<input class=" r" id="unitcost" name="unitcost" size="4" onchange="calcLine()" />
							</td>
							<td class="r">
								<input type="checkbox" id="incltax" name="incltax" checked/>
							</td>
							<td>
								<input class="r" id="totalcost" name="totalcost" size="6" />
							</td><td>
								<button onclick="addLine()">ADD</button>
							</td>
						</tr>
					</table>
					<?php
					echo "<input type='hidden' name='mode' id='mode' value='{$inputParams->w}'/>";
					?>
					<datalist id="products">
						<?php
						$r = $DB->allProductsByOrder();
						while ($product = $r->fetch_object("product"))
						{
							echo "<option productid='{$product->idproduct}' unit='{$product->product_unit_cost}' unitdesc='{$product->product_unit_text->toHTML()}' value='{$product->product_description->toHTML()}'></option>";
						}
						?>
					</datalist>
				</div>
				<div id="sale">
					<h3>SALE DETAILS</h3>
					<table id="saleslist"></table>
					<button onclick="finalise()">FINALISE</button>
				</div>
			</div>
		</div>
	</div>
	<?php include ("./includes/footer.html");?>
</body>
</html>
