<?php
class IRFFiling
{
	private $_employer_ird_num;

	private $_date;

	private $_employee_name;

	private $_employee_phone;

	private $_employee_email;

	private $_allEmployees;

	function __construct()
	{

	}

	private function sum($a,$f)
	{
		$sum = 0;
		foreach($a as $e)
			$sum += $e[$f];
		return $sum;
	}


	private function formatCents($v)
	{
		if ($v == 0)
			$v = "000";
		else {
			if ($v < 100)
				$v = sprintf("%03d", $v);
			else
				$v = sprintf("%d", $v);
		}
		return $v;
	}

	private function formatDollarsToCents($v)
	{
		$v = intval(round(floatVal($v) * 100, 0));
		return $this->formatCents($v);
	}

	public function addEmployer($irdnum,$date,$name,$phone,$email)
	{
		$irdnum = intval(str_replace("-", "", trim($irdnum)));

		$this->_employer_ird_num = $irdnum;
		if (!is_object($date))
			$date = new DateTime($date);
		$this->_date = clone $date;
		$this->_employee_name = trim($name);
		$this->_employee_phone = trim($phone);
		$this->_employee_email = trim($email);
	}

	public function addEmployeePay($irdnum,$gross,$paye,$kiwisaver_employee,$kiwisaver_employer,$esct,$name,$taxcode,$startdate,$paycadence,$paystart,$payend,$hours)
	{
		$rec = array();
		$irdnum = intval(str_replace("-", "", trim($irdnum)));
		$rec["irdnum"] = sprintf("%09d", $irdnum);
		$rec["gross"] = intval(round(floatVal($gross) * 100.0,0));

		error_log("inernal gross {$rec["gross"]}");

		$rec["paye"] = intval(round(floatVal($paye) * 100,0));

		$rec["kiwisaver_employee"] = $this->formatDollarsToCents($kiwisaver_employee);
		$rec["kiwisaver_employer"] = $this->formatDollarsToCents($kiwisaver_employer);
		$rec["kiwisaver_esct"] = $this->formatDollarsToCents($esct);

		//$rec["kiwisaver_employee"] = ($kiwisaver_employee) == 0 ? "000" : intval(round(floatVal($kiwisaver_employee) * 100,0));
		//$rec["kiwisaver_employer"] = ($kiwisaver_employer) == 0 ? "000" : intval(round(floatVal($kiwisaver_employer) * 100,0));
		//$rec["kiwisaver_esct"] = ($esct) == 0 ? "000" : intval(round(floatVal($esct) * 100,0));


		$rec["name"] = trim($name);
		$rec["taxcode"] = trim($taxcode);
		$rec["taxcode"] = str_replace(" ", "", $rec["taxcode"]);
		$rec["taxcode"] = str_replace("-", "", $rec["taxcode"]);
		$rec["startdate"] = (new DateTime($startdate))->format("Ymd");
		$rec["pay_cadence"] = $paycadence;
		$rec["paystart"] = (new DateTime($paystart))->format("Ymd");
		$rec["payend"] = (new DateTime($payend))->format("Ymd");
		//Hours are rounded up.
		if ($hours >= 100.0)
			$rec["hours"] = sprintf("%05d",round($hours * 100,0));
		else
			$rec["hours"] = sprintf("%04d", round($hours * 100, 0));

		$this->_allEmployees[] = $rec;
	}
	public function createExtract($filename)
	{
		$f = fopen($filename, "w");
		$tot = 0;


		$str = "HEI2,";
		$str .= sprintf("%09d", $this->_employer_ird_num);
		$str .= ",";
		$str .= $this->_date->format("Ymd");
		$str .= ",";
		$str .= "N"; //Final employment
		$str .= ",";
		$str .= "N"; //Nil return
		$str .= ",";
		$str .= ",";
		$str .= substr($this->_employee_name,0,20);
		$str .= ",";
		$str .= substr($this->_employee_phone, 0, 12);
		$str .= ",";
		//$email = str_replace(".", "", $this->_employee_email);
		$email = $this->_employee_email;
		$str .= substr($email, 0, 60);
		$str .= ",";
		$str .= sprintf("%d",count($this->_allEmployees));
		$str .= ",";

		$v = 0;
		$str .= sprintf("%d",$this->sum($this->_allEmployees,"gross"));
		$str .= ",";
		$str .= "0";
		$str .= ",";
		$str .= "000";
		$str .= ",";

		$v = $this->sum($this->_allEmployees,"paye");
		$str .= sprintf("%d",$v);
		$tot += $v;

		$str .= ",";
		$str .= "0"; //Prior period PAYE adjustments
		$str .= ",";
		$str .= "000"; //Child support payments
		$str .= ",";
		$str .= "000"; //SL Replayment
		$str .= ",";
		$str .= "0"; //Total SLCIR deductions.
		$str .= ",";
		$str .= "0"; //Total SLBOR deductions.
		$str .= ",";
		$v = $this->sum($this->_allEmployees,"kiwisaver_employee");
		$str .= $this->formatCents($v);
		$tot += $v;

		$str .= ",";
		$v = $this->sum($this->_allEmployees,"kiwisaver_employer");
		$str .= $this->formatCents($v);
		$tot += $v;

		$str .= ",";
		$v = $this->sum($this->_allEmployees,"kiwisaver_esct");
		$str .= $this->formatCents($v);
		$tot += $v;

		$str .= ",";
		$str .= $this->formatCents($tot);

		$str .= ",";
		$str .= "000";

		$str .= ",";
		$str .= "0";

		$str .= ",";
		$str .= "0";

		$str .= ",";
		$str .= "staTera_v1_2";

		$str .= ",";
		$str .= "0001";

		$str .= "\r\n";

		fwrite($f,$str);

		//Records
		foreach($this->_allEmployees as $e)
		{
			$str = "DEI";
			$str .= ",";
			$str .= $e["irdnum"];
			$str .= ",";
			$str .= $e["name"];
			$str .= ",";
			$str .= $e["taxcode"];
			$str .= ",";
			$str .= $e["startdate"];
			$str .= ",";
			$str .= ",";
			$str .= $e["paystart"];
			$str .= ",";
			$str .= $e["payend"];
			$str .= ",";
			switch ($e["pay_cadence"])
			{
				case 'weekly':
					$str .= "WK";
					break;
				case 'fortnightly':
					$str .= "FT";
					break;
				case 'monthly':
					$str .= "MT";
					break;
				case 'adhoc':
					$str .= "AH";
					break;
				default:
					$str .= "AH";
					break;
			}
			$str .= ",";
			$str .= $e["hours"];
			$str .= ",";
			$str .= $e["gross"];
			$str .= ",";
			$str .= "0"; //Adjustments
			$str .= ",";
			$str .= "000"; //Acc not liable
			$str .= ",";
			$str .= "0"; //Lump sum
			$str .= ",";
			$str .= $e["paye"];
			$str .= ",";
			$str .= "0"; //Adjustments
			$str .= ",";
			$str .= "000"; //Child support
			$str .= ",";
			$str .= ",";
			$str .= "000"; //Student loan deductions
			$str .= ",";
			$str .= "0";  //Student loan commisioner
			$str .= ",";
			$str .= "0"; //Student loan voluentary
			$str .= ",";
			$str .= $e["kiwisaver_employee"];
			$str .= ",";
			$str .= $e["kiwisaver_employer"];
			$str .= ",";
			$str .= $e["kiwisaver_esct"];
			$str .= ",";
			$str .= "000"; //Tax credits
			$str .= ",";
			$str .= "0"; //Family tax credits
			$str .= ",";
			$str .= "0"; //ESS

			$str .= "\r\n";
			fwrite($f, $str);
		}

		fclose($f);
	}
}
?>