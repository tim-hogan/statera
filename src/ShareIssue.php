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

$formfields = array();
$strerr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

    if (!$session->checkCSRF())
    {
        $DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
        header("Location: SecurityError.php");
        exit();
    }
    $totShares = 0;
    $totCurrent = 0;

    $formfields["date"] = FormList::getDateField("date");
    $formfields["shareholder"] = FormList::getIntegerField("shareholder");
    $formfields["qty"] = FormList::getIntegerField("qty");
    $formfields["price"] = FormList::getCurrencyField("price");
    $formfields["paid"] = FormList::getCheckboxField("paid");
    $formfields["cash"] = FormList::getCurrencyField("cash");

    if ($formfields["shareholder"] == 0)
        $strerr = "You must specify a shareholder";

    if ($formfields["qty"] <= 0)
        $strerr = "Share quantity must be greater than zero";

    if ($formfields["price"] <= 0)
        $strerr = "Share price must be greater than zero";

    if ($formfields["paid"])
    {
        $totShares = $formfields["qty"] * $formfields["price"];
        if ($totShares > $formfields["cash"])
        {
            $v = "$" . number_format($totShares,2);
            $strerr = "Cash paid must be greater or equal to total share value of {$v}";
        }
        $totCurrent = $formfields["cash"]-$totShares;
    }

    if (strlen($strerr) == 0 )
    {
        $undo = new Undo("Share issue");
        //Issue shares
        $DB->BeginTransaction();

        $rslt = $DB->issueShares($formfields["date"],$formfields["shareholder"],$formfields["qty"],$formfields["price"],$totShares,$totCurrent);
        if (!$rslt[0] || ! $rslt[1])
            $DB->TransactionError();
        
        if ($rslt[0])
            $undo->add(new UndoAction("delete","share","idshare",$rslt[0]) );
        if ($rslt[1])
            $undo->add(new UndoAction("delete","journal","journal_xtn",$rslt[1]) );
        if ($rslt[2])
            $undo->add(new UndoAction("delete","journal","journal_xtn",$rslt[2]) );

        $undolist->push($undo);
        if (! $DB->isTransactionError())
            $DB->updateUndoList($user->iduser,$undolist->toJSON());

        if (!$DB->EndTransaction() )
        {
            error_log("Database transaction failed");
            $strerr = "ERROR: Database transaction failed - call support";
        }
    }

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>SHARE ISSUE</title>
    <link href="css/base.css" rel="stylesheet" />
    <link href="css/heading.css" rel="stylesheet" />
    <link href="css/menu.css" rel="stylesheet" />
    <link href="css/footer.css" rel="stylesheet" />
    <style>
        h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
        h2 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
        #shareheading {margin-left: 20px;}
        #msg {margin: 20px;}
        #form {margin: 20px;padding: 20px;border: solid 1px #888;border-radius: 8px;}
        #form label {display: block;margin-top: 20px;}
        #form label.first {display: block;margin-top: 0px;}
        #form input[type="submit"] {margin-top: 20px;}
        #split {margin: 10px; padding: 10px; border: solid 1px #888;}
        p.err {font-size: 12pt;color: red; font-weight: bold;}
        .r {text-align: right;}
    </style>
    <script>
        var x = {
            ge: function (t) {
                return document.getElementById(t);
            },
            disp: function (t, w) {
                x.ge(t).style.display = w;
            }
        }
        function paidup(n) {
            if (n.checked) x.disp("split","block"); else x.disp("split","none");
        }

        function calc() {
            let q = parseFloat(x.ge("qty").value);
            let p = x.ge("price").value;
            p = p.trim();
            p = p.replace("$", "");
            p = parseFloat(p);
            t = q * p;
            t = t.toFixed(2);
            x.ge("cash").value = t;
            x.ge("cashshares").value = t;
            t = 0;
            x.ge("cashcurrentact").value = t.toFixed(2);
        }

        function calcsplit() {
            let q = parseFloat(x.ge("qty").value);
            let p = x.ge("price").value;
            p = p.trim();
            p = p.replace("$", "");
            p = parseFloat(p);
            let t = q * p;
            let c = parseFloat(x.ge("cash").value);
            x.ge("cashshares").value = t.toFixed(2);
            let v = c - t;
            x.ge("cashcurrentact").value = v.toFixed(2);
        }

    </script>
</head>
<body>
    <div id="container">
        <?php include ("./includes/heading.html");?>
        <?php include ("./includes/menu.html");?>
        <?php
            if (strlen($strerr) > 0)
            {
                echo "<div id='msg'>";
                echo "<p class='err'>{$strerr}</p>";
                echo "</div>";
            }
        ?>
        <div id="main">
            <div id="shareheading">
                <h1>ISSUE SHARES</h1>
            </div>
            <div id="form">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="divfield">
                    <label for="date" class="first">ISSUE DATE</label>
                    <?php
                    $dtNow = new DateTime();
                    $dtNow->setTimezone(new DateTimeZone($user->user_timezone->raw()));
                    $strDate = $dtNow->format("Y-m-d");
                    echo "<input id='date' type='date', name='date' value='{$strDate}'/>";
                    ?>
                </div>
                <div class="divfield">
                    <label for="shareholder">SHARE HOLDER</label>
                    <select id="shareholder" name="shareholder">
                        <option value="0"></option>
                        <?php
                        $r = $DB->allShareholders("where shareholder_deleted = 0", "order by shareholder_lastname");
                        while ($sh = $r->fetch_object("shareholder"))
                        {
                            $name = strtoupper($sh->shareholder_lastname->toHTML()) . ", " . $sh->shareholder_firstnames->toHTML();
                            $name = trim($name,",");
                            $name = trim($name);
                            echo "<option value='{$sh->idshareholder}'>{$name}</option>";
                        }
                        ?>
                    </select>
                </div>    
                <div class="divfield">
                    <label for="qty">NUMBER OF SHARES</label>
                    <input id="qty" type="text" class="r" name="qty" size="4" onchange="calc()"/>
                </div>
                <div class="divfield">
                    <label for="price">SHARE PRICE</label>
                    <input id="price" type="text" class="r" name="price" size="4" value="$1.00" onchange="calc()" />
                </div>
                <div class="divfield">
                   <label for="paid">FULLY PAID UP</label>
                   <input id="paid" type="checkbox" name="paid" checked onchange="paidup(this)"/>
                </div>
                <div id="split">
                    <h2>FUNDS SPLIT</h2>
                    <div class="divfield">
                        <label for="cash">CASH PAID</label>
                        <input id="cash" type="text" class="r" name="cash" size="9" onchange="calcsplit()"/>
                    </div>
                    <div class="divfield">
                        <label for="cashshares">SHARES</label>
                        <input id="cashshares" type="text" class="r" name="cashshares" size="9" readonly/>
                    </div>
                    <div class="divfield">
                        <label for="cashcurrentact">SHAREHOLDER CURRENT ACCOUNT</label>
                        <input id="cashcurrentact" type="text" class="r" name="cashcurrentact" size="9" readonly/>
                    </div>
                </div>               
                <?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
                <input type="submit" name="issue" value="ISSUE SHARES" />
                </form>
            </div>
        </div>
     </div>
    <?php include ("./includes/footer.html");?>
</body>
</html>
