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



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>BANK ACCOUNTS</title>
    <link href="css/base.css" rel="stylesheet" />
    <link href="css/heading.css" rel="stylesheet" />
    <link href="css/menu.css" rel="stylesheet" />
    <link href="css/footer.css" rel="stylesheet" />
    <style>
        #main {margin: 20px;}
        #main h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
        #table {padding: 20px;}
        #table table {border-collapse:collapse;}
        #table th, td {padding-right: 14px;vertical-align: top;}
        #table td.acct {font-size: 14pt;color: #6b6ba7;font-weight: bold;padding-top: 12px;padding-bottom: 8px;}
        #table td.tddesc {min-width: 200px;}
        .r {text-align: right;}
    </style>
</head>
<body>
    <div id="container">
        <?php include ("./includes/heading.html");?>
        <?php include ("./includes/menu.html");?>
        <div id="main">
            <h1>BANK ACCOUNT</h1>
            <div id="table">
                <table>
                    <tr><th>DATE</th><th>DESCRIPTION</th><th class="r">DEPOSITS</th><th class="r">WITHDRAWALS</th><th class="r">BALANCE</th></tr>
                    <?php
                    $balances =  $DB->getBankBalances();
                    $acts = $DB->AllBankTransactions(100);
                    foreach ($acts as $chart => $act)
                    {
                        $accanme = htmlspecialchars($act["name"]);
                        $balance = $balances[$chart];
                        $strBalance = LedgerAmount::format1($balance);
                        echo "<tr><td class='acct' colspan='4'>{$accanme}</td><td class='acct r'>{$strBalance}</td></tr>";
                        while ($j = $act["r"]->fetch_assoc())
                        {
                            $dt = new DateTime($j['journal_date']);
                            //Need to build the descriptions
                            $desc = $DB->allDescriptionsForFolio($j["journal_folio"]);
                            $desc = str_replace("\n","<br/>",$desc);
                            $cr = "";
                            $dr = "";
                            if ($j["GROSS"] > 0 )
                                $cr = number_format($j["GROSS"],2);
                            if ($j["GROSS"] < 0 )
                                $dr = number_format(-$j["GROSS"],2);

                            $strBalance = LedgerAmount::format1($balance);
                            echo "<tr><td class='r'>{$dt->format('j M Y')}</td><td class='tddesc'>{$desc}</td><td class='r'>{$cr}</td><td class='r'>{$dr}</td><td class='r'>{$strBalance}</td></tr>";
                            $balance -= $j["GROSS"];
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
    <?php include ("./includes/footer.html");?>
</body>
</html>