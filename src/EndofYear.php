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


define("ACTION_NONE",0);
define("ACTION_COMPLETE",1);
define("ACTION_UNDO",2);


$strmsg = "";
$strerr = "";

$company = $DB->getCompany();
$company->company_sales_tax_first_month;

$AcctDate = new AccountDate(AccountDate::subMonths($company->company_sales_tax_first_month,1));
$cuurent_financial_year = $AcctDate->finacialYear(new DateTime());

$firstdate = $DB->getJournalFirstDate();
if (!$firstdate)
    $strmsg = "There are no records in the journal to process at this time";

$first_financial_year = $AcctDate->finacialYear(new DateTime($firstdate));

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (!$session->checkCSRF())
    {
        $DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
        header("Location: SecurityError.php");
        exit();
    }

    if (isset($_POST["complete"]))
    {
        $year_end = $_POST["complete"];
        error_log("Year end is {$year_end}");

        //Check to see if we have laready got an endo f year record
        if (!$DB->getJournalStartEOYRecord($year_end) &&  !$DB->getJournalEndEOYRecord($year_end))
        {
            $DB->BeginTransaction();

            $DB->createJournalStartEOYRecord($year_end);

            //Calculate depreciation
            $coa1 = $this->getChartFor('asset','fixed_asset',null,SEARCH_FIRST);
            $coa2 = $this->getChartFor('expense','financial','depreciation',SEARCH_FIRST);
            $r = $DB->allAssets();
            while ($asset = $r->fetch_object("asset"))
            {
                $current_value = $DB->assetValue($asset["idasset"],$year_end);
                $orginal_value = $DB->assetOrginalValue($asset["idasset"]);
                $ageMonths = $DB->assetAgeMonths($asset["idasset"],$year_end);
                if ($orginal_value > 0)
                {
                    //Create a depritiation record
                    $j1["journal_date"] = $year_end;
                    $j1["journal_asset"] = $asset["idasset"];
                    $j1["journal_description"] = "Depreciation";
                    $j1["journal_tax"] = 0.00;
                    $j1["journal_gross"] = 0.00;

                    $depreciation = 0.0;
                    if ($asset["asset_depreciation_method"] == "sl")
                    {
                        if ($ageMonths > 0)
                        {
                            if ($ageMonths >= 12)
                            {
                                $depreciation = $orginal_value * $asset["asset_depreciation_rate"];
                            }
                            else
                            {
                                $depreciation = $orginal_value * $asset["asset_depreciation_rate"];
                                $depreciation *= floatval($ageMonths)/12.0;
                            }
                        }
                    }

                    $j1["journal_net"] = -($depreciation);

                    $DB->createPair($j1,$coa1,$coa2,0,false);
                }
            }

            $DB->createJournalEndEOYRecord($year_end);

            if (! $DB->EndTransaction() )
                $strerr = "Database error performing eod of year processing";

        }
        else
            $strerr="End of year has already been processed";
    }

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>YEAR END</title>
    <link href="css/base.css" rel="stylesheet" />
    <link href="css/heading.css" rel="stylesheet" />
    <link href="css/menu.css" rel="stylesheet" />
    <link href="css/footer.css" rel="stylesheet" />
    <style>
    #main {margin: 20px;}
    #main h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
    #completed_list th {padding-right: 28px;}
    #completed_list td {padding-right: 28px;}
    </style>
</head>
<body>
    <div id="conatiner">

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

            <h1>END OF YEAR PROCESSING</h1>
            <?php
                if (strlen($strmsg) > 0)
                {
                    echo "<div id='msg'>";
                    echo "<p>{$strmsg}</p>";
                    echo "</div>";
                }
            ?>
            <div id="completed_list">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <table>
                        <tr>
                            <th>YEAR END DATE</th><th>STATUS</th><th>ACTION</th>
                        </tr>
                        <?php
                    $d = $first_financial_year[1];
                    while ($d <= $cuurent_financial_year[1])
                    {
                        $action = ACTION_NONE;
                        $strDate = $d->format("Y-m-d");
                        echo "<tr>";
                        echo "<td>{$d->format("j/n/Y")}</td>";
                        $jstart = $DB->getJournalStartEOYRecord($d);
                        $jend = $DB->getJournalEndEOYRecord($d);
                        echo "<td>";
                        if (!$jstart && !$jend)
                        {
                            if ($d != $cuurent_financial_year[1]) {
                                echo "REQUIRED";
                                $action = ACTION_COMPLETE;
                            }
                        }
                        if ($jstart && $jend)
                        {
                            echo "COMPLETE";
                            $action = ACTION_UNDO;
                        }
                        if (($jstart && !$jend) || (!$jstart && $jend))
                            echo "ERROR";
                        echo "</td>";

                        echo "<td>";
                        switch ($action)
                        {
                            case ACTION_NONE;
                                break;
                            case ACTION_COMPLETE:
                                echo "<button name='complete' value = '{$strDate}'>PERFROM END OF YEAR</button>";
                                break;
                            case ACTION_UNDO:
                                echo "<button _date='{$strDate}' _action='undo'>PERFROM END OF YEAR</button>";
                                break;
                        }


                        echo "</td>";


                        echo "</tr>";

                        $d->add(new DateInterval("P1Y"));
                    }
                        ?>
                    </table>
                    <?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
                </form>
            </div>
        </div>
    </div>
</body>
</html>