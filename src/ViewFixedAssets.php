<?php
session_start();
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
$o_comapny = $DB->getCompany();
$year_End_month = (($o_comapny->company_financialyear_start_month - 2) % 12) + 1;
$acct_date = new AccountDate($year_End_month);

$o_assets = $DB->o_everyAsset();
if ($o_assets)
{
	$firstdate = $o_assets[0]->asset_purhcase_date;
	$fyfirst = $acct_date->finacialYear($firstdate);
	$fylast = $acct_date->finacialYear(new DateTime());
	var_error_log($fylast, "fylast");
	$dt_year_end = clone $fylast[1];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>QUOTES</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 20px;}
		#list th,td {padding-right: 24px;}
		.r {text-align: right;}
		.b {font-weight: bold;}
		.gap {height: 16px;}
        @media print {
			#heading {display: none;}
			#menu {display: none;}
		}

	</style>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<div id="list">
				<table>
					<?php
					if ($o_assets)
					{
						$startyear = intval(($fyfirst[1])->format("Y"));
						$endyear = intval(($fylast[1])->format("Y"));
						echo "<table>";
						echo "<tr><th></th><th></th><th colspan='2'>DEPRECIATION</th></tr>";
						echo "<tr><th>PURCAHSE DATE</th><th>NAME</th><th>METHOD</th><th>RATE</th><th>PURCHASE PRICE</th><th>AGE (MONTHS)</th>";
						for ($y = $startyear; $y <= $endyear; $y++)
						{
							echo "<th>{$y}</th>";
						}
						echo "</tr>";
						$tot1 = 0.00;
						$tot2 = 0.00;

						foreach($o_assets as $asset)
						{
							$strDate = (new DateTime($asset->asset_purhcase_date))->format("j/n/Y");
							$strDepMethod = strtoupper($asset->asset_depreciation_method);
							$strRate = sprintf("%2.1f%%", $asset->asset_depreciation_rate * 100.0);
							$orginal_value = $DB->assetOrginalValue($asset->idasset);
							$tot1 += $orginal_value;
							$strOrgValue = LedgerAmount::format1($orginal_value);
							$ageMonths = $DB->assetAgeMonths($asset->idasset, $dt_year_end);
							echo "<tr><td class='r'>{$strDate}</td><td>{$asset->asset_name->toHTML()}</td><td>{$strDepMethod}</td><td>{$strRate}</td><td class='r'>{$strOrgValue}</td><td>{$ageMonths}</td>";

							$lastCurrent = 0;
							for ($y = $startyear; $y <= $endyear; $y++)
							{
								$current_value = $DB->assetCurrentValue($asset->idasset, $acct_date->finacialYear("{$y}-01-01") [1] );
								$strV = LedgerAmount::format1($current_value);
								echo "<td class='r'>{$strV}</td>";
								$lastCurrent = $current_value;
							}
							$tot2 += $lastCurrent;
							echo "</tr>";
						}
						$strTot1 = LedgerAmount::format1($tot1);
						$strTot2 = LedgerAmount::format1($tot2);

						echo "<tr><td class='gap'></td></tr>";
						echo "<tr><td class='b'>TOTAL</td><td></td><td></td><td></td><td class='r b'>{$strTot1}</td><td></td><td class='r b'>{$strTot2}</td></tr>";

						echo "</table>";

					} else
						echo "<p>NO ASSETS</p>";
					?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>