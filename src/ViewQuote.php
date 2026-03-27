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
$o_quote = null;
$quote_id = 0;
$inputParams = null;

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
	if (isset($_GET["v"]))
	{
		$inputParams = InputParam::load($_GET['v'], $session->session_key);
		if ($inputParams)
		{
			$quote_id = $inputParams->i;
			$o_quote = $DB->o_getQuoteById($quote_id);
		}
	}
}

$dtNow = new DateTime();
$o_taxrate = $DB->getTaxRateForClassAndDate(1, $dtNow);

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
		#controls {margin-top: 20px; margin-bottom: 20px; text-align: center;}
		#newquoteheading {margin-left: 20px;}
		#newquoteheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#printpage {margin: auto;max-width: 800px;}
		#printarea img {width: 375px;}
		#printarea table {border-collapse: collapse; width: 100%;}
		#printarea td.td1 {width: 70%;}
		#printarea td.td2 {width: 15%;}
		#printarea td.td3 {width: 15%;}
		#tabelline {width: 100%;}
		#gap {height: 20px;}
		#feet button {background-color: #06c11e; font-size: 14pt;color: white;}
		#feet a {text-decoration: none;color: white;}
		#feet a:link {color: white;}
		#feet a:visited {color: white;}
		#d_accept {text-align: center;}
		td.sz1 {font-size: 14pt; font-weight: bold;vertical-align: top;}
		.r {text-align: right;}
		.l {text-align: left;}
		.lcol1 {width: 10%;vertical-align:top;}
		.lcol2 {width: 60%;vertical-align:top;}
		.lcol3 {width: 15%;vertical-align:top;}
		.lcol4 {width: 15%;vertical-align:top;}
		.tdgap1 {height: 15px;}
		.tdgap2 {height: 45px;}
		@media print {
			#controls {display:none;}
			#printpage {width: 21cm; height: 29.7cm; margin: 0;border: none;}
			#printarea {margin-top: 2.5cm; margin-bottom: 2.5cm; margin-left: 2cm; margin-right: 2cm;}
			#printarea img {width: 10cm;}
			#gap {height: 1cm;}
			#feet p {margin-left: 1.5cm;margin-right: 1.5cm;}
			.tdgap1 {height: 0.5cm;}
			.tdgap2 {height: 1.5cm;}
		}
	</style>
</head>
<body>
	<div id="controls">
		<button onclick="window.print();">PRINT</button>
		<button onclick="location.href = '/';">HOME</button>
		<button onclick="location.href='Quote.php?v=<?php echo $_GET["v"];?>'">EDIT</button>
	</div>
	<div id="printpage">
		<div id="printarea">
			<?php
				if ($o_quote)
				{
					echo "<table>";
					echo "<tr><td class='td1'><img src='images/precisetrees/Logo1-Transparent.png' /></td><td colspan='2' class='sz1 r'>QUOTE</td></tr>";

					$o_lines = $DB->o_everyQuoteLine($o_quote->idquote);

					$strQuote_num = sprintf("%05d", $o_quote->quote_number);
					$dt = new DateTime($o_quote->quote_date);
					$strdate = $dt->format("j/n/Y");
					$dt->add(new DateInterval("P30D"));
					$strdateexpire = $dt->format("j/n/Y");

					echo "<tr>";
						echo "<td class='td1'></td>";
						echo "<td class='td2'>QUOTE #</td>";
						echo "<td class='td3 r'>{$strQuote_num}</td>";
					echo "</tr>";

					echo "<tr>";
						echo "<td class='td1'></td>";
						echo "<td class='td2'>DATE</td>";
						echo "<td class='td3 r'>{$strdate}</td>";
					echo "</tr>";

					echo "<tr>";
						echo "<td class='td1'>TO:</td>";
						echo "<td class='td2'>VALID UNTIL</td>";
						echo "<td class='td3 r'>{$strdateexpire}</td>";
					echo "</tr>";

					echo "<tr>";
						echo "<td class='td1'>{$o_quote->quote_contact_name->toHTML()}</td>";
						echo "<td class='td2'></td>";
						echo "<td class='td3'></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td class='td1'>{$o_quote->quote_address1->toHTML()}</td>";
						echo "<td class='td2'></td>";
						echo "<td class='td3'></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td class='td1'>{$o_quote->quote_address2->toHTML()}</td>";
						echo "<td class='td2'></td>";
						echo "<td class='td3'></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td class='td1'>{$o_quote->quote_address3->toHTML()}</td>";
						echo "<td class='td2'></td>";
						echo "<td class='td3'></td>";
					echo "</tr>";
					echo "<td class='td1'>{$o_quote->quote_address4->toHTML()}</td>";
						echo "<td class='td2'></td>";
						echo "<td class='td3'></td>";
					echo "</tr>";
					echo "<td class='td1'>{$o_quote->quote_city->toHTML()}</td>";
						echo "<td class='td2'></td>";
						echo "<td class='td3'></td>";
					echo "</tr>";

					echo "</table>";
					echo "<div id='gap'></div>";

					echo "<div id='lineitmes'>";
					echo "<table id='tabelline'>";
					$bHaveQty = false;
					foreach($o_lines as $l)
					{
						if ($l->quote_line_qty != 0)
							$bHaveQty = true;
					}
					if ($bHaveQty)
						echo "<tr><th class='lcol1 l'>ITEM</th><th class='lcol2'>DESCRIPTION</th><th class='lcol3 r'>QTY</th><th class='lcol4 r'>COST</th></tr>";
					else
						echo "<tr><th class='lcol1 l'>ITEM</th><th class='lcol2'>DESCRIPTION</th><th class='lcol3 r'></th><th class='lcol4 r'>COST</th></tr>";

					$sum = 0;
					foreach($o_lines as $l)
					{
						$v = "";
						$sum += $l->quote_line_cost;
						if (! $o_quote->quote_option_no_item_cost)
							$v = LedgerAmount::format1($l->quote_line_cost);
						if ($l->quote_line_cost == 0)
							$v = "";
						if ($l->quote_line_qty == 0)
							$strQTY = "";
						else
							$strQTY = $l->quote_line_qty;

						if (!$bHaveQty)
							$strQTY = "";

						echo "<tr><td class='lcol1'>{$l->quote_line_item}</td><td class='lcol2'>{$l->quote_line_descripton->toHTML()}</td><td class='lcol3 r'>{$strQTY}</td><td  class='lcol4 r'>{$v}</td></tr>";
						echo "<tr><td colspan='3' class='tdgap1'></td></tr>";
					}

					$gst = $sum * $o_taxrate->taxrate_rate;
					$total = $sum + $gst;
					$v1 = LedgerAmount::format1($sum);
					$v2 = LedgerAmount::format1($gst);
					$v3 = LedgerAmount::format1($total);

					echo "<tr><td colspan='3' class='tdgap2'></td></tr>";
					echo "<tr><td class='lcol1 r'></td><td class='lcol2'></td><td class='lcol3'>SUBTOTAL</td><td  class='lcol4 r'>{$v1}</td></tr>";
					echo "<tr><td colspan='3' class='tdgap1'></td></tr>";
					echo "<tr><td class='lcol1 r'></td><td class='lcol2'></td><td class='lcol3'>GST</td><td  class='lcol4 r'>{$v2}</td></tr>";
					echo "<tr><td colspan='3' class='tdgap1'></td></tr>";
					echo "<tr><td class='lcol1 r'></td><td class='lcol2'></td><td class='lcol3'>TOTAL</td><td  class='lcol4 r'>{$v3}</td></tr>";

					echo "</table>";
					echo "</div>";

				echo "<div id='gap'></div>";

				echo "<div id='feet'>";
				$v = "q={$o_quote->idquote}&n={$o_quote->quote_number}";
						$s = InputParam::encryptFromString($v, $devt_environment->getkey("QUOTE_KEY"));
						//$s = Secure::sec_encryptParam($v,base64_encode($devt_environment->getkey("QUOTE_KEY")));
						echo "<div id='d_accept'>";
							echo "<button><a href='https://precisetrees.nz/acceptquote?u={$s}'>ACCEPT QUOTE</a></button>";
						echo "</div >";
						echo "<p>or email your acceptance to admin@precisetrees.nz quoting the Quote number <strong>{$strQuote_num}<strong></p>";
					echo "</div>";
				}
				else
				{
					echo "Quote not available ID = {$quote_id}";
				}
			?>
		</div>
	</div>
</body>
</html>
