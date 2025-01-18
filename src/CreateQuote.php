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

if (! $session->quote_num)
{
    $session->quote_num = $DB->getNextQuoteNumber();
}

$strQuote_num = sprintf("%05d", $quote_num);

$errmsg = "";

$formfields = ["date" => "","num" =>"","addr1"=>"", "addr2" => "", "addr3" => "", "addr4" => "","addr5"=>""];
$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone("Pacific/Auckland"));
$formfields["date"] = $dtNow->format("Y-m-d");
$formfields["num"] = $strQuote_num;


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $o_quote = $DB->o_getQuote($session->quote_num);
    if (!$o_quote)
    {
        $o_quote = $DB->createNewQuote($session->quote_num);
    }

    $formfields["date"] = FormList::getField("date");
    $formfields["num"] = FormList::getIntegerField("num");
    $formfields["name"] = FormList::getField("name");
    $formfields["addr1"] = FormList::getField("addr1");
    $formfields["addr1"] = FormList::getField("addr1");
    $formfields["addr2"] = FormList::getField("addr2");
    $formfields["addr3"] = FormList::getField("addr3");
    $formfields["addr4"] = FormList::getField("addr4");
    $formfields["addr5"] = FormList::getField("addr5");

}
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
        #form div.form_field {margin-bottom: 20px;}
        #printpage {margin: 20px; border: solid 1px #888;}
        #printarea table {border-collapse: collapse; width: 100%;}
		.r {text-align: right;}
        @media print {
			#printpage {width: 210cm; height: 297cm; margin: 0;}
			#printarea {margin-top: 2.5cm; margin-bottom: 2.5cm; margin-left: 2cm; margin-right: 2cm;}
		}
	</style>
    <script>
        function createTextInputField(parent,name,size,classs,defaultVal) {
			let input = st.cea("input", parent);
			st.sa(input,"name",name);
			st.sa(input, "size", size);
			if (st.def(classs) && classs.length > 0)
				st.sa(input, "class", classs);
			if (st.def(defaultVal))
				st.sa(input, "value", defaultVal);
		}

		function createTDTextInputField(parent, name, size,classs,defaultVal) {
			let td = st.cea("td", parent);
			createTextInputField(td, name,size,classs,defaultVal);
        }

        function addQuoteLine()
        {
        	let tbl = st.ge("tblquotelines");
			let tr = st.cea("tr", tbl);
			createTDTextInputField(tr, "item[]", "5");
			createTDTextInputField(tr, "description[]", "40");
			createTDTextInputField(tr, "cost[]", "5","r","$0.00");

        }

        function start() {
            addQuoteLine();
        }
    </script>
</head>
<body onload="start()">
    <div id="conatainer">
        <?php include ("./includes/heading.html");?>
        <?php include ("./includes/menu.html");?>
        <div id="main">
            <?php
			if (strlen($errmsg) > 0)
			{
				echo "<div id='msg'><p class='err'>{$errmsg}</p></div>";
			}
            ?>
            <div id="newloanheading">
                <h1>NEW LOAN DETAILS</h1>
            </div>
            <div id="form">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div class="form_field">
                        <label for="num">QUOTE NUMBER</label>
                        <input id="num" name="num" type="text" size="5" value="<?php echo $formfields["num"]; ?>" readonly />
                    </div>
                    <div class="form_field">
                        <label for="date">DATE</label>
                        <input id="date" name="date" type="date" value="<?php echo $formfields["date"]; ?>" />
                    </div>
                    <div class="form_field">
                        <label for="name">NAME</label>
                        <input id="name" name="name" type="text" size="30" value="<?php echo $formfields["name"]; ?>" />
                    </div>
                    <div class="form_field">
                        <label for="to1">TO</label>
                        <input id="to1" name="to1" type="text" size="30" value="<?php echo $formfields["to1"]; ?>"  />
                        <input id="to2" name="to2" type="text" size="30" value="<?php echo $formfields["to2"]; ?>" />
                        <input id="to3" name="to3" type="text" size="30" value="<?php echo $formfields["to3"]; ?>" />
                        <input id="to4" name="to4" type="text" size="30" value="<?php echo $formfields["to4"]; ?>" />
                        <input id="to5" name="to5" type="text" size="30" value="<?php echo $formfields["to5"]; ?>" />
                    </div>

                    <div class="form_field">
                        <h2>QUOTE LINES</h2>
                        <button type="button" onclick="addQuoteLine()" title="Add more lines">+</button>
                        <table id="tblquotelines">
                            <tr>
                                <th colspan="2" class="c">SHAREHOLDER NAME</th>
                            </tr>
                            <tr>
                                <th>ITEM</th><th>DESCRIPTION</th><th>COST</th><th>
                            </tr>
                        </table>
                    </div>


                    <button>CREATE QUOTE</button>
                    <input type="hidden" name="formtoken" value="<?php echo $session->csrf_key;?>" />
                </form>
            </div>
            <div id="printpage">
                <div id="printarea">
                    <table>
                        <tr><td><img src="images/Logo1-Transparent.png" /></td><td colspan="2" class="r">QUOTE</td></tr>
                        <tr><td></td><td>QUOTE #</td><td><?php echo $strQuote_num;?></td></tr>
                        <tr><td></td><td>DATE</td><td><?php echo $formfields["date"]; ?></td></tr>
                        <tr><td></td><td>VALID UNTIL</td><td><?php echo $formfields["date"]; ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>