<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classTime.php";
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




$DB = new stateraDB($devt_environment->getDatabaseParameters());
$session = new Session($DB,"getSession","setSession");
$selff = trim($_SERVER["PHP_SELF"],"/");
if ($session->isAvailable())
    $user = $DB->getUserByRandId($session->Id());
if (!$user)
{
    $session->redirect = $selff;
    header("Location: Signin.php");
    exit();
}
if ($session->session_key)
    $_SESSION["session_key"] = $session->session_key;

$g_months = ["JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC"];
$company = $DB->getCompany();
$saleTaxMonths = AccountDate::cadenceMonths($company->company_sales_tax_cadence,$company->company_sales_tax_first_month);
$report = null;
$dateRange = null;

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    if (isset($_GET["v"]))
    {
        $s = Secure::sec_decryptParamPart($_GET["v"],base64_encode($session->session_key));
        if ($s && strlen($s) > 0)
        {
            parse_str($s,$a);
            if (isset($a["p"]))
            {
                $d = new DateTime($a["p"]);
                $dateRange = AccountDate::cadenceRangeMonths(intval($d->format("Y")),intval($d->format("m")),$company->company_sales_tax_cadence,false);
                $report = $DB->gstReport($dateRange[0],$dateRange[1]);
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (!$session->checkCSRF())
    {
        $DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
        header("Location: SecurityError.php");
        exit();
    }
    $dateRange = AccountDate::cadenceRangeMonths($_POST["year"],$_POST["month"],$company->company_sales_tax_cadence,false);
    var_error_log($dateRange,"daterange");
    $report = $DB->gstReport($dateRange[0],$dateRange[1]);

}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>GST REPORT</title>
    <link href="css/base.css" rel="stylesheet" />
    <link href="css/heading.css" rel="stylesheet" />
    <link href="css/menu.css" rel="stylesheet" />
    <link href="css/footer.css" rel="stylesheet" />
    <style>
    #entry {margin: 20px; padding: 20px; border: solid 1px #888; border-radius: 8px;}
    #entry input[type='submit'] {margin-top: 20px;}
    #results {margin: 20px; padding: 20px; border: solid 1px #888; border-radius: 8px;}
    .r {text-align: right;}
    </style>
</head>
<body>
    <div id="container">
        <?php include ("./includes/heading.html");?>
        <?php include ("./includes/menu.html");?>
        <div id="main">
            <div id="entry">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <table>
                        <p>SELECT GST REPORT END MONTH</p>
                        <tr>
                            <td>END MONTH</td><td>YEAR</td>
                        </tr>
                        <?php
                        echo "<tr>";
                        echo "<td>";
                        echo "<select name='month'>";
                        foreach ($saleTaxMonths as $k => $m)
                        {
                            echo "<option value='{$k}'>{$m}</options>";
                        }
                        echo "</select>";
                        echo "</td>";

                        echo "<td>";
                        echo "<select name='year'>";
                        $startYear = intval((new DateTime($company->company_start_date))->format("Y"));
                        $endYear = intval((new DateTime())->format("Y"));
                        while ($startYear <= $endYear)
                        {
                            $selected = "";
                            if ($startYear == $endYear)
                                $selected = "selected";
                            echo "<option value='$startYear' {$selected}>{$startYear}</options>";
                            $startYear++;
                        }
                        echo "</select>";
                        echo "</td>";

                        echo "</tr>";
                        ?>
                    </table>
                    <?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
                    <input type="submit" name="create" value="CREATE REPORT" />
                </form>    
            </div>
            <div id="results">
                <table>
                    <?php
                        if ($report)
                        {
                            $strd1 = (new DateTime($dateRange[0]))->format("j M Y");
                            $strd2 = (new DateTime($dateRange[1]))->format("j M Y");
                            echo "<h2>{$company->company_sales_tax_name->raw()} Report for {$strd1} - {$strd2}</h2>";
                            $detail = $report["detail"];
                            foreach($detail as $l)
                            {
                                $v = LedgerAmount::format1($l["value"]);
                                if ($l["line"] == 15)
                                    echo "<tr><td>{$l["line"]}</td><td>{$l["name"]}</td><td class='r'>{$v}</td><td>{$l["suffix"]}</td></tr>";
                                else
                                    echo "<tr><td>{$l["line"]}</td><td>{$l["name"]}</td><td class='r'>{$v}</td></tr>";
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
