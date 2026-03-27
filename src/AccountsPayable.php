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
$errmsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

	var_error_log($_POST, "POST");

	if (!$session->checkCSRF())
	{
		$DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input",$user->iduser);
		header("Location: SecurityError.php");
		exit();
	}

	if (isset($_POST["multi"]) && $_POST["multi"] == "1")
	{
		$num = count($_POST["mxtn"]);
		$folio = $DB->getLastFolio() + 1;
		for ($idx = 0; $idx < $num; $idx++)
		{
			$xtn = FormList::getIntegerIndexField("mxtn", $idx);
			$amount = FormList::getCurrencyIndexField("mamt",$idx);
			$date = FormList::getDateField("date");
			$description = FormList::getField("desc");
			$xtn = $DB->payAccountsPayableCombined($xtn, $amount, $date,$description,$folio);
		}
	}
	else
	{
		$amount = FormList::getCurrencyField("amt");
		$xtn = FormList::getIntegerField("xtn");
		$date = FormList::getDateField("date");

		if (strlen($date) != 10)
			$errmsg = "Date field error";

		if ($amount <= 0.0)
			$errmsg = "The amount entered must be greater than zero.";

		if ($xtn <= 0.0)
			$errmsg = "Internal error, cannot find accounts payable record";

		if (strlen($errmsg) == 0)
		{
			$undo = new Undo("Paid expense");
			$xtn = $DB->payAccountsPayable($xtn, $amount, $date);
			if ($xtn) {
				$DB->createAudit("transaction", "Accounts payable: Transaction # {$xtn}",$user->iduser);
				$undo->add(new UndoAction("delete","journal","journal_xtn",$xtn) );
				$undolist->push($undo);
				$DB->updateUndoList($user->iduser,$undolist->toJSON());
			}
			else
				$errmsg = "Database transaction failed - contact support";
		}
	}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>ACCOUNTS PAYABLE</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 20px;}
		#main h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#main h2 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		p.err {color: red;font-weight: bold;font-size: 14pt;}
		#table {padding: 20px;}
		#table table {border-collapse:collapse;}
		#table th, td {padding-right: 14px;}
		#table th {text-align: left;}
		#table td.tddesc {min-width: 200px;}
		#multipay {margin-top: 20px; padding: 20px;border: solid 1px #888; border-radius: 8px;}
		.r {text-align: right;}
		.l {text-align: left;}
	</style>
	<script src="/js/st.js"></script>
	<script>
		function pay(n) {
			clearError();
			let tr = n.parentElement.parentElement;
			let inputs = tr.getElementsByTagName("INPUT");
			let i = inputs[1];
			let date = inputs[2];
			if (date.value.length != 0) {

				let form = st.ce("FORM");
				document.body.appendChild(form);

				form.method = "POST";
				form.action = "<?php echo $selff;?>";
				st.ci("xtn", st.ga(i, "_xtn"), form);
				st.ci("amt", i.value, form);
				st.ci("date", date.value, form);
				st.ci("formtoken", "<?php echo $session->csrf_key;?>", form);
				form.submit();
			}
			else {
				let m2 = st.ge("msg2");
				let p2 = st.cea("P", m2);
				p2.className = "err";
				p2.innerHTML = "ERROR: You must enter a valid date";
			}
		}

		function clearError() {
			st.removeAllChildren(st.ge("msg2"));
		}

		function errorMsg(m) {
			clearError();
			let m2 = st.ge("msg2");
			let p2 = st.cea("P", m2);
			p2.className = "err";
			p2.innerHTML = m;
		}

		function payMultiple(n) {
			clearError();
			let table = st.ge("tbl_list");
			let desc = st.ge("multidesc").value;
			if (desc.length == 0) {
				errorMsg("THE MULTIPLE ACCOUNTS DECRIPTION FIELD CANNOT BE BLANK");
				return;
			}
			let date = st.ge("multipaiddate").value;
			if (date.length == 0) {
				errorMsg("ERROR: You must enter a valid date in the multiple accounts section");
				return;
			}

			//Now go and get all the check boxes
			let l = table.getElementsByTagName("INPUT");
			//Is type checkbox
			let cnt = 0;
			let xtn = [];
			let amt = [];
			for (let i of l) {
				if (i.type == "checkbox" && i.checked) {
					let tr = i.parentElement.parentElement;
					let inputs = tr.getElementsByTagName("INPUT");
					if (inputs[0].checked) {
						i = inputs[1];
						xtn[cnt] = st.ga(i, "_xtn");
						amt[cnt] = i.value;
						cnt++;
					}
				}
			}

			//Now create the form and send
			let form = st.ce("FORM");
			document.body.appendChild(form);

			form.method = "POST";
			form.action = "<?php echo $selff;?>";
			st.ci("multi", 1, form);
			for (let idx = 0; idx < cnt; idx++) {
				st.ci("mxtn[" +idx+ "]", xtn[idx], form);
				st.ci("mamt[" +idx + "]", amt[idx], form);
			}
			st.ci("date", date, form);
			st.ci("desc", desc, form);
			st.ci("formtoken", "<?php echo $session->csrf_key;?>", form);
			form.submit();

		}

		function itemcheck(n) {
			let sum = 0;
			let table = st.ge("tbl_list");
			let l = table.getElementsByTagName("INPUT");
			//Is type checkbox
			for (let i of l) {
				if (i.type == "checkbox" && i.checked) {
					let v = parseFloat(st.ga(i, "_value"));
					sum += v;
				}
			}
			st.ge("multiamount").value = st.format_currency(sum);
		}

	</script>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<div id="msg2"></div>
			<?php
			if (strlen($errmsg) > 0)
			{
				echo "<div id='msg'><p class='err'>{$errmsg}</p></div>";
			}
			?>
			<h1>ACCOUNTS PAYABLE</h1>
			<div id="table">
				<table id="tbl_list">
					<?php
					$accts = $DB->everyAccountsPayable();
					if (!$accts || count($accts) == 0)
					{
						echo "<tr><th>NO PENDING PAYABLE ACCOUNTS</th></tr>";
					}
					else
					{
						echo "<tr><th></th><th>DATE</th><th>DESCRIPTION</th><th>VENDOR</th><th class='r'>AMMOUNT OWING</th><th class='r'>AMMOUNT PAID</th><th>DATE PAID</th></tr>";
						foreach($accts as $act)
						{
							$strdt = (new DateTime($act["journal_date"]))->format('j M Y');
							$desc = htmlspecialchars($act["journal_description"]);
							$strAmount = LedgerAmount::format1(-$act["journal_gross"]);
							$_value = -($act["journal_gross"]);
							$vendname = htmlspecialchars($act['journal_vendor_name']);
							echo "<tr><td><input type='checkbox' name='checkers[]' onchange='itemcheck(this)' _value='{$_value}' /></td><td>{$strdt}</td><td class='tddesc'>{$desc}</td><td class='tddesc'>{$vendname}</td><td class='r'>{$strAmount}</td><td><input class='r' type='txt' _xtn='{$act['journal_xtn']}' name='pay[]' value='{$strAmount}' size='10' /></td><td><input type='date' name='paiddate' /></td><td><button onclick='pay(this)'>PAY</button></td></tr>";
						}
					}
					?>
				</table>
			</div>
			<div id="multipay">
				<h2>PAY MULTIPLE ACCOUNTS ON ONE BANK TRANSACTION</h2>
				<table>
					<tr><th class="l">* DESCRIPTION</th><th>AMOUNT</th><th>DATE</th></tr>
					<tr><td><input id="multidesc" type="text" name="multidesc" placeholder="ENTER THE COMBINED DESCRIPTION" size="50" /></td><td><input id="multiamount" type="text" name="multiamount" size="10" class="r" readonly/></td><td><input id="multipaiddate" type='date' name='multipaiddate' /></td><td><button name="multipay" onclick="payMultiple(this)">PAY MULTIPLE</button></td></tr>
				</table>
			</div>
		</div>
	</div>
	<?php include ("./includes/footer.html");?>
</body>
</html>