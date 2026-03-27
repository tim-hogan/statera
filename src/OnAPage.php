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

$plot = false;
$lines = array();

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (!$session->checkCSRF())
	{
		$DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
		header("Location: SecurityError.php");
		exit();
	}

	if (isset($_POST["cashflow"]))
	{
		$full_date_cope = $DB->getJournalStartAndEndDates();
		$a = $DB->cashFlow($full_date_cope[0], $full_date_cope[1]);
		$data = $a["data"];
		$range = $a["range"];

		var_error_log($full_date_cope, "full_date_cope");
		var_error_log($data, "data");
		var_error_log($range, "range");

		$sales = $data["cr"] ["income"] ["sale"];
		$operating = $data["dr"] ["expense"] ["operating"];
		$cos = $data["dr"] ["expense"] ["cost of sale"];
		$cnt = 0;
		foreach($range as
			$key => $text)
		{
			$ssales = 0.0;
			$sexpenses = 0.0;
			if (isset($sales[$key]))
				$ssales = $sales[$key];
			if (isset($operating[$key]))
				$sexpenses = $operating[$key];
			if (isset($cos[$key]))
				$sexpenses += $cos[$key];

			$lines[] = [substr($key,0,4), substr($key,4,2), $ssales, -($sexpenses)];
			$cnt++;
		}
		$plot = true;
	}

	if (isset($_POST[""]))
	{

	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>ON A PAGE</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#selectoptions {padding: 5px;}
		#graphouter {padding: 5px;}
		#graphinner {background-color: #888;}
	</style>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script src="/js/st.js"></script>
	<script>
		google.charts.load('current', { 'packages': ['corechart'] });
		var chart = null;
														<?php
		if ($plot)
		{
			echo "var data_cashflow = [['Month','Income','Expenditure','Net'],";
			foreach($lines as $line)
			{
				$net = $line[2] - $line[3];
				$month = $line[1] -  1;
				echo "[new Date({$line[0]},{$month},1),{$line[2]},{$line[3]},{$net}],";
			}
			echo "];";
		}
		?>

		function drawChart(d) {
			var data = google.visualization.arrayToDataTable(d);
			var options = {
				colors: ['orange', 'blue', 'yellow'],
				curveType: 'function',
				//height: 600,
				//width: 1100,
				//series: ser,
				backgroundColor: '#000000',
				titleTextStyle: { color: '#b0b0ff' },
				vAxis: { textStyle: { color: '#ffe000' } },
				hAxis: {
					textStyle: { color: '#ffe000' },
					format: 'MMM y',
					gridlines: { count: 4, units: {months: ['MMM Y'] }}
				},
				legend: { position: 'bottom',textStyle: {color: '#e0e0e0', fontSize: 12} }
			}
			chart = new google.visualization.LineChart(st.ge('graphinner'));
			chart.draw(data, options);
			console.log("Draw");
		}

		function divRect(n) {
			let a = st.ge(n);
			if (a) {
				return { w: a.offsetWidth, h: a.offsetHeight };
			}
			return null;
		}

		function sizeit() {
			let wRect = { w: window.innerWidth, h: window.innerHeight};
			let containRect = divRect("container");
			let headingRect = divRect("heading");
			let menuRect = divRect("menu");
			let graphouterRect = divRect("graphouter");
			let graphInnerRect = divRect("graphinner");
			let availheight = containRect.h - (headingRect.h + menuRect.h);
			let g = st.ge("graphinner");
			g.style.width = (graphInnerRect.w) + "px";
			g.style.height =  (availheight - 32) + "px";

		}

		function loadchart() {
			google.charts.load('current', { 'packages': ['corechart'] });
		}
		function start() {
			sizeit();
			<?php
			if ($plot)
			{
				//echo "loadchart();";
				echo "drawChart(data_cashflow);";
			}
			?>
		}
	</script>
</head>
<body onload="start()">
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
			<div id="selectoptions">
				<div id="form">
					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
						<button name="cashflow">CASH FLOW</button>
						<button name="revenue">REVENUE</button>
						<?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
					</form>
				</div>
			</div>
			<div id="graphouter">
				<div id="graphinner">

				</div>
			</div>
		</div>
	</div>
</body>
</html>