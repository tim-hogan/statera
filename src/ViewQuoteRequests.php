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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>VIEW QUOTE REQUEST</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#controls {margin-top: 20px; margin-bottom: 20px; text-align: center;}
		#list {padding: 20px;}
		h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		th, td {padding-right: 16px;}
	</style>
</head>
<body>
	<div id="controls">
		<button onclick="location.href = '/';">HOME</button>
	</div>
	<div id="list">
		<h1>QUOTE REQUESTS</h1>
		<table>
            <?php
				$quote_requests = $DB->LastQuoteRequests();
				if (count($quote_requests) > 0)
                {
					echo "<tr><th>ID</th><th>DATE</th><th>NAME</th><th>PHONE</th><th>ADDRESS</th></tr>";
					foreach($quote_requests as $qr)
					{
						echo "<tr>";
						$strDate = classTimeHelpers::timeFormatnthDate($qr->quote_request_date, "Pacific/Auckland");
						echo "<td>{$qr->idquote_request}</td>";
						echo "<td>{$strDate}</td>";
						echo "<td>{$qr->quote_request_name->toHTML()}</td>";
						echo "<td>{$qr->quote_request_phone->toHTML()}</td>";
						echo "<td>{$qr->quote_request_addreess1->toHTML()}</td>";
						echo "<td>{$qr->quote_request_addreess2->toHTML()}</td>";
						echo "<td>{$qr->quote_request_addreess3->toHTML()}</td>";
						echo "<td>{$qr->quote_request_addreess4->toHTML()}</td>";
						echo "</tr>";
					}
                }
				else
					echo "<p>NO REQUESTS</p>"
            ?>
		</table>
	</div>
</body>
</html>
