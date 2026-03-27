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
$errmsg = "";
$staffid = 0;

$strDate = classTimeHelpers::timeFormat(new DateTime(), "Y-m-d", $user->user_timezone->raw());
$strMobDate = classTimeHelpers::timeFormat(new DateTime(), "D jS M", $user->user_timezone->raw());
$strMobDate = strtoupper($strMobDate);

$formfields = [];

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	var_error_log($_POST, "_POST");

	if (isset($_POST["staffchange"]))
	{
		$s = Secure::sec_decryptParamPart(urldecode(FormList::getField("staff")), base64_encode($session->session_key));
		if ($s)
		{
			parse_str($s, $a);
			$staffid = intval($a["s"]);
		}       
	}

	if (isset($_POST["submit"]))
	{
		$s = Secure::sec_decryptParamPart(urldecode(FormList::getField("staff")), base64_encode($session->session_key));
		if ($s)
		{
			parse_str($s, $a);
			$staffid = intval($a["s"]);
		
			error_log("Staff id = {$staffid}");

			if (!$staffid || $staffid == 0)
				$errmsg = "ERROR: Invalid staff selected";
			$date = FormList::getDateField("date");
			//Check date is valid
			try {
				$dt = new DateTime($date);
			} 
			catch (Exception $e) {
				$date = null;
				$errmsg = "ERROR: Invalid date entered";
			}

			$hours = FormList::getDecimalField("hours");


			if (strlen($errmsg) == 0) 
			{
				if ($hours <= 0)
				{
					$DB->deleteTimesheetEntry($staffid, $date);
					$DB->createAudit("timesheet", "Timesheet entry deleted for staff {$staffid} date {$date}", $user->iduser);

				}
				else
				{
					$DB->createUpdateTimeEntry($staffid, $date, $hours, $user->iduser);
					$DB->createAudit("timesheet", "Timesheet entry for staff {$staffid} date {$date} hours {$hours}", $user->iduser);
				}
			}
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>TIME SHEET</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		body {background-color: black;}
		#container {display: none;}
		#mobilecontainer {display: block; background-color: black;}
		table.dateselector {width: 100%;}
		#mobform td {font-size: 18pt;}
		#mobform td.td1 {color: white; text-align: left;padding-left: 10px; font-size: 24pt;}
		#mobform td.td2 {color: lightseagreen; text-align: center;font-size: 24pt;}
		#mobform td.td3 {color: white; text-align: right;padding-right: 10px;font-size: 24pt;}
		#mobheading  {text-align: center;}
		#mobheading h1 {color: forestgreen;}
		#mobform div.formfieldsel{text-align: center;margin-top: 28px;}
		#mobform select {font-size: 16pt;}
		#mobform div.formfieldhours {margin-top: 28px;text-align: center;}
		#mobform div.formfieldhours input {font-size: 24pt; font-weight: bold; color: #10b7f4; text-align: center;width: 40px; height: 40px;}
		#mobform label {color: yellow;display: block;}
		#mobhours {font-size: 24pt;width: 60px; height: 60px;text-align: center;}
		#mobbutton {margin-top: 16px;text-align: center;}
		#mobbutton button {margin-top: 36px;font-size: 16pt;}
		#msg {margin-left: 12px;margin-right: 12px; font-size: 14pt; color: red;}
		.r {text-align: right;}
		#list {padding: 20px; border: solid 1px #888; border-radius: 8px;margin-bottom: 50px;}
		#list h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
		#list th,td {padding-right: 20px;}
		#moblist {margin: 10px;}
		#moblist h1 {color: #eee;font-size: 14pt;}
		#moblist th,td {color: #888;padding-right: 12px;}
		@media only screen and (min-width: 640px) {
			body {background-color: initial}
			#container {display: block;}
			#mobilecontainer {display: none;}
			#main {margin: 20px;}
			#msg p.err {color: red;font-weight: bold;font-size: 14pt;}
			#genheading h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
			#form {padding: 20px; border: solid 1px #888; border-radius: 8px;margin-bottom: 50px;}
			#form div.formfield {margin-bottom: 16px;}
			#form input {font-size: 14pt;}
			#form select {font-size: 14pt;}
			button {font-size: 14pt;}
		}
	</style>
	<script src="/js/st.js"></script>	
	<script>
		var g_days = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
		var g_months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG","SEP", "OCT","NOV","DEC"];
		function moveDay(v) {
			let amnt = v * 24 * 3600 * 1000;
			let d = st.ge("amobdate");
			let dv = st.ga(d,"_v");
			let adate = new Date(dv);
			adate.setTime(adate.getTime() + amnt);
			let strDate = adate.getFullYear() + "-" + st.pad((adate.getMonth()+1), 2) + "-" + st.pad(adate.getDate(), 2);
			st.ge("mobdate").value = strDate;
			st.sa(d,"_v",strDate);
			d.innerHTML = g_days[adate.getDay()] + " " + adate.getDate() + " " + g_months[adate.getMonth()];
		}
		function prevDay() {
			moveDay(-1);
		}

		function nextDay() {
			moveDay(1);
		}

		function staffChange(n) {
			let form = st.ce("FORM");
			document.body.appendChild(form);
			form.method = "POST";
			form.action = "<?php echo $selff;?>";
			st.ci("staffchange", "1", form);
			st.ci("staff", n.value, form);
			form.submit();
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
			<div id="genheading">
				<h1>ENTER TIMESHEET</h1>
			</div>
			<div id="form">
				<form id="mainform" method="post" enctype='multipart/form-data' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div class="formfield">
						<label for="date" class="first">DATE</label>
						<input id="date" type="date" name="date" value="<?php echo $strDate;?>" />
					</div>
					<div class="formfield">
						<select name="staff" onchange="staffChange(this)">
							<?php
							$v = "s=0";
							$s = Secure::sec_encryptParam($v, base64_encode($session->session_key));
							echo "<option value='{$s}'>[SELECT STAFF MEMBER]</option>";

							$o_staff_list = $DB->o_everyStaffOnTimesheet();
							foreach($o_staff_list as $staff)
							{
								$v = "s={$staff->idstaff}";
								$s = Secure::sec_encryptParam($v, base64_encode($session->session_key));
								$selected = "";
								if ($staff->idstaff == $staffid)
									$selected = "selected";
								echo "<option value='{$s}' {$selected}>{$staff->staff_name->toHTML()}</option>";
							}
							?>
						</select>
					</div>
					<div class="formfield">
						<label for="hours">HOURS WORKED</label>
						<input id="hours" name="hours" class="r" type="text" size="4" value="0"/>
					</div>
					<button name="submit" value="submit">SUBMIT</button>
			   </form>
			 </div>
			<div id="list">
			<?php
			if ($staffid != 0)
			{
                $dt = new DateTime();
                $dt->setTimezone(new DateTimeZone($user->user_timezone->raw()));
                $year = intval($dt->format("Y"));
                $month = sprintf("%02d", intval($dt->format("m")));

                $start = new DateTime("{$year}-{$month}-01 00:00:00");
                $end = clone ($start);
                $end->add(new DateInterval("P2M"));
                $end->sub(new DateInterval("P1D"));
                $start->sub(new DateInterval("P1M"));


                $staff = $DB->o_getStaff($staffid);
                $entries = $DB->everyTimeSheetForStaff($staffid, $start->format("Y-m-d"), $end->format("Y-m-d"));

                echo "<h1>Timesheet entries for {$staff->staff_name->toHTML()}</h1>";
				echo "<table>";
				echo "<tr><th>DATE</th><th>HOURS</th>";
				
				if ($staff->staff_type == "contractor")
					echo "<th></th>";
				else
					echo "<th>WAGES PROCESSED</th>";
				echo "</tr>";
				
				foreach($entries as $e)
				{
					echo "<tr>";
					$ent_date = new DateTime($e->timesheet_date);
					echo "<td>{$ent_date->format("D jS M")}</td>";
					$hours = sprintf("%4.1f", $e->timesheet_hours);
					echo "<td class='r'>{$hours}</td>";
					if ($e->timesheet_processed && $staff->staff_type != "contractor")
						echo "<td class='r'>YES</td>";
					else
						echo "<td></td>";

					echo "</tr>";
				}
				echo "</table>";
			}
			?>
			</div>
		</div>
	</div>
	<div id="mobilecontainer">
		<div id="mobheading">
			<h1>TIME SHEET ENTRY</h1>
		</div>
		<div id="mobmain">
			<?php
			if (strlen($errmsg) > 0)
			{
				echo "<div id='msg'><p class='err'>{$errmsg}</p></div>";
			}
			?>
			<div id="mobform">
				<form method="post" enctype='multipart/form-data' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
					<div id="dater">
						<table class="dateselector">
							<tr><td class="td1" onclick="prevDay()"><</td><td id="amobdate" class="td2" _v="<?php echo $strDate;?>" ><?php echo $strMobDate;?></td><td class="td3" onclick="nextDay()">></td></tr>
						</table>
					</div>
					<div class="formfieldsel">
					<select name="staff" onchange="staffChange(this)">
							<?php
							$v = "s=0";
							$s = Secure::sec_encryptParam($v, base64_encode($session->session_key));
							echo "<option value='{$s}'>[SELECT STAFF MEMBER]</option>";
							$o_staff_list = $DB->o_everyStaffOnTimesheet();
							foreach($o_staff_list as $staff)
							{
								$v = "s={$staff->idstaff}";
								$s = Secure::sec_encryptParam($v, base64_encode($session->session_key));
								$selected = "";
								if ($staff->idstaff == $staffid)
									$selected = "selected";
								echo "<option value='{$s}' {$selected}>{$staff->staff_name->toHTML()}</option>";
							}
							?>
					</select>
					</div>
					<div class="formfieldhours">
						<label for="mobhours">HOURS WORKED</label>
						<input id="mobhours" name="hours" type="text" value="0"/>
					</div>
					<div id="mobbutton">
						<button name="submit">SUBMIT</button>
					</div>
					<input type="hidden" id="mobdate" name="date" value="<?php echo $strDate; ?>" />
				</form>
			</div>
		</div>
		<div id="moblist">
			<?php
			if ($staffid != 0)
			{
				$dt = new DateTime();
				$dt->setTimezone(new DateTimeZone($user->user_timezone->raw()));
				$year = intval($dt->format("Y"));
				$month = sprintf("%02d",intval($dt->format("m")));

				$start = new DateTime("{$year}-{$month}-01 00:00:00");
				$end = clone ($start);
				$end->add(new DateInterval("P2M"));
				$end->sub(new DateInterval("P1D"));
				$start->sub(new DateInterval("P1M"));


				$staff = $DB->o_getStaff($staffid);
				$entries = $DB->everyTimeSheetForStaff($staffid, $start->format("Y-m-d"), $end->format("Y-m-d"));

				echo "<h1>Timesheet entries for {$staff->staff_name->toHTML()}</h1>";
				echo "<table>";
				echo "<tr><th>DATE</th><th>HOURS</th>";
				error_log("Staff tpye = {$staff->staff_type}");
				if ($staff->staff_type == "contractor")
					echo "<th></th>";
				else
					echo "<th>WAGES PROCESSED</th>";
				echo "</tr>";
				
				foreach($entries as $e)
				{
                    echo "<tr>";
                    $ent_date = new DateTime($e->timesheet_date);
                    echo "<td>{$ent_date->format("D jS M")}</td>";
                    $hours = trim(sprintf("%4.1f", $e->timesheet_hours));
					
                    echo "<td class='r'>{$hours}</td>";
                    if ($e->timesheet_processed && $staff->staff_type != 'contractor')
                        echo "<td class='r'>YES</td>";
                    else
                        echo "<td></td>";

                    echo "</tr>";
				}
				echo "</table>";
			}
			?>
		</div>
	</div>
</body>
</html>