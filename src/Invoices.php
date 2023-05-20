<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>CASH SALE</title>
    <link href="css/base.css" rel="stylesheet" />
    <link href="css/heading.css" rel="stylesheet" />
    <link href="css/menu.css" rel="stylesheet" />
    <link href="css/footer.css" rel="stylesheet" />
    <style>
    #list {padding: 20px;}
    h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
    #list td, th {padding-right: 20px;}
    .l {text-align: left;}
    .r {text-align: right;}
    </style>
</head>
<body>
    <div id="container">
        <?php include ("./includes/heading.html");?>
        <?php include ("./includes/menu.html");?>
        <div id="main">
            <div id="list">
            <h1>INVOICES</h1>
                <table>
                    <tr><th class="l">NUMBER</th><th class="l">DATE</th><th class="l">ACCOUNT</th><th class="r">VALUE (Inc TAX)</th><th class="r">OUTSTANDING</th></tr>
                    <?php
                    $r = $DB->allInvoices();
                    while ($invoice = $r->fetch_object("invoice"))
                    {
                        $v = "i={$invoice->idinvoice}";
                        $s = Secure::sec_encryptParam($v,base64_encode($session->session_key));
                        echo "<tr>";
                        $in = sprintf("%06d",$invoice->invoice_number);
                        echo "<td><a href='Invoice.php?v={$s}'>{$in}</a></td>";
                        $strDate = (new DateTime($invoice->invoice_date))->format('jS M Y');
                        echo "<td>{$strDate}</td>";
                        if ($invoice->invoice_cash_sale)
                            echo "<td>CASH SALE</td>";
                        else
                            echo "<td>{$invoice->invoice_account_name->toHTML()}</td>";


                        $net = $DB->sumInvoiceLines($invoice->idinvoice) ['NET'];

                        if ($invoice->invoice_sale_tax_class > 0)
                        {
                            $taxrate = $DB->getTaxRateForClassAndDate($invoice->invoice_sale_tax_class,$invoice->invoice_date);
                            $total = round($net * (1+$taxrate->taxrate_rate),2);
                        }
                        else
                            $total = $net;

                        $strV = "$" . number_format($total,2);

                        echo "<td class='r'>{$strV}</td>";
                        $o = $DB->journalInvoiceOutstanding($invoice->idinvoice);
                        if ($o["GROSS"] == 0.00)
                            echo "<td class='r'>PAID</td>";
                        else
                        {
                            $outstanding = "$" . number_format($o["GROSS"],2);
                            echo "<td class='r'>{$outstanding}</td>";
                        }

                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
