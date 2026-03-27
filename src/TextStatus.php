<?php
require_once './includes/classEnvironment.php';
require_once "./includes/classstateraDB.php";

$DB = new stateraDB($devt_environment->getDatabaseParameters());
function var_error_log( $object=null )
{
	ob_start();                    // start buffer capture
	var_dump( $object );           // dump the values
	$contents = ob_get_contents(); // put the buffer into a variable
	ob_end_clean();                // end capture
	error_log( $contents );        // log contents of the result of var_dump( $object )
}


if (isset($_GET['type']))
{
	$type = $_GET['type'];
	$status = '';
	$textid = 0;
	switch (strtoupper($type))
	{
		case 'TEXTACK':
			if (isset($_GET['status'])) $status = $_GET['status'];
			if (isset($_GET['tid'])) $textid = intval($_GET['tid']);
			if ($textid > 0)
			{
				$DB->createAudit("Text", "Text status update [{$textid}] {$status}");
			}
			break;
		case 'TEXTRCV':
			if (isset($_GET['status']))
				$status = $_GET['status'];
			if (isset($_GET['tid']))
				$textid = intval($_GET['tid']);
			if ($textid > 0) {
				$DB->createAudit("Text", "Text received [{$textid}] {$status}");
			}
			break;

		default:
			break;
	}
}
?>