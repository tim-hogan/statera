<?php
use function CommonMark\Render\HTML;
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
$formfields = array();
$strerr = "";
$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone($user->user_timezone->raw()));
$default_product_service = [
	["id" => 1,"type" => "Service", "description" => "Enter job description or job title (hourly rate)", "unit" => "hr" , "rate" => "$100.00"],
	["id" => 2,"type" => "Service","description" => "Enter job description or job title (hourly rate)", "unit" => "hr" , "rate" => "$120.00"],
	["id" => 3,"type" => "Service","description" => "Enter job description or job title (hourly rate)", "unit" => "hr" , "rate" => "$150.00"],
	["id" => 4,"type" => "Service","description" => "Enter job description or job title (daily rate)", "unit" => "day" , "rate" => "$800.00"],
	["id" => 5,"type" => "Service","description" => "Enter job description or job title (daily rate)", "unit" => "day" , "rate" => "$960.00"],
	["id" => 6,"type" => "Service","description" => "Enter job description or job title (daily rate)", "unit" => "day" , "rate" => "$1200.00"],
	["id" => 7,"type" => "Service","description" => "Travel", "unit" => "km" , "rate" => "$0.79"],
	["id" => 8,"type" => "Goods","description" => "Goods to be sold (ex tax)", "unit" => "" , "rate" => "$10.00"],
	["id" => 8,"type" => "Service","description" => "Spare", "unit" => "" , "rate" => ""],
	];

//Default charts
$default_charts = [
	["code" => 100,"type" => "cash","subtype" => "bank","typename" => "Cash","description" => "Current Account","taxclass" => null,"desc_dr" => "","desc_cr" => "","balance_sheet_desc" => "asset","balance_sheet_desc_sub" => "current_asset"],
	["code" => 200,"type" => "income","subtype" => "sale","typename" => "Income","description" => "Sales","taxclass" => null,"desc_dr" => "","desc_cr" => "","balance_sheet_desc" => "revenue","balance_sheet_desc_sub" => ""],
	["code" => 300,"type" => "current asset","subtype" => "accounts receivable","typename" => "Current Assets","description" => "Accounts Receivable","taxclass" => null,"desc_dr" => "","desc_cr" => "","balance_sheet_desc" => "asset","balance_sheet_desc_sub" => "current_asset"],
	["code" => 400,"type" => "current liability","subtype" => "accounts payable","typename" => "Current Liabilities","description" => "Accounts Payable","taxclass" => null,"desc_dr" => "","desc_cr" => "","balance_sheet_desc" => "liability","balance_sheet_desc_sub" => ""],
	["code" => 500,"type" => "equity","subtype" => "shares","typename" => "Equity","description" => "Shares paid up","taxclass" => null,"desc_dr" => "","desc_cr" => "","balance_sheet_desc" => "liability","balance_sheet_desc_sub" => ""],
	["code" => 600,"type" => "asset","subtype" => "fixed_asset","typename" => "Asset","description" => "Fixed assets","taxclass" => null,"desc_dr" => "","desc_cr" => "","balance_sheet_desc" => "asset","balance_sheet_desc_sub" => "fixed_asset"],
	["code" => 700,"type" => "liability","subtype" => "shareholders","typename" => "Liability","description" => "Shareholder current account","taxclass" => null,"desc_dr" => "","desc_cr" => "","balance_sheet_desc" => "liability","balance_sheet_desc_sub" => ""],
	["code" => 800,"type" => "tax","subtype" => "gst","typename" => "Tax","description" => "GST","taxclass" => null,"desc_dr" => "","desc_cr" => "","balance_sheet_desc" => "tax","balance_sheet_desc_sub" => ""],
	["code" => 801,"type" => "tax","subtype" => "company","typename" => "Tax","description" => "Company Tax","taxclass" => null,"desc_dr" => "","desc_cr" => "","balance_sheet_desc" => "tax","balance_sheet_desc_sub" => ""],
];

//Chart expeneses m = mandatory
$chart_expenses_operating = [
	["Desc" => "ACC Levies", "tax" => 1, "m" => 0],
	["Desc" => "Accomodation", "tax" => 1, "m" => 0],
	["Desc" => "Accountants", "tax" => 1, "m" => 0],
	["Desc" => "Adevertising", "tax" => 1, "m" => 0],
	["Desc" => "Audition makeup/hair", "tax" => 1, "m" => 0,],
	["Desc" => "Bank Fees", "tax" => null, "m" => 1],
	["Desc" => "Cleaning", "tax" => 1, "m" => 0],
	["Desc" => "Commision", "tax" => 1, "m" => 0],
	["Desc" => "Computer Expenses", "tax" => 1, "m" => 0],
	["Desc" => "Depreciation", "tax" => 1, "m" => 1],
	["Desc" => "Entertainment", "tax" => 1, "m" => 0],
	["Desc" => "Entertainment 50%", "tax" => 1, "m" => 0],
	["Desc" => "Fees", "tax" => 1, "m" => 0],
	["Desc" => "Freight and Couriers", "tax" => 1, "m" => 0],
	["Desc" => "FBT", "tax" => 0, "m" => 0],
	["Desc" => "Fuel (Non Vehilce)", "tax" => 1, "m" => 0],
	["Desc" => "General", "tax" => 1, "m" => 0],
	["Desc" => "Hire of equipment", "tax" => 1, "m" => 0],
	["Desc" => "Insurance", "tax" => 1, "m" => 0],
	["Desc" => "Laundry", "tax" => 1, "m" => 0],
	["Desc" => "Legal", "tax" => 1, "m" => 0],
	["Desc" => "Meetings", "tax" => 1, "m" => 0],
	["Desc" => "Membership Fees", "tax" => 1, "m" => 0],
	["Desc" => "Motor Vehicle", "tax" => 1, "m" => 0],
	["Desc" => "Music costs", "tax" => 1, "m" => 0],
	["Desc" => "Packaging", "tax" => 1, "m" => 0],
	["Desc" => "Parking", "tax" => 1, "m" => 0],
	["Desc" => "Postage", "tax" => 1, "m" => 0],
	["Desc" => "Power", "tax" => 1, "m" => 0],
	["Desc" => "Printing and Sationary", "tax" => 1, "m" => 0],
	["Desc" => "Protective Clothing", "tax" => 1, "m" => 0],
	["Desc" => "Rates", "tax" => 1, "m" => 0],
	["Desc" => "Relocations costs", "tax" => 1, "m" => 0],
	["Desc" => "Repairs and Maintenance", "tax" => 1, "m" => 0],
	["Desc" => "Rubish collection", "tax" => 1, "m" => 0],
	["Desc" => "Security", "tax" => 1, "m" => 0],
	["Desc" => "Staff Welfare", "tax" => 1, "m" => 0],
	["Desc" => "Subscriptions and courses", "tax" => 1, "m" => 0],
	["Desc" => "Telephone/Intertnet", "tax" => 1, "m" => 0,],
	["Desc" => "Technical & Reserach Material", "tax" => 1, "m" => 0,],
	["Desc" => "Tools Under($1000)", "tax" => 1, "m" => 0,],
	["Desc" => "Travel", "tax" => 1, "m" => 0,],
	["Desc" => "Uniforms", "tax" => 1, "m" => 0,],
	["Desc" => "Water rates", "tax" => 1, "m" => 0,]
	];

$chart_expenses_financial = [
	["Desc" => "Depreciation", "subsubtype" => "depreciation", "tax" => 0, "m" => 1],
	["Desc" => "Interest Paid", "subsubtype" => "interest","tax" => 0, "m" => 1]
];


function createDefaultValues()
{
	global $formfields;
	global $dtNow;

	$rslt = classTimeHelpers::ipDecode();

	if (isset($rslt["countryCode"] ) )
	{
		$formfields["company_country"] = $rslt["countryCode"];
		if ($formfields["company_country"] == "NZ")
			$formfields["company_sales_tax_name"] = "GST";
	}
	$formfields["company_start_date"] = $dtNow->format("Y-m-d");
}

function createChartEntry($code,$type,$subtype,$typename,$description,$taxclass,$desc_dr,$desc_cr,$balance_sheet_desc,$balance_sheet_desc_subtype)
{

	global $DB;

	$chart = array();
	$chart["chart_code"] = $code;
	$chart["chart_type"] = $type;
	$chart["chart_type_name"] = $typename;
	$chart["chart_subtype"] = $subtype;
	$chart["chart_description"] = $description;
	$chart["chart_taxclass"] = $taxclass;
	$chart["chart_description_dr"] = $desc_dr;
	$chart["chart_description_cr"] = $desc_cr;
	$chart["chart_balancesheet"] = $balance_sheet_desc;
	$chart["chart_balancesheet_subtype"] =$balance_sheet_desc_subtype;

	$DB->p_create_from_array("chart",$chart);
}

function buildExpenseLine($e,$idx)
{
	echo "<tr>";
	echo "<td><input type='checkbox' name='expense_checked[{$idx}]' checked /></td>";
	$name = htmlspecialchars($e["Desc"]);
	echo "<td>{$name}</td>";
	echo "<tr>";

}

function buildProductService($ps,$idx)
{
	echo "<tr>";
	echo "<td><input _id='{$ps["id"]}' type='checkbox' name='product_checked[{$idx}]' checked /></td>";
	$service_selected = "";
	$goods_selected = "";
	if ($ps["type"] == "Service")
		$service_selected = "selected";
	if ($ps["type"] == "Goods")
		$goods_selected = "selected";
	echo "<td><select name='product_type[{$idx}]'><option value='goods' {$goods_selected} >Goods</option><option value='service' {$service_selected} >Service</option></select></td>";
	echo "<td><input _id='{$ps["id"]}' type='text' name='product_description[{$idx}]' value='' placeholder='{$ps["description"]}' size='50'/></td>";
	echo "<td><input _id='{$ps["id"]}' type='text' name='product_unit[{$idx}]' value='{$ps["unit"]}' size='4'/></td>";
	echo "<td><input _id='{$ps["id"]}' type='text' name='product_rate[{$idx}]' value='{$ps["rate"]}' size='6'/></td>";
	echo "</tr>";
}

function createInput($fieldname,$type,$label,$size,$list=null)
{
	global $formfields;
	if (strlen($label) > 0)
		echo "<label for='{$fieldname}'>{$label}</label>";
	$v = (isset($formfields[$fieldname])) ? $formfields[$fieldname] : "";
	if ($type=="text")
		echo "<input id='{$fieldname}' name='{$fieldname}' type='{$type}' value='{$v}' size='{$size}' />";

	if ($type=="dropdown")
	{
		echo "<select id='{$fieldname}' name='{$fieldname}'>";
		foreach($list as $k => $v)
		{
			$selected = (isset($formfields["$fieldname"]) && $k == $formfields["$fieldname"]) ? "selected" : "";
			echo "<option value='{$k}' {$selected}>{$v}</option>";
		}
		echo "</select>";
	}

	if ($type=="date")
	{
		echo "<input id='{$fieldname}' name='{$fieldname}' type='{$type}' value='{$v}' />";
	}
}

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
	createDefaultValues();
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{


	var_error_log($_POST,"post");

	$gstclassid = null;


	$formfields["company_name"] = FormList::getField("company_name");
	$formfields["company_address1"] = FormList::getField("company_address1");
	$formfields["company_address2"] = FormList::getField("company_address2");
	$formfields["company_address3"] = FormList::getField("company_address3");
	$formfields["company_address4"] = FormList::getField("company_address4");
	$formfields["company_city"] = FormList::getField("company_city");
	$formfields["company_postcode"] = FormList::getField("company_postcode");
	$formfields["company_state"] = FormList::getField("company_state");
	$formfields["company_country_prefix"] = FormList::getField("company_country");
	$formfields["company_country"] = classTimeHelpers::$countries[$formfields["company_country_prefix"]];
	$formfields["company_email"] = FormList::getField("company_email");
	$formfields["company_phone"] = FormList::getField("company_phone");
	$formfields["company_start_date"] = FormList::getField("company_start_date");
	$formfields["company_tax_number"] = FormList::getField("company_tax_number");
	$formfields["company_bank_acct_name"] = FormList::getField("company_bank_acct_name");
	$formfields["company_bank_acct_number"] = FormList::getField("company_bank_acct_number");
	$formfields["company_sales_tax_name"] = FormList::getField("company_sales_tax_name");
	$formfields["company_sales_tax_cadence"] = FormList::getIntegerField("company_sales_tax_cadence");
	$formfields["company_sales_tax_first_month"] = FormList::getIntegerField("company_sales_tax_first_month");

	$DB->BeginTransaction();


	$company = [
		"company_name" => $formfields["company_name"],
		"company_address1" => $formfields["company_address1"],
		"company_address2" => $formfields["company_address2"],
		"company_address3" => $formfields["company_address3"],
		"company_address4" => $formfields["company_address4"],
		"company_city" => $formfields["company_city"],
		"company_postcode"=> $formfields["company_postcode"],
		"company_state"=> $formfields["company_state"],
		"company_country"=> $formfields["company_country"],
		"company_country_prefix"=> $formfields["company_country_prefix"],
		"company_email"=> $formfields["company_email"],
		"company_phone"=> $formfields["company_phone"],
		"company_start_date"=> $formfields["company_start_date"],
		"company_tax_number"=> $formfields["company_tax_number"],
		"company_bank_acct_name"=> $formfields["company_bank_acct_name"],
		"company_bank_acct_number"=> $formfields["company_bank_acct_number"],
		"company_sales_tax_name"=> $formfields["company_sales_tax_name"],
		"company_sales_tax_cadence"=> $formfields["company_sales_tax_cadence"],
		"company_sales_tax_first_month"=> $formfields["company_sales_tax_first_month"],
	];

	$DB->p_create_from_array("company",$company);

	//Create a tax classes
	switch ($formfields["company_country_prefix"])
	{
		case "NZ":
			//GST
			$taxclass= ["taxclass_name" => "GST",
						"taxclass_description" => "New Zealand GST",
						"taxclass_invoice_text" => "GST NUMBER"
			];
			$DB->p_create_from_array("taxclass",$taxclass);

			$gstclassid = $DB->insert_id;

			$taxrate= ["taxrate_taxclass" => $gstclassid,
					   "taxrate_from_date" => "2010-10-01",
					   "taxrate_rate" => 0.15,
					   "taxrate_comments" => "New Zealand GST From 1 Oct 2010"
			];
			$DB->p_create_from_array("taxrate",$taxrate);

			//Company tax
			$taxclass= ["taxclass_name" => "COMPANY",
						"taxclass_description" => "New Zealand Compnay Tax",
						"taxclass_invoice_text" => ""
			];
			$DB->p_create_from_array("taxclass",$taxclass);


			$taxrate= ["taxrate_taxclass" => $DB->insert_id,
					   "taxrate_from_date" => "2012-04-01",
					   "taxrate_rate" => 0.28,
					   "taxrate_comments" => "New Zealand Corporate Tax Rate 2012"
			];

			$DB->p_create_from_array("taxrate",$taxrate);
			break;
	}

	foreach($default_charts as $c)
	{

		$chart = array();
		$chart["chart_code"] = $c["code"];
		$chart["chart_type"] = $c["type"];
		$chart["chart_type_name"] = $c["typename"];
		$chart["chart_subtype"] = $c["subtype"];
		$chart["chart_description"] = $c["description"];
		$chart["chart_taxclass"] = $gstclassid;
		$chart["chart_description_dr"] = $c["desc_dr"];
		$chart["chart_description_cr"] = $c["desc_cr"];
		$chart["chart_balancesheet"] = $c["balance_sheet_desc"];
		$chart["chart_balancesheet_subtype"] = $c["balance_sheet_desc_sub"];

		$DB->p_create_from_array("chart",$chart);

	}

	//Excpenses operating
	$n = count($chart_expenses_operating);
	error_log("Count of expenses checked {$n}");

	$idcode = 900;
	for ($idx=0; $idx < $n;$idx++)
	{
		if ($chart_expenses_operating[$idx] ["m"]  || (isset($_POST["expense_checked"] [$idx]) && $_POST["expense_checked"] [$idx] == "on"))
		{
			$chart = array();
			$chart["chart_code"] = $idcode;
			$chart["chart_type"] = "expense";
			$chart["chart_description_dr"] = $chart_expenses_operating[$idx] ["Desc"];
			$chart["chart_type_name"] = "Expense";
			$chart["chart_subtype"] = "operating";
			$chart["chart_description"] = $chart_expenses_operating[$idx] ["Desc"];
			if ($chart_expenses_operating[$idx] ["tax"])
				$chart["chart_taxclass"] = $gstclassid;
			$chart["chart_description_cr"] = "";
			$chart["chart_balancesheet"] = "expense";
			$chart["chart_balancesheet_subtype"] = "";

			$DB->p_create_from_array("chart",$chart);

			$idcode += 2;
		}
	}

	//expenses financial
	$n = count($chart_expenses_financial);
	error_log("Count of expenses checked {$n}");

	for ($idx=0; $idx < $n;$idx++)
	{
		if ($chart_expenses_financial[$idx] ["m"] )
		{
			$chart = array();
			$chart["chart_code"] = $idcode;
			$chart["chart_type"] = "expense";
			$chart["chart_description_dr"] = $chart_expenses_financial[$idx] ["Desc"];
			$chart["chart_type_name"] = "Expense";
			$chart["chart_subtype"] = "financial";
			$chart["chart_subsubtype"] = $chart_expenses_financial[$idx] ["subsubtype"];
			$chart["chart_description"] = $chart_expenses_financial[$idx] ["Desc"];
			if ($chart_expenses_financial[$idx] ["tax"])
				$chart["chart_taxclass"] = $gstclassid;
			$chart["chart_description_cr"] = "";
			$chart["chart_balancesheet"] = "expense";
			$chart["chart_balancesheet_subtype"] = "";

			$DB->p_create_from_array("chart",$chart);

			$idcode += 2;
		}
	}

	//Products
	$n = count($default_product_service);
	error_log("Count of products checked {$n}");
	if (isset($_POST["product_checked"]))
	{
		for ($idx = 0; $idx < $n;$idx++)
		{
			if (isset($_POST["product_checked"] [$idx]) && $_POST["product_checked"] [$idx] == "on")
			{

				$product = array();
				$product["product_description"] = FormList::getIndexField("product_description",$idx);
				$product["product_unit_cost"] = FormList::getIndexedCurrencyField("product_rate", $idx);
				$product["product_unit_text"] = FormList::getIndexField("product_unit",$idx);
				$product["product_type"] = strtolower(FormList::getIndexField("product_type",$idx));

				var_error_log($product,"product");

				if (strlen($product["product_description"]) > 0)
				{
					$DB->p_create_from_array("product",$product);
				}
			}
		}
	}

	//Initial shareholding
	if (isset($_POST["shareholderlastname"]))
	{
		$cnt = count($_POST["shareholderlastname"]);
		for ($idx = 0; $idx < $cnt; $idx++)
		{
			$id = $DB->createShareHolder($_POST["shareholderlastname"][$idx], $_POST["shareholderfirstnames"][$idx]);
			if ($id !== false)
			{
				//Now we create the journal entries
				$n = intval($_POST["numShares"][$idx]);
				$p = Money::getInputAmount($_POST["shareprice"][$idx]);
				$c = Money::getInputAmount($_POST["cashpaid"][$idx]);
				$totShares = ($p * $n);
				$totCurrent = $c - ($p * $n);
				$rslt = $DB->issueShares($_POST["company_start_date"], $id, $n, $p, $totShares, $totCurrent);
				if (!$rslt[0] || !$rslt[1])
					$DB->TransactionError();
			}
		}
	}

	//$DB->TransactionError();

	if ( $DB->EndTransaction() )
	{
		header("Location: index.php");
		exit();
	}
	else
	{
		$strerr = "A database update error has occured - contact support";
	}

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>SETUP WIZARD</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#msg {margin-left: 10px; margin-right: 10px;}
		#main {margin-left: 10px; margin-right: 10px;}
		h1 {margin-top: 0;color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		h2 {margin-top: 0;color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		.section {margin: 10px; padding: 10px; border: solid 1px #888; border-radius: 8px;}
		label {display: block;margin-top: 18px;}
		input {display: block;margin-top: 3px;}
		input[type='submit'] {margin-bottom: 20px;}
		select {font-size: 12pt;}
		p.intro {font-family: Akshar;font-weight: 300;color: #666;font-size: 14pt;max-width: 800px;}
		p.err {font-size: 12pt; color: red;}
		#shares th {padding-right: 20px;}
		#shares th.c {text-align: center;}
		#shares input.r {text-align: right;}
	</style>
	<script src="/js/st.js"></script>
	<script>
		function createTextInputField(parent,name,size,classs,defaultVal) {
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

		function addProdServiceLine() {
			let tbl = st.ge("tblprodserv");

			let cb = st.cisz("checkbox", "product_checked[]");
			cb.checked = true;
			let d = st.cisz("text", "product_description[]", "",50);
			let u = st.cisz("text", "product_unit[]", "",4);
			let r = st.cisz("text", "product_rate[]", "",6);
			st.otrow(tbl,cb,d,u,r);
		}

		function addShareholderLine() {
			let tbl = st.ge("tblshareholders");
			let tr = st.cea("tr", tbl);
			createTDTextInputField(tr, "shareholderlastname[]", "20");
			createTDTextInputField(tr, "shareholderfirstnames[]", "30");
			createTDTextInputField(tr, "numShares[]", "5","r");
			createTDTextInputField(tr, "shareprice[]", "5","r","$1.00");
			createTDTextInputField(tr, "cashpaid[]", "5","r","$0.00");
		}

		function start() {
			addShareholderLine();
		}
	</script>
</head>
<body onload="start()">
	<div id="conatiner">

		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<?php
		if (strlen($strerr) > 0)
		{
			echo "<div id='msg'>";
			echo "<p class='err'>{$strerr}</p>";
			echo "</div>";
		}
		?>
		<div id="main">
			<h1>SETUP WIZARD</h1>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

				<div id="comapny" class="section">
					<h2>YOUR COMPANY</h2>
					<p class="intro">You need to setup specific information about your company here.  This accounting system needs to know things like you official name, your registered company address and tax numbers.</p>
					<?php
					createInput("company_name","text","YOUR COMPANY NAME",80);
					createInput("company_address1","text","YOUR REGISTERED COMPANY ADDRESS",40);
					createInput("company_address2","text","",40);
					createInput("company_address3","text","",40);
					createInput("company_address4","text","",40);
					createInput("company_city","text","CITY",30);
					createInput("company_postcode","text","POST CODE",10);
					createInput("company_state","text","STATE",30);
					createInput("company_country","dropdown","COUNTRY",30,classTimeHelpers::$countries);
					createInput("company_email","text","COMPANY EMAIL ADDRESS",50);
					createInput("company_phone","text","COMPANY PHONE NUMBER",12);
					createInput("company_start_date","date","COMPANY START DATE",0);
					createInput("company_tax_number","text","COMPANY TAX NUMBER",10);
					createInput("company_bank_acct_name","text","COMPANY BANK ACCOUNT NAME",20);
					createInput("company_bank_acct_number","text","COMPANY BANK ACCOUNT NUMBER",12);
					createInput("company_sales_tax_name","text","SALES TAX NAME",4);
					createInput("company_financialyear_start_month", "text", "WHAT IS THE START MONTH FOR NEW FINANCIAL YEAR (ENTER MONTH NUMBER)", 2);
					createInput("company_sales_tax_cadence","text","HOW OFTEN DO YOU NEED TO SUBMIT SALES TAX RETURNS (EVERY n MONTHS)",2);
					createInput("company_sales_tax_first_month","text","WHAT IS THE FIRST START MONTH FOR SALES TAX RETURN (ENTER MONTH NUMBER)", 2);
					?>
				</div>
				<div id="expenses" class="section">
					<h2>EXPENSE CATEGORIES</h2>
					<p class="intro">Select which expense categories you will be using</p>
					<table id="tblprodserv">
						<?php
						$idx = 0;
						foreach($chart_expenses_operating as $e)
						{
							if ($e["m"] != 1)
								buildExpenseLine($e,$idx);
							$idx++;
						}
						?>
					</table>
				</div>
				<div id="products" class="section">
					<h2>CHARGEABLE PRODUCTS AND SERVICES</h2>
					<p class="intro">Choose what products and services you want to offer. You can edit the descriptions, units and rates.</p>
					<table id="tblprodserv">
						<tr><td><button type="button" onclick="addProdServiceLine()" title="Add more lines">+</button></td></tr>
						<?php
						$pidx = 0;
						foreach($default_product_service as $p)
						{
							buildProductService($p,$pidx);
							$pidx++;
						}
						?>
					</table>
				</div>
				<div id="shares" class="section">
					<h2>CREATE INITIAL SHAREHOLDERS</h2>
					<button type="button" onclick="addShareholderLine()" title="Add more lines">+</button>
					<table id="tblshareholders">
					<tr><th colspan="2" class="c">SHAREHOLDER NAME</th></tr>
					<tr><th>LAST</th><th>FIRST</th><th>NUM OF SHARES</th><th>SHARE PRICE</th><th>CASH PAID</th></tr>
					</table>
				</div>


			<input type="submit" name="go" value="COMPLETE SETUP" />
			<?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
			</form>
		</div>        
	</div>
	<?php include ("./includes/footer.html");?>
</body>
</html>