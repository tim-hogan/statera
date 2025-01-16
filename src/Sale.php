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

$inputParams = null;
if (isset($_GET["v"]))
{
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
	var_error_log($_POST,"_POST");

	if (!$session->checkCSRF())
	{
		$DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
		header("Location: SecurityError.php");
		exit();
	}

	$accountid = 0;
	$date = null;

	if ( count($_POST["desc"]) == count($_POST["desc"]) &&
		 count($_POST["desc"]) == count($_POST["qty"]) &&
		 count($_POST["desc"]) == count($_POST["unit"]) &&
		 count($_POST["desc"]) == count($_POST["prodid"]) &&
		 count($_POST["desc"]) == count($_POST["total"]))
	{
		$date = null;
		if (isset($_POST["date"]))
			$date = $_POST["date"];
		if (isset($_POST["account"]))
		{
			$accountid = intval($_POST["account"]);
			$account = $DB->getAccount($accountid);
		}

		error_log("Mode = {$_POST["mode"]}");

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
			$line["total"] = $_POST["total"] [$idx];

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
						for ($idx = 0; $idx < count($_POST["desc"]);$idx++)
						{
							//Create an invoice lines
							$productid = intval($_POST["prodid"] [$idx]);
							$linedesc = $_POST["desc"] [$idx];
							$qty = floatval($_POST["qty"] [$idx]);
							$unitdesc = $_POST["unitdesc"] [$idx];
							$unit = floatval($_POST["unit"] [$idx]);
							$total = round($qty * $unit,2);

							$line = $DB->createInvoiceLine($invoice->idinvoice,$productid,$linedesc,$qty,$unitdesc,$unit,$total);
							if ($line)
							{
								$undo->add(new UndoAction("delete","invoice_line","idinvoice_line",$line->idinvoice_line) );
								$net += $total;
							}
							else
								$DB->TransactionError();
						}

						//Create cash sale hjournal entries
						if ($account)
						{
							if ($account->account_sale_tax_class)
							{
								$tax_class_account = $DB->getTaxClass($account->account_sale_tax_class);
								$tax = $DB->getTaxRateForClassAndDate($tax_class_account->idtaxclass,new DateTime());
								$taxrate = 0.0;
								if ($tax)
									$taxrate = $tax->taxrate_rate;
								$ledgerAmount = LedgerAmount::createFromNet($net,$taxrate);
							}
							else
								$ledgerAmount = LedgerAmount::createFromNet($net,0.0);
						}
						else
							$ledgerAmount = LedgerAmount::createFromNet($net,$salestaxrate);
						$xtn = $DB->saleCash($date,$description,$accountid,$invoice->idinvoice,$ledgerAmount,0,0,false);
						$undo->add(new UndoAction("delete","journal","journal_xtn",$xtn) );


						$undolist->push($undo);
						$DB->updateUndoList($user->iduser,$undolist->toJSON());

					}
					else
						$DB->TransactionError();

					if ( $DB->EndTransaction() )
					{
						$v = "i={$invoice->idinvoice}";
						$s = Secure::sec_encryptParam($v,base64_encode($session->session_key));
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
						for ($idx = 0; $idx < count($_POST["desc"]);$idx++)
						{
							//Create an invoice lines
							$productid = intval($_POST["prodid"] [$idx]);
							$linedesc = $_POST["desc"] [$idx];
							$qty = floatval($_POST["qty"] [$idx]);
							$unitdesc = $_POST["unitdesc"] [$idx];
							$unit = floatval($_POST["unit"] [$idx]);
							$total = round($qty * $unit,2);

							$line = $DB->createInvoiceLine($invoice->idinvoice,$productid,$linedesc,$qty,$unitdesc,$unit,$total);
							if ($line)
							{
								$undo->add(new UndoAction("delete","invoice_line","idinvoice_line",$line->idinvoice_line) );
								$net += $total;
							}
							else
								$DB->TransactionError();
						}

						//Create account sale journal entries
						if ($account->account_sale_tax_class)
						{
							$tax_class_account = $DB->getTaxClass($account->account_sale_tax_class);
							$tax = $DB->getTaxRateForClassAndDate($tax_class_account->idtaxclass,new DateTime());
							$taxrate = 0.0;
							if ($tax)
								$taxrate = $tax->taxrate_rate;
							$ledgerAmount = LedgerAmount::createFromNet($net,$taxrate);
						}
						else
							$ledgerAmount = LedgerAmount::createFromNet($net,0.0);

						$xtn = $DB->saleAccount($date,$description,$accountid,$invoice->idinvoice,$ledgerAmount,0,0,false);
						$undo->add(new UndoAction("delete","journal","journal_xtn",$xtn) );


						$undolist->push($undo);
						$DB->updateUndoList($user->iduser,$undolist->toJSON());
					}
					else
						$DB->TransactionError();

					if ( $DB->EndTransaction() )
					{
						$v = "i={$invoice->idinvoice}";
						$s = Secure::sec_encryptParam($v,base64_encode($session->session_key));
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
			constructor(seq,desc,qty,unitdesc,unit,total,prodid) {
				this.seq = seq;
				this.desc = desc;
				this.qty = qty;
				this.unitdesc = unitdesc;
				this.unit = unit;
				this.total = total;
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
					this.createInput("total[]",l.total,form);
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
			st.ge("quantity").value = "1";
			st.ge("unitcost").value = "";
			st.ge("totalcost").value = "";
		}

		function displayLines() {
			let sum = 0;
			let tbl = st.ge("saleslist");
			st.removeAllChildren(tbl);
			for (let l of items.lines) {
				st.trow(tbl, l.desc, l.qty, "$" + l.unit, "$" + l.total,"<button onclick='deleteLine("+ l.seq +")'>DEL</button>");
				sum += parseFloat(l.total);
			}
			let tax = 0;
			let tot = sum;
			if (g_useSalesTax.length > 0) {
				tax = sum * g_salesTaxRate;
				tot = sum + tax;
			}
			st.trow(tbl, "", "", "", "")
			if (g_useSalesTax.length > 0) {
				st.trow(tbl, "", "", g_useSalesTax, "$" + tax.toFixed(2))
				st.trow(tbl, "", "", "", "")
			}
			st.trow(tbl,"","","TOTAL","$" + tot.toFixed(2))
		}

		function addLine() {
			calcLine();
			let m = items.maxseq() + 1;
			let product_id = 0;
			let p = st.ge("product");
			if (st.def(p._productid)) {
				product_id = p._productid;
			}

			let unitcost = parseFloat(st.ge("unitcost").value).toFixed(2);

			if (isNaN(unitcost)) {
				unitcost = "";
			}

			let l = new Line(m,
				st.ge("product").value,
				parseFloat(st.ge("quantity").value),
				st.ge("unitdesc").value,
				unitcost,
				parseFloat(st.ge("totalcost").value).toFixed(2),
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
					let l = new Line(m, fl.desc, fl.qty, fl.unitdesc, fl.unit, fl.total, fl.prodid);
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
								<input class="r" id="quantity" name="quantity" size="2" value="1" onchange="calcLine()" />
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
