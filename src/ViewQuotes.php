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

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	var_error_log($_POST, "_POST");
	if (isset($_POST["accept"]))
	{
		$quoteid = intval($_POST["accept"]);
		$DB->markQuoteAccepted($quoteid);
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
	</script>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<div id="expheading">
				<h1>LIST OF QUOTES</h1>
			</div>
			<div id="list">
				<table>
					<tr><th>QUOTE #</th><th>DATE</th><th>TO</th><th>STATUS</th><th class="r">NET AMMOUNT</th><th></th></tr>
                    <?php
					$r = $DB->allQuotesDesc();
					while ($o_quote = $r->fetch_object("quote"))
					{
						$netTotal = $DB->netSumQuoteLines($o_quote->idquote);
						$v1 = LedgerAmount::format1($netTotal);
						$strQuoteNum = sprintf("%05d",$o_quote->quote_number);
						$strDate = (new DateTime($o_quote->quote_date))->format("j/n/Y");
						$button = "";
						if ($o_quote->quote_status->raw() == "open")
							$button = "<button _qid='{$o_quote->idquote}' onclick='markAccepted(this)'>MARK AS ACCEPTED</button>";
                        $v = "i={$o_quote->idquote}";
                        $s = Secure::sec_encryptParam($v, base64_encode($session->session_key));
						echo "<tr><td><a href='ViewQuote.php?v={$s}'>{$strQuoteNum}</a></td><td>$strDate</td><td>{$o_quote->quote_contact_name->toHTML()}</td><td>{$o_quote->quote_status->toHTML()}</td><td class='r'>{$v1}</td><td>{$button}</td></tr>";
					}
                    ?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>
