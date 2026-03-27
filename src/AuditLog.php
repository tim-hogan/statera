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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>AUDIT</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#genheading {margin-left: 20px;}
		#genheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#list {margin: 20px;}
		th,td {padding-right: 16px;}
	</style>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<div id="genheading">
				<h1>AUDIT LOG</h1>
			</div>
			<div id="list">
				<table>
					<tr>
						<th>TIMSTAMP</th><th>TYPE</th><th>DESCRIPTION</th><th>USER</th>
					</tr>
					<?php
				$r = $DB->allAudits(100);
				while ($o_audit = $r->fetch_object("audit"))
				{
					$strT = classTimeHelpers::timeFormat24Hr($o_audit->audit_timestamp, "Pacific/Auckland");
					echo "<tr><td>{$strT}</td><td>{$o_audit->audit_type->toHTML()}</td><td>{$o_audit->audit_description->toHTML()}</td><td>{$o_audit->user_firstname->toHTML()} {$o_audit->user_lastname->toHTML()}</td></tr>";
				}
					?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>