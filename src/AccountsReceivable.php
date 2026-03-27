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

	if (!$session->checkCSRF())
	{
		$DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input",$user->iduser);
		header("Location: SecurityError.php");
		exit();
	}

	$amount = FormList::getCurrencyField("amt");
	$xtn = FormList::getIntegerField("xtn");
	$date = FormList::getDateField("date");

	if ($amount <= 0.0)
		$errmsg = "The amount entered must be greater than zero.";

	if ($xtn <= 0.0)
		$errmsg = "Internal error, cannot find accounts payable record";

	if (strlen($date) != 10)
		$errmsg = "Date field error";

	if (strlen($errmsg) == 0)
	{
		$undo = new Undo("Received account payment");
		$xtn = $DB->payAccountsReceivable($xtn, $amount, $date);
		if ($xtn) {
			$DB->createAudit("transaction", "Accounts receivable: Transaction # {$xtn}",$user->iduser);
			$undo->add(new UndoAction("delete","journal","journal_xtn",$xtn) );
			$undolist->push($undo);
			$DB->updateUndoList($user->iduser,$undolist->toJSON());
		}
		else
			$errmsg = "Database transaction failed - contact support";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>ACCOUNTS RECEIVEABLE</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 20px;}
		#main h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		p.err {color: red;font-weight: bold;font-size: 14pt;}
		#table {padding: 20px;}
		#table table {border-collapse:collapse;}
		#table th, td {padding-right: 14px;}
		#table th {text-align: left;}
		#table td.tddesc {min-width: 200px;}
		.r {text-align: right;}
	</style>
	<script src="/js/st.js"></script>
	<script>
		function recv(n) {
			let tr = n.parentElement.parentElement;
			let inputs = tr.getElementsByTagName("INPUT");
			let i = inputs[0];
			let date = inputs[1];
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
			<h1>ACCOUNTS RECEIVABLE</h1>
			<div id="table">
				<table>
					<?php
					$accts = $DB->everyAccountsReceivable();
					if (!$accts || count($accts) == 0)
					{
						echo "<tr><th>NO PENDING RECEIVABLES</th></tr>";
					}
					else
					{
						echo "<tr><th>DATE</th><th>DESCRIPTION</th><th>ACCOUNT</th><th>INVOICE</th><th class='r'>AMMOUNT PENDING</th><th>AMMOUNT RECEIVED</th><th>DATE RECEIVED</th></tr>";
						foreach($accts as $act)
						{
							$strdt = (new DateTime($act["journal_date"]))->format('j M Y');
							$desc = htmlspecialchars($act["journal_description"]);
							$strAmount = LedgerAmount::format1($act["journal_gross"]);
							$invoice = $DB->getInvoice($act['journal_invoice']);
							$account = $DB->getAccount($act['journal_account']);
							$strInvoiceNum = "";
							if ($invoice)
								$strInvoiceNum = sprintf("%06d",$invoice->invoice_number);
							$strAcctname = "";
							if ($account)
								$strAcctname = $account->account_name->toHTML();
							echo "<tr><td>{$strdt}</td><td class='tddesc'>{$desc}</td><td class='tddesc'>{$strAcctname}</td><td class='r'>{$strInvoiceNum}</td><td class='r'>{$strAmount}</td><td><input class='r' type='txt' _xtn='{$act['journal_xtn']}' name='pay[]' value='$strAmount' size='10' /></td><td><input type='date' name='recvdate' /></td><td><button onclick='recv(this)'>RECEIVED</button></td></tr>";
						}
					}
					?>
				</table>
			</div>
		</div>
	</div>
	<?php include ("./includes/footer.html");?>
</body>
</html>