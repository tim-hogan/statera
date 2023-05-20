<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classInputParam.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
require dirname(__FILE__) . "/includes/classFormList2.php";

function var_error_log( $object=null , $text='')
{
    ob_start();
    var_dump( $object );
    $contents = ob_get_contents();
    ob_end_clean();
    error_log( "{$text} {$contents}" );
}

require dirname(__FILE__) . "/includes/commonSession.php";

$dtNow = new DateTime();
$dtNow->setTimezone(new DateTimeZone($user->user_timezone->raw()));
$company = $DB->getCompany();
$taxclass = $DB->getTaxClassByName($company->company_sales_tax_name->raw());

$errmsg = "";

$formfields=array();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (!$session->checkCSRF())
    {
        $DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
        header("Location: SecurityError.php");
        exit();
    }

    foreach($_POST as $key => $v)
        $formfields[$key] = $v;  
    
    //Checks
    $date = null;
    if (isset($_POST["date"]))
        $date = $_POST["date"];
    try {
        $dt = new DateTime($date);
    }
    catch (Exception $e) {
        $date = null;    
    }

    $chart = 0;
    $chart = FormList::getIntegerField("chart");
    $desc = FormList::getField("desc");
    $amount = FormList::getCurrencyField("amt");
    $inctax = FormList::getCheckboxField("incgst");
    $taxapplies = FormList::getCheckboxField("taxapplies");
    $vendname = FormList::getField("vendname");
    $vendtax = FormList::getField("vendertax");
    
    if ($taxapplies)
        error_log("Tax applies");
    if ($inctax)
        error_log("Includes tax");
    
    if ($date == null )
        $errmsg = "Invalid date specified";
    if ($chart == 0)
        $errmsg = "An expense category must be specified";
    if ($amount == 0)
        $errmsg = "You must enter an amount";

    if (strlen($errmsg) == 0)
    {
        $taxrate = $DB->getTaxRateForClassAndDate($taxclass->idtaxclass,$date);
        $ledger = null;
        if ($taxapplies)
        {
            if ($inctax)   
                $ledger = LedgerAmount::createFromGoss($amount,$taxrate->taxrate_rate);
            else
                $ledger = LedgerAmount::createFromNet($amount,$taxrate->taxrate_rate);
        }
        else
            $ledger = LedgerAmount::createFromNet($amount,0.0);
        
        if (isset($_POST["paid"]))
            $undo = new Undo("Expense been paid");
        else
            $undo = new Undo("Expense to be paid");

        $DB->BeginTransaction();

        if (isset($_POST["paid"]))
            $xtn = $DB->expensePaid($date,$desc,$ledger,$vendname,$vendtax,0,$chart,false);
        else
            $xtn = $DB->expenseUnPaid($date,$desc,$ledger,$vendname,$vendtax,0,$chart,false);
            
        if ($DB->EndTransaction())
        {
            $undo->add(new UndoAction("delete","journal","journal_xtn",$xtn) );
            $undolist->push($undo);
            $DB->updateUndoList($user->iduser,$undolist->toJSON());
            $formfields = array();
        }
        else
            $errmsg = "Database error creating expenses - contact support";
    }
   
}
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
        #msg p.err {color: red;font-weight: bold;font-size: 14pt;}
        #saleheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
        #form {padding: 20px; border: solid 1px #888; border-radius: 8px;}
        #form input {display: block;}
        #form input[type='checkbox'] {margin-top: 16px;display:inline;}
        #form input[type='submit'] {margin-top: 24px;font-size: 14pt;}
        #form select {display: block;}
        #form label {margin-top: 16px;display: block;}
        #form label.first {margin-top: 0px;}
        #vendor {margin-top: 16px;padding: 20px;border: solid 1px #888; background-color: #f0f0f0;}
        #vendor h2 {margin-top: 0;color: #6b6ba7;font-family: Akshar;font-weight: 300;font-size: 16pt;}
        .inputcurrency {text-align: right;}
        #form input.amt {display: inline;}
        #form input.incgst {display: inline;font-size: 14pt;}
        #submit {margin-top: 10px; padding: 10px;border: solid 1px #aaa;}
        #submit p {font-size: 14pt; color: #888;}
        button {margin-top: 24px;display: block;font-size: 14pt;}
        button.b1 {color: red;}
        button.b2 {color: green;}
    </style>
    <script>
        function catChange(n) {
            let l = n.getElementsByTagName("option");
            for (let e of l) {
                if (e.value == n.value) {
                    if (e.getAttribute("_taxclass").length > 0)
                        document.getElementById("taxapplies").checked = true;
                    else
                        document.getElementById("taxapplies").checked = false;
                }
            }
        }

        function venderChange(n) {
            let dl = document.getElementById("vendnamelist");
            let l = dl.getElementsByTagName("option");
            for (let e of l) {
                if (e.value == n.value) {
                    let t = e.getAttribute("_tax");
                    if (t.length > 0)
                        document.getElementById("vendertax").value = t;
                }
            }
        }
    </script>
</head>
<body>
    <div id="container">
        <?php include ("./includes/heading.html");?>
        <?php include ("./includes/menu.html");?>
        <div id="main">
            <?php
            if (strlen($errmsg) > 0)
            {
                echo "<div id='msg'><p class='err'>{$errmsg}</p></div>";
            }
            ?>
            <div id="saleheading">
                <h1>ENTER EXPENSES</h1>
            </div>
            <div id="form">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <?php
                    //Pre form code
                    $strDate = (isset($formfields["date"]) ) ? $formfields["date"] : $dtNow->format("Y-m-d");
                    $strDesc = (isset($formfields["desc"]) ) ? $formfields["desc"] : "";
                    $strAmnt = (isset($formfields["amt"]) ) ? $formfields["amt"] : "";
                    $checked = (isset($formfields["incgst"]) && ! $formfields["incgst"]) ? "" : "checked";
                    $strVendname = (isset($formfields["vendname"]) ) ? $formfields["vendname"] : "";
                    $strVendTax = (isset($formfields["vendertax"]) ) ? $formfields["vendertax"] : "";
                    ?>
                    <div class="formfield">
                        <label for="date" class="first">DATE</label>
                        <input id="date" type="date" name="date" value="<?php echo $strDate;?>" />
                    </div>
                    <div class="formfield">
                        <label for="desc">DESCRIPTION</label>
                        <input id="desc" type="text" name="desc" size="80" value="<?php echo $strDesc;?>" />
                    </div>
                    <div class="formfield">
                        <label for="chart">* EXPENSE CATEGORY</label>
                        <select id="chart" name="chart" onchange="catChange(this)">
                            <option value="0"></option>
                            <?php
                            $charts = $DB->everyChartExpense();
                            foreach($charts as $chart)
                            {
                                $selected = (isset($formfields["chart"]) && $formfields["chart"] == $chart["chart_code"]) ? "selected" : "";
                                $d = htmlspecialchars($chart["chart_description"]);
                                echo "<option value='{$chart["chart_code"]}' _taxclass='{$chart['taxclass_name']}' {$selected}>{$d}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="formfield">
                        <label for="amt">* AMOUNT</label>
                        <input id="amt" class="inputcurrency amt" type="text" name="amt" size="8" placeholder="$0.00" value="<?php echo $strAmnt;?>" />
                        <input id="incgst" class="incgst" type="checkbox" name="incgst" <?php echo $checked;?> /><span>AMOUNT INCLUDES <?php echo $company->company_sales_tax_name->toHTML();?></span>
                    </div>
                    <div class="formfield">
                        <input id="taxapplies" type="checkbox" name="taxapplies" /><span><?php echo $company->company_sales_tax_name->toHTML();?> APPLIES ON THIS EXPENSE</span>
                    </div>

                    <div id="vendor">
                        <h2>VENDOR INFORMATION</h2>
                        <div class="formfield">
                            <label for="vendname">VENDOR NAME</label>
                            <input list="vendnamelist" id="vendname" type="text" name="vendname" size="60" value="<?php echo $strVendname;?>" onchange="venderChange(this)"/>
                            <datalist id="vendnamelist">
                                <?php
                                //Look up all journals for expenses to get vendor names from past
                                $namelist = $DB->getJournalVendorNameList();
                                foreach($namelist as $name)
                                {
                                    $v = htmlspecialchars($name["journal_vendor_name"]);
                                    $t = htmlspecialchars($name["journal_vendor_tax_number"]);
                                    echo "<option value='{$v}' _tax='{$t}'></option>";
                                }
                                ?>
                            </datalist>
                        </div>
                        <div class="formfield">
                            <label for="vendertax">VENDOR TAX NUMBER</label>
                            <input id="vendertax"type="text" name="vendertax" size="10" value="<?php echo $strVendTax;?>"/>
                        </div>
                    </div>

                    <?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
                    <div id="submit">
                    <p>Once completed choose from:</p>
                        <div id="buttons">
                            <button class="b1" type="submit" name="notpaid" title="This expense has not been paid yet">ENTER - NOT PAID YET</button>
                            <button class="b2" type="submit" name="paid" title="This expense has been paid and funds have been deducted from your bank account">ENTER - BEEN PAID</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<?php include ("./includes/footer.html");?>
</html>