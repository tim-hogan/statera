<?php
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

if (!isset($_GET['v']))
{
    error_log("ERROR: {$selff} [" .__LINE__. "] Call to invoice without parameter");
    header("Location: SecurityError.php");
    exit();
}

$s = Secure::sec_decryptParamPart($_GET['v'],base64_encode($session->session_key));
if (!$s || strlen($s) == 0)
{
    error_log("ERROR: {$selff} [" .__LINE__. "] Unable to invoice key");
    header("Location: SecurityError.php");
    exit();
}
parse_str($s,$a);

$invoiceid = 0;
if (isset($a['i']))
    $invoiceid = intval($a['i']);
$invoice = $DB->getInvoice($invoiceid);
$invoice_title = sprintf("%06d",$invoice->invoice_number);


function buildAccountLines($invoice,&$lines)
{
    $alines =  array();

    if ($invoice->invoice_cash_sale)
        $alines[] = "CASH SALE";

    if ($invoice->invoice_account_name->length() > 0)
        $alines[] = $invoice->invoice_account_name->toHTML();

    if ($invoice->invoice_account_address1->length() > 0)
        $alines[] = $invoice->invoice_account_address1->toHTML();
    if ($invoice->invoice_account_address2->length() > 0)
        $alines[] = $invoice->invoice_account_address2->toHTML();
    if ($invoice->invoice_account_address3->length() > 0)
        $alines[] = $invoice->invoice_account_address3->toHTML();
    if ($invoice->invoice_account_address4->length() > 0)
        $alines[] = $invoice->invoice_account_address4->toHTML();

    if ($invoice->invoice_account != 0)
        $alines[] = "ACCOUNT NUMBER " . sprintf("%06d", $invoice->invoice_account);

    $lines[0] = $alines;

}

function buildCompanyLines($invoice,&$lines)
{
    $clines =  array();

    if ($invoice->invoice_company_address1->length() > 0)
        $clines[] = $invoice->invoice_company_address1->toHTML();
    if ($invoice->invoice_company_address2->length() > 0)
        $clines[] = $invoice->invoice_company_address2->toHTML();
    if ($invoice->invoice_company_address3->length() > 0)
        $clines[] = $invoice->invoice_company_address3->toHTML();
    if ($invoice->invoice_company_address4->length() > 0)
        $clines[] = $invoice->invoice_company_address4->toHTML();

    if ($invoice->invoice_company_city->length() > 0)
    {
        $str = $invoice->invoice_company_city->toHTML();
        if ($invoice->invoice_company_postcode->length() > 0)
            $str .= ", {$invoice->invoice_company_postcode->toHTML()}";
        $clines[] = $str;
    }

    if ($invoice->invoice_company_country->length() > 0)
        $clines[] = $invoice->invoice_company_country->toHTML();

    $lines[1] = $clines;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>INVOICE</title>
    <link href="css/base.css" rel="stylesheet" />
    <link href="css/heading.css" rel="stylesheet" />
    <link href="css/menu.css" rel="stylesheet" />
    <style>
        #container {margin: auto; width: 1000px;}
        #page {border: solid 1px #888;}
        #printarea {margin: 40px;}
        #nonprint {text-align: center;}
        #nonprint button {margin-top: 15px; margin-bottom: 15px;}
        td.h1 {font-size: 16pt;vertical-align:top;}
        td.h1r {font-size: 20pt;font-weight: bold;text-align: right;vertical-align:top;}
        td.td4 {padding-right: 8px;}
        td.td31 {font-size: 14pt; font-weight: bold;border-top: solid 1px black;border-bottom: solid 1px black;}
        td.td51 {border-bottom: solid 1px black;}
        td.td52 {font-weight: bold; font-size: 12pt;color: white; background-color: black;}
        td.tdadl {font-size: 12pt; text-align: left;}
        td.tdadr {font-size: 12pt; text-align: right;}
        td.tdtail1 {font-size: 14pt;}
        td.blank1 {height: 10px;}
        td.blank2 {height: 15px;}
        td.blank3 {height: 20px;}
        td.blank4 {height: 1em;}
        td.bt {border-top: solid 1px black;}
        #printarea table {width: 100%;}
        #middle td {vertical-align: top;}
        #middle td.desc {width: 75%; padding-right: 2cm;}
        #middle td.tdm {vertical-align: middle;}
        .r {text-align: right;}
        @media print {
            #container {margin: 0; padding: 0; width: 21cm; height: 29.7cm;}
            #heading {display: none;}
            #menu {display: none;}
            #nonprint {display: none;}
            #page {width: 21cm; height: 29.7cm;border: none;}
            #printarea {margin: 2.5cm;}
            #printarea table {width: 16cm; border: none;}
            td.blank1 {height: 0.5cm;}
            td.blank2 {height: 0.75cm;}
            td.blank3 {height: 1cm;}
            td.td4 {padding-right: 0.5cm;}
            body{-webkit-print-color-adjust: exact}
        }
    </style>
    <script>
        <?php echo "var g_Title = 'INVOICE-{$invoice_title}';\n";?>
        function start() {
            (document.getElementsByTagName("title")[0]).innerHTML = g_Title;
        }
    </script>
</head>
<body onload="start()">
    <div id="container">
        <?php include ("./includes/heading.html");?>
        <?php include ("./includes/menu.html");?>
        <div id="nonprint">
            <button onclick="window.print()">PRINT</button>
        </div>
        <div id="page">
            <div id="printarea">
                <table>
                    <?php
                    if ($invoice->invoice_sale_tax_class)
                        echo "<tr><td class='h1'>TAX INVOICE</td><td class='h1r'>{$invoice->invoice_company_name->toHTML()}</td></tr>";
                    else
                        echo "<tr><td class='h1'>INVOICE</td></tr>";
                    echo "<tr><td class='blank1' colspan='2'></td></tr>";
                    $strDate = (new DateTime($invoice->invoice_date))->format('jS F Y');
                    echo "<tr><td>{$strDate}</td><td></td></tr>";
                    echo "<tr><td class='blank1' colspan='2'></td></tr>";

                    //Do lines
                    $lines = array();
                    buildAccountLines($invoice,$lines);
                    buildCompanyLines($invoice,$lines);

                    $maxlines = max(count($lines[0]), count($lines[1]));
                    for ($i=0;$i<$maxlines;$i++)
                    {
                        echo "<tr>";
                        echo "<td class='tdadl'>";
                        if (isset($lines[0] [$i]))
                            echo $lines[0] [$i];
                        echo "</td>";
                        echo "<td class='tdadr'>";
                        if (isset($lines[1] [$i]))
                            echo $lines[1] [$i];
                        echo "</td>";
                        echo "</tr>";
                    }

                    //Invoice number
                    $strInvNum = sprintf("%06d",$invoice->invoice_number);
                    echo "<tr><td class='blank1' colspan='2'></td></tr>";

                    $left = array();
                    if ($invoice->invoice_account_ref1->length() > 0)
                        $left[] = $invoice->invoice_account_ref1->toHTML();

                    if ($invoice->invoice_account_ref2->length() > 0)
                        $left[] = $invoice->invoice_account_ref2->toHTML();

                    $right = array();
                    $right[] =  "INVOICE NUMBER {$strInvNum}";

                    if ($invoice->invoice_sale_tax_class)
                    {
                        $right[] = "{$invoice->invoice_sale_tax_name->toHTML()} {$invoice->invoice_tax_number->toHTML()}";
                    }


                    $maxlines = max(count($left), count($right));
                    for ($i=0;$i<$maxlines;$i++)
                    {
                        echo "<tr>";
                        echo "<td class='tdadl'>";
                        if (isset($left[$i]))
                            echo $left[$i];
                        echo "</td>";
                        echo "<td class='tdadr'>";
                        if (isset($right[$i]))
                            echo $right[$i];
                        echo "</td>";
                        echo "</tr>";
                    }


                    echo "<tr><td class='blank1' colspan='2'></td></tr>";
                    ?>
                </table>
                <div id="middle">
                    <table>
                        <tr><td class="bt" colspan="5"></td></tr>
                        <tr><td class='blank1' colspan='5'></td></tr>
                        <tr><td>DESCRITION</td><td class="r">QTY</td><td>UNIT</td><td class="r td4">UNIT PRICE</td><td class="r">TOTAL</td></tr>
                        <tr><td class='blank1' colspan='5'></td></tr>
                        <?php
                            $net = 0.0;
                            $strnet = "";
                            $strTotal = "";
                            $lines = $DB->everyInvoiceLine($invoice->idinvoice);
                            foreach($lines as $l)
                            {
                                $desc = htmlspecialchars($l["invoice_line_description"]);
                                $qty = floatval($l["invoice_line_qty"]);
                                if ($qty > 0)
                                {
                                    $unitdesc = htmlspecialchars($l["invoice_line_unit_desc"]);
                                    $unit = "$". number_format($l["invoice_line_unit_cost"],2);
                                    $total = "$". number_format($l["invoice_line_total_cost"],2);
                                    $net += $l["invoice_line_total_cost"];
                                    echo "<tr><td class='desc'>{$desc}</td><td class='r'>{$qty}</td><td>{$unitdesc}</td><td class='r td4'>{$unit}</td><td class='r'>{$total}</td></tr>";
                                    echo "<tr><td class='blank4' colspan='5'></td></tr>";
                                }
                                else
                                {
                                    echo "<tr><td>{$desc}</td><td colspan='3'></td></tr>";
                                }
                            }

                            $strnet = "$". number_format($net,2);
                            echo "<tr><td class='blank3' colspan='5'></td></tr>";
                            if ($invoice->invoice_sale_tax_class > 0)
                            {
                                $inv_total = $net;
                                $inv_tax = 0;
                                $taxclass = $DB->getTaxClass($invoice->invoice_sale_tax_class);
                                $strTaxName = strtoupper($taxclass->taxclass_name->toHTML());
                                $taxrate = $DB->getTaxRateForClassAndDate($invoice->invoice_sale_tax_class,$invoice->invoice_date);
                                if ($taxrate)
                                {
                                    $inv_tax = round($net * $taxrate->taxrate_rate,2);
                                    $strinv_tax = "$". number_format($inv_tax,2);
                                    $inv_total = $net + $inv_tax;
                                    $strTotal = "$". number_format($inv_total,2);
                                }
                                echo "<tr><td colspan='3'></td><td class='r td4'>NET</td><td class='r td51'>{$strnet}</td></tr>";
                                echo "<tr><td class='blank2' colspan='5'></td></tr>";
                                echo "<tr><td colspan='3'></td><td class='r td4'>{$strTaxName}</td><td class='r'>{$strinv_tax}</td></tr>";
                                echo "<tr><td class='blank2' colspan='5'></td></tr>";
                                echo "<tr><td class='r td4 tdm' colspan='4'>TOTAL INVOICE</td><td class='r td52'>{$strTotal}</td></tr>";
                            }
                            else
                            {
                                echo "<tr><td class='r td4 tdm' colspan='4'>TOTAL INVOICE</td><td class='r td52'>{$strnet}</td></tr>";
                                $strTotal = $strnet;
                            }


                        ?>
                    </table>
                </div>
                <div id="tail">
                    <table>
                        <tr><td class="bt" colspan="5"></td></tr>
                        <?php
                        if ($invoice->invoice_cash_sale)
                        {
                            echo "<tr><td class='blank3' colspan='3'></td></tr>";
                            echo "<tr><td class='tdtail1' colspan='3'>RECEIPT</td></tr>";
                            echo "<tr><td class='blank1' colspan='3'></td></tr>";
                            echo "<tr><td>INVOICE TOTAL</td><td></td><td class='r'>{$strTotal}</td></tr>";
                            echo "<tr><td>RECEIVED</td><td class='r'>{$strTotal}</td><td></td></tr>";
                            echo "<tr><td class='blank1' colspan='3'></td></tr>";
                            echo "<tr><td>BALANCE</td><td class='r'></td><td class='r td31'>$0.00</td></tr>";
                        }
                        else
                        {
                            echo "<tr><td class='blank3' ></td></tr>";
                            echo "<tr><td class='tdtail1'>REMITTANCE INFORMATON</td></tr>";
                            echo "<tr><td class='blank1'></td></tr>";
                            if ($invoice->invoice_bank_acct_name->length() > 0)
                            {
                                echo "<tr><td>BANK: {$invoice->invoice_bank_acct_name->toHTML()}</td></tr>";
                            }
                            if ($invoice->invoice_bank_acct_number->length() > 0)
                            {
                                echo "<tr><td>ACCOUNT NUMBER: {$invoice->invoice_bank_acct_number->toHTML()}</td></tr>";
                            }
                            echo "<tr><td>REFERENCE: {$strInvNum}</td></tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>