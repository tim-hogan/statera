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

if (!isset($_GET['v'])) {
	error_log("ERROR: {$selff} [" . __LINE__ . "] Call to ViewAttach without parameter");
	header("Location: SecurityError.php");
	exit();
}

$s = Secure::sec_decryptParamPart($_GET['v'], base64_encode($session->session_key));
if (!$s || strlen($s) == 0)
{
	error_log("ERROR: {$selff} [" . __LINE__ . "] Unable to decode key");
	header("Location: SecurityError.php");
	exit();
}

parse_str($s, $a);

$items = array();
$journal_id = $a["i"];
$o_journal = $DB->o_getJournal($journal_id);
if ($o_journal->journal_attachment_group)
{
	$ag = $DB->o_getAttachmentGroup($o_journal->journal_attachment_group);
	$items = $DB->o_everyAttachmentForGroup($o_journal->journal_attachment_group);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>EXPENSE ATTACHMENTS</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
        #main {margin: 20px;}
        #list {padding: 20px;border: solid 1px #888;border-radius: 8px;}
		#heading1 h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
	</style>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
            <div id="heading1">
                <h1>ATATCHEMNTS TO EXPENSE</h1>
            </div>
			<div id="list">
				<table>
                    <?php
					foreach($items as $a)
					{
						echo "<tr><td><a href='attachments/{$a->attachment_filename->raw()}'>{$a->attachment_original_name->toHTML()}</a></td></tr>";
					}
                    ?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>