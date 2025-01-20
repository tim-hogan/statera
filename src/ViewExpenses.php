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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>EXPENSES</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
        #main {margin: 20px;}
		#expheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#list {padding: 20px;border: solid 1px #888;border-radius: 8px;}
		#list td {padding-right: 12px;}
		.r {text-align: right;}

	</style>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
            <div id="expheading">
                <h1>LIST OF EXPENSES</h1>
            </div>
			<div id="list">
				<table>
					<tr><th>DATE</th><th>CHART</th><th>DESCRIPTION</th><th>ATTACHMENTS</th><th class="r">AMMOUNT</th><th></th></tr>
					<?php
					$a = $DB->o_everyJournalExpense();
					foreach($a as $j)
					{
						$strDate = (new DateTime($j->journal_date))->format("j/n/Y");
						$strchart = "{$j->chart_code} " . $j->chart_description_dr->toHTML();
						$desc = $j->journal_description->toHTML();
						echo "<tr><td>{$strDate}</td><td>{$strchart}</td><td>{$desc}</td>";
						echo "<td>";
						if ($j->journal_attachment_group)
						{
							echo "<a href=ViewAttach?g={}>VIEW</a>";
						}
						echo "</td>";
						echo "<td class='r'>";
						echo LedgerAmount::format1($j->journal_gross);
						echo "</td>";
						echo "</tr>";
					}
					?>
				</table>
			</list>
		</div>
	</div>
	<?php include ("./includes/footer.html");?>
</body>
</html>