<?php session_start(); ?>
<?php
//devt.Version = 1.0
header('Content-Type: application/json');

require './includes/classSecure.php';
require_once "./includes/classRolling.php";
require_once "./includes/classTextMsg.php";
require './includes/classstateraDB.php';
require dirname(__FILE__) . "/includes/classInputParam.php";

$DB = new stateraDB($devt_environment->getDatabaseParameters());

//Diagnostic
function var_error_log( $object=null , $text='')
{
	ob_start();                    // start buffer capture
	var_dump( $object );           // dump the values
	$contents = ob_get_contents(); // put the buffer into a variable
	ob_end_clean();                // end capture
	error_log( "{$text} {$contents}" );        // log contents of the result of var_dump( $object )
}


/*
Repsonse format
meta:
	status: true | false
	request: <request made>
	time:   <timestamp>
	errorcode:  errorcode if error
	errormsg:   error message if error
data:
	<response data>
*/



//Globals
$key = '';
$req = '';
$reqValue1 = '';
$reqValue2 = '';
$reqValue3 = '';

//Functions
function newMetaResponseHdr($status,$req,$errorcode = null,$errormsg = null)
{
	$dt = new DateTime('now');
	$meta = array();
	$meta['status'] = $status;
	$meta['req'] = $req;
	$meta['time'] = $dt->format('Y-m-d') . "T" . $dt->format('H:i:s') . "Z";
	$meta['errorcode'] = $errorcode;
	$meta['errormsg'] = $errormsg;
	return $meta;
}

function newErrorMetaHdr($req,$errorcode,$errormsg)
{
	return newMetaResponseHdr(false,$req,$errorcode,$errormsg);
}

function newOKMetaHdr($req)
{
	return newMetaResponseHdr(true,$req);
}

function returnError($req,$code,$desc)
{
   $rslt = array();
   $meta = newErrorMetaHdr($req,$code,$desc);
   $rslt['meta'] = $meta;
   $rslt['data'] = array();
   echo json_encode($rslt);
   exit();
}

function Sanatise($in,$trimit=true)
{
	$data = $in;
	if ($trimit)
		$data = trim($data);
	$data = stripslashes($data);
	$data = strip_tags(htmlspecialchars_decode($data));
	return $data;
}



/*
***********************************************************************
GET FUNCTIONS
***********************************************************************
*/
function getquote($req,$param1)
{
	global $DB;

	$quote = $DB->getQuoteById(intval($param1));
	if (!$quote)
		returnError($req, "2003", "Invalid quote id");

	//Now we need to sum the lines
	$lines = $DB->o_everyQuoteLine(intval($quote["idquote"]));
	$sum = 0.0;
	foreach($lines as $line) {
		$sum += $line->quote_line_cost;
	}


	//Now get sales tax information
	$o_SalesTax = $DB->getTaxRateForClassAndDate(1, (new DateTime($quote["quote_date"])));
	$salestaxinfo = ["name" => $o_SalesTax->taxclass_name->raw(), "rate" => $o_SalesTax->taxrate_rate] ;

	$ret = array();
	$ret['meta'] = newOKMetaHdr($req);
	$ret['data'] = ["quote" => $quote, "total_net" => $sum, "salestax" => $salestaxinfo];
	echo json_encode($ret);
	exit();

}

/*
***********************************************************************
PUT AND POST FUNCTIONS
***********************************************************************
*/
function postRequestQuote($req,$params)
{
	global $devt_environment;
	global $DB;

	$data = array();

	var_error_log($params, "postRequestQuote Params");
	if (! Rolling::checkRate($DB, "Quote") )
	{
		//Check the minimum of params
		if (isset($params["name"]) && isset($params["phone"]))
		{
			$name = Sanatise($params["name"]);
			$phone = Sanatise($params["phone"]);
			$addr1 = "";
			$addr2 = "";
			$addr3 = "";
			$addr4 = "";
			if (isset($params["addr1"]))
				$addr1 = Sanatise($params["addr1"]);
			if (isset($params["addr2"]))
				$addr2= Sanatise($params["addr2"]);
			if (isset($params["addr3"]))
				$addr3 = Sanatise($params["addr3"]);
			if (isset($params["addr4"]))
				$addr4 = Sanatise($params["addr4"]);

			$quote_req_id = $DB->createQuoteRequest($name, $phone, $addr1, $addr2, $addr3, $addr4);
			if ($quote_req_id !== false)
			{
				//Send text messages to all.
				$msg = "New Quote Request #{$quote_req_id} for {$name} on {$phone}";
				$textMsg = new devt\TextMsg\TextMessage();
				$allUsers = $DB->o_everyUserNotifyQuote();
				foreach ($allUsers as $u)
				{
					$to = $u->user_phone->raw();
					$textresult = $textMsg->send($to, $msg, "64", "TextStatus.php");
					if (isset($textresult["meta"]) && $textresult["meta"]["status"] == "OK") {
						$textid = $textresult["data"]["textid"];
						$DB->createAudit("text", "Text sent to {$to} textid {$textid}");
					}
				}
			}
		}
		else
			returnError($req, 1001, "Missing name and or phone");
	}
	else
		returnError($req, 1002, "Rolling - too many hits");

	$ret = array();
	$ret['meta'] = newOKMetaHdr($req);
	$ret['data'] = $data;
	echo json_encode($ret);
	exit();

}

function postAcceptQuote($req,$params)
{
	global $devt_environment;
	global $DB;

	$data = array();
	$key = null;
	$quoteid = 0;
	if (isset($params["key"]))
		$key = $params["key"];
	if (isset($params["key1"]))
		$key = $params["key1"];

	if ($key)
	{
		$s = Secure::sec_decryptParamPart($params["key"], base64_encode($devt_environment->getkey("QUOTE_KEY")));
		parse_str($s, $a);

		$quoteid = intval($a["q"]);
	}
	else
	{
		if (isset($params["key2"]))
			$key = $params["key2"];
		$inputParams = null;
		$inputParams = InputParam::load($key, $devt_environment->getkey("QUOTE_KEY"));
		$quoteid = intval($inputParams->q);
	}

	//Look up quote
	$o_quote = $DB->o_getQuoteById($quoteid);

	if (!$o_quote)
		returnError($req, "2001", "Invalid quote id");

	if ( ! $DB->markQuoteAccepted($quoteid) )
		returnError($req, "2002", "Failed to update quote");

	//Create auditd
	$DB->createAudit("quote","Quote nunber {$o_quote->quote_number} accepted via API");

	//Get net value
	$netvalue = $DB->netSumQuoteLines($quoteid);
	$netvalue = "$" . number_format($netvalue, 2);

	//Send text messages to all.
	$msg = "Quote {$o_quote->quote_number} has been accepted via WEB; net value {$netvalue}";
	$textMsg = new devt\TextMsg\TextMessage();
	$allUsers = $DB->o_everyUserNotifyQuote();
	foreach($allUsers as $u)
	{
		$to = $u->user_phone->raw();
		$textresult = $textMsg->send($to, $msg, "64", "TextStatus.php");
		if (isset($textresult["meta"]) && $textresult["meta"]["status"] == "OK")
		{
			$textid = $textresult["data"]["textid"];
			$DB->createAudit("text", "Text sent to {$to} textid {$textid}");
		}
	}

	$ret = array();
	$ret['meta'] = newOKMetaHdr($req);
	$ret['data'] = $data;
	echo json_encode($ret);
	exit();
}


//Start
var_error_log($devt_environment->getkey("DEV_MODE"),"DEV_MODE");


if (!isset($_GET['r']))
	returnError(null,1000,"Invalid parameter");

$r = $_GET['r'];
$tok = strtok($r,"/");
if (strlen($tok) == 16)
{
	$key = $tok;
	$req = strtok("/");
}
else
	$req = $tok;
$reqValue1 =strtok("/");
$reqValue2 =strtok("/");
$reqValue3 =strtok("/");

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
	$result = array();
	switch (strtolower($req))
	{
		case 'getquote':
			getQuote($req, $reqValue1);
			break;
		default:
			returnError($req,1000,"Invalid parameter");
			break;
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT' || $_SERVER['REQUEST_METHOD'] == 'POST') {

	$contents = file_get_contents('php://input');
	$params = array();
	$params = json_decode($contents,true);

	switch (strtolower($req))
	{
	case 'requestquote':
		postRequestQuote($req,$params);
		break;
	case 'acceptquote':
		postAcceptQuote($req,$params);
		break;
	default:
		returnError($req,1000,"Invalid parameter");
		break;
	}
}
?>