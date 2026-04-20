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
$idquote = 0;
$o_quote = null;

if (isset($_GET["v"]))
{
	$inputParams = InputParam::load($_GET["v"],$session->session_key);
	$idquote = $inputParams->i;
    $o_quote = $DB->o_getQuoteById($idquote);
    error_log("quoteid = {$idquote}");
}

if (!$o_quote)
{
    header("Location: SecurityError.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	var_error_log($_POST, "POST");
    
	if (isset($_POST["account"]) )
    {
        $nAccount = intval($_POST["account"]);
		if ($nAccount > 0)
        {
            $DB->AssignAccountToQuote($o_quote->idquote, $nAccount);
        }
    }

	header("Location: QuotesNoAccount.php");
    exit();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>ASSIGN CUSTOMER</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 20px;}
		#expheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#expheading h2 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#details {margin: 20px;padding: 20px;border: solid 1px #888;border-radius: 8px;}
		#details h2 {margin-top: 0;color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#details td {padding-right: 16px;}
		#form {margin: 20px;padding: 20px;border: solid 1px #888;border-radius: 8px;}
		.r {text-align: right;}
		.b {font-weight: bold;}
		.formfield {margin-bottom: 24px;}

	</style>
	<script>
		var selff = "<?php echo $selff;?>";
	</script>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<div id="expheading">
				<h1>ASSIGN</h1>
			</div>
		</div>
		<div id="details">
			<?php
			$strQuoteNum = sprintf("%05d", $o_quote->quote_number);
            $strDate = (new DateTime($o_quote->quote_date))->format("j/n/Y");
            $netTotal = $DB->netSumQuoteLines($o_quote->idquote);
            $v1 = LedgerAmount::format1($netTotal);

            echo "<h2>QUOTE {$strQuoteNum}</h2>";
            echo "<table>";
            echo "<tr><td>DATE</td><td>{$strDate}</td></tr>";
            echo "<tr><td>TO</td><td>{$o_quote->quote_contact_name->toHTML()}</td></tr>";
            echo "<tr><td>VALUE</td><td>{$v1}</td></tr>";
            echo "</table>";
            ?>
		</div>
		<div id="form">
				<form method="post" action="<?php echo "{$selff}?v={$_GET["v"]}";?>">
				<div class="formfield">
					<select name="account">
					<option value="0">[SELECT CUSTOMER ACCOUNT]</option>
					<?php
					$r = $DB->allAccounts("where account_deleted = 0", "order by account_name");
					while ($o_account = $r->fetch_object("account")) 
					{
						echo "<option value='{$o_account->idaccount}'>{$o_account->account_name->toHTML()}</option>";
					}
					?>
					</select>
				</div>
				<?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
				<button>ASSIGN</button>
			</form>
		</div>
	</div>
</body>
</html>
