<?php
use Vtiful\Kernel\Format;
//devt.Version = 1.0
require_once dirname(__FILE__) . '/classSQLPlus2.php';
require_once dirname(__FILE__) . '/classAccounts.php';

define ('SEARCH_ONEONLY',1);
define ('SEARCH_FIRST',2);

class glb extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"global_default_homepage" =>["type" => "varchar"],
					"global_default_domainname" =>["type" => "varchar"],
					"global_password_min_length" => ["type" => "int"],
					"global_password_min_num" =>["type" => "int"],
					"global_password_min_upper" =>["type" => "int"],
					"global_password_min_lower" =>["type" => "int"],
					"global_password_min_special" =>["type" => "int"],
					"global_password_renew_days" =>["type" => "int"],
					"global_password_no_renew_within_hours" =>["type" => "int"],
					"global_password_maxattempts" =>["type" => "int"],
					"global_undolist_max_depth" =>["type" => "int"],
					"global_undolist_max_age" =>["type" => "int"]
				]
			);
	}

}

class asset extends TableRow
{
	function __construct($tabledata = null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idasset" => ["type" => "int", "pk" => true],
					"asset_name" => ["type" => "varchar"],
					"asset_purhcase_date" => ["type" => "date"],
					"asset_depreciation_method" => ["type" => "enum"],
					"asset_depreciation_rate" => ["type" => "double"]
				]
			);
	}
}

class attachment extends TableRow
{
	function __construct($tabledata = null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idattachment" => ["type" => "int" , "pk"=> true],
					"attachment_deleted" => ["type" => "boolean"],
					"attachment_group" => ["type" => "int"],
					"attachment_filename" => ["type" => "varchar"],
					"attachment_original_name" => ["type" => "varchar"]
				]
			);
	}
}

class attachment_group extends TableRow
{
	function __construct($tabledata = null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idattachment_group" => ["type" => "int", "pk" => true],
					"attachment_group_timestamp" => ["type" => "datetime"],
					"attachment_group_type" => ["type" => "varchar"],
					"attachment_group_description" => ["type" => "varchar"]
				]
			);
	}
}

class audit extends TableRow
{
	function __construct($tabledata = null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idaudit" => ["type" => "int", "pk" => true],
					"audit_timestamp" => ["type" => "datetime"],
					"audit_type" => ["type" => "varchar"],
					"audit_description" => ["type" => "varchar"],
					"audit_user" => ["type" => "int"],
					"user_firstname" => ["type" => "varchar"],
					"user_lastname" => ["type" => "varchar"]
				]
			);
	}
}

class company extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"company_name" =>["type" => "varchar"],
					"company_address1" =>["type" => "varchar"],
					"company_address2" =>["type" => "varchar"],
					"company_address3" =>["type" => "varchar"],
					"company_address4" =>["type" => "varchar"],
					"company_city" =>["type" => "varchar"],
					"company_postcode" =>["type" => "varchar"],
					"company_country" =>["type" => "varchar"],
					"company_country_prefix" =>["type" => "varchar"],
					"company_email" =>["type" => "varchar"],
					"company_phone" =>["type" => "varchar"],
					"company_start_date"=>["type" => "date"],
					"company_tax_number" =>["type" => "varchar"],
					"company_bank_acct_name" =>["type" => "varchar"],
					"company_bank_acct_number" =>["type" => "varchar"],
					"company_sales_tax_name" =>["type" => "varchar"],
					"company_sales_tax_cadence" => ["type" => "int"],
					"company_sales_tax_first_month"=>["type" => "int"],
					"company_financialyear_start_month" =>["type" => "int"],
					"company_style_theme" => ["type" => "varchar"]
				]
			);
	}
}

class user extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"iduser" =>["type" => "int"],
					"user_deleted" =>["type" => "boolean"],
					"user_randid" => ["type" => "varchar"],
					"user_username" =>["type" => "varchar"],
					"user_lastname" =>["type" => "varchar"],
					"user_firstname" =>["type" => "varchar"],
					"user_email" =>["type" => "varchar"],
					"user_phone" => ["type" => "varchar"],
					"user_forcereset" =>["type" => "boolean"],
					"user_hash" =>["type" => "varchar"],
					"user_salt" =>["type" => "varchar"],
					"user_createtime" =>["type" => "datetime"],
					"user_security" =>["type" => "int"],
					"user_last_signin" =>["type" => "datetime"],
					"user_expires" =>["type" => "datetime"],
					"user_prev_hash" =>["type" => "varchar"],
					"user_prev_salt" =>["type" => "varchar"],
					"user_disabled" =>["type" => "boolean"],
					"user_timezone" =>["type" => "varchar"],
					"user_failed_signin_count" =>["type" => "int"],
					"user_pw_renew_date" =>["type" => "datetime"],
					"user_pw_change_date" =>["type" => "datetime"],
					"user_x509_serial" =>["type" => "varchar"],
					"user_apikey" =>["type" => "varchar"],
					"user_remember_me" => ["type" => "boolean"],
					"user_session_key" => ["type" => "varchar"],
					"user_session_data" => ["type" => "varchar"],
					"user_undolist" => ["type" => "varchar"],
					"user_notify_quotes" => ["type" => "boolean"]
				]
			);
	}
}

class taxbracket extends TableRow
{
	function __construct($tabledata = null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idtaxbracket" =>["type" => "int","pk" => true],
					"taxbracket_deleted" =>["type" => "boolean"],
					"taxbracket_from_date" =>["type" => "date"],
					"taxbracket_amount" =>["type" => "double"],
					"taxbracket_percent" => ["type" => "double"],
					"taxbracket_product" => ["type" => "double"]
				]
			);
	}
}

class taxclass extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idtaxclass" =>["type" => "int"],
					"taxclass_name" =>["type" => "varchar"],
					"taxclass_description" =>["type" => "varchar"],
					"taxclass_invoice_text" =>["type" => "varchar"]
				]
			);
	}
}

class taxrate extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idtaxrate" =>["type" => "int"],
					"taxrate_taxclass" =>["type" => "int"],
					"taxrate_from_date" =>["type" => "date"],
					"taxrate_rate" =>["type" => "double"],
					"taxrate_comments"=>["type" => "varchar"],
					"idtaxclass" => ["type" => "int"],
					"taxclass_name" => ["type" => "varchar"],
					"taxclass_description" => ["type" => "varchar"],
					"taxclass_invoice_text" => ["type" => "varchar"]
				]
			);
	}
}

class chart extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"chart_code" =>["type" => "int"],
					"chart_deleted" =>["type" => "boolean"],
					"chart_type" =>["type" => "varchar"],
					"chart_type_name" =>["type" => "varchar"],
					"chart_subtype"=>["type" => "varchar"],
					"chart_subsubtype"=>["type" => "varchar"],
					"chart_description"=>["type" => "varchar"],
					"chart_taxclass"=>["type" => "int"],
					"chart_description_cr"=>["type" => "varchar"],
					"chart_description_dr"=>["type" => "varchar"],
					"chart_balancesheet"=>["type" => "varchar"],
					"chart_balancesheet_subtype"=>["type" => "varchar"],
				]
			);
	}
}

class product extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idproduct" =>["type" => "int"],
					"product_deleted"=>["type" => "boolean"],
					"product_order" =>["type" => "int"],
					"product_description" =>["type" => "varchar"],
					"product_type" =>["type" => "varchar"],
					"product_unit_cost"=>["type" => "double"],
					"product_unit_text"=>["type" => "varchar"]
				]
			);
	}
}

class quote extends TableRow
{
	function __construct($tabledata = null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idquote" => ["type" => "int", "pk" => true],
					"quote_deleted" => ["type" => "boolean"],
					"quote_number" => ["type" => "int"],
					"quote_date" => ["type" => "date"],
					"quote_customer_account" => ["type" => "date"],
					"quote_contact_name" => ["type" => "varchar"],
					"quote_contact_phone" => ["type" => "varchar"],
					"quote_contact_email" => ["type" => "varchar"],
					"quote_status" => ["type" => "varchar"],
					"quote_accept_date" => ["type" => "date"],
					"quote_value_net" => ["type" => "decimal"],
					"quote_value_tax" => ["type" => "decimal"],
					"quote_value_gross" => ["type" => "decimal"],
					"quote_address1" => ["type" => "varchar"],
					"quote_address2" => ["type" => "varchar"],
					"quote_address3" => ["type" => "varchar"],
					"quote_address4" => ["type" => "varchar"],
					"quote_city" => ["type" => "varchar"],
					"quote_option_no_item_cost" => ["type" => "boolean"],
					"quote_accepted_timestamp" => ["type" => "datetime"],
					"quote_declined_timestamp" => ["type" => "datetime"],
					"quote_completed_timestamp" => ["type" => "datetime"]
				]
			);
	}

}

class quote_line extends TableRow
{
	function __construct($tabledata = null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idquote_line" => ["type" => "int", "pk" => true],
					"quote_line_quote" => ["type" => "int"],
					"quote_line_item" => ["type" => "int"],
					"quote_line_descripton" => ["type" => "varchar"],
					"quote_line_qty" => ["type" => "decimal"],
					"quote_line_cost" => ["type" => "decimal"]
				]
			);
	}

}

class quote_request extends TableRow
{
	function __construct($tabledata = null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idquote_request" => ["type" => "int", "pk" => true],
					"quote_request_deleted" => ["type" => "boolean"],
					"quote_request_date" => ["type" => "date"],
					"quote_request_name" => ["type" => "varchar"],
					"quote_request_phone" => ["type" => "varchar"],
					"quote_request_email" => ["type" => "varchar"],
					"quote_request_addreess1" => ["type" => "varchar"],
					"quote_request_addreess2" => ["type" => "varchar"],
					"quote_request_addreess3" => ["type" => "varchar"],
					"quote_request_addreess4" => ["type" => "varchar"],
					"quote_request_addreess5" => ["type" => "varchar"],
					"quote_request_comment" => ["type" => "varchar"],
				]
			);
	}

}

class account extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idaccount" =>["type" => "int", "pk" => true],
					"account_deleted"=>["type" => "boolean"],
					"account_name" =>["type" => "varchar"],
					"account_email" =>["type" => "varchar"],
					"account_phone" =>["type" => "varchar"],
					"account_address1"=>["type" => "varchar"],
					"account_address2"=>["type" => "varchar"],
					"account_address3"=>["type" => "varchar"],
					"account_address4"=>["type" => "varchar"],
					"account_city"=>["type" => "varchar"],
					"account_postcode"=>["type" => "varchar"],
					"account_state"=>["type" => "varchar"],
					"account_country"=>["type" => "varchar"],
					"account_contact_accounts"=>["type" => "varchar"],
					"account_sale_tax_class"=>["type" => "int"],
					"taxclass_name" =>["type" => "varchar"]
				]
			);
	}
}

class invoice extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idinvoice" =>["type" => "int"],
					"invoice_deleted"=>["type" => "boolean"],
					"invoice_date" => ["type" => "datetime"],
					"invoice_number" =>["type" => "int"],
					"invoice_account" =>["type" => "int"],
					"invoice_cash_sale" =>["type" => "boolean"],
					"invoice_company_name" =>["type" => "varchar"],
					"invoice_company_address1"=>["type" => "varchar"],
					"invoice_company_address2"=>["type" => "varchar"],
					"invoice_company_address3"=>["type" => "varchar"],
					"invoice_company_address4"=>["type" => "varchar"],
					"invoice_company_city"=>["type" => "varchar"],
					"invoice_company_postcode"=>["type" => "varchar"],
					"invoice_company_country"=>["type" => "varchar"],
					"invoice_sale_tax_class" =>["type" => "int"],
					"invoice_sale_tax_name"=>["type" => "varchar"],
					"invoice_tax_number"=>["type" => "varchar"],
					"invoice_bank_acct_name"=>["type" => "varchar"],
					"invoice_bank_acct_number"=>["type" => "varchar"],
					"invoice_account_name"=>["type" => "varchar"],
					"invoice_account_address1"=>["type" => "varchar"],
					"invoice_account_address2"=>["type" => "varchar"],
					"invoice_account_address3"=>["type" => "varchar"],
					"invoice_account_address4"=>["type" => "varchar"],
					"invoice_account_city"=>["type" => "varchar"],
					"invoice_account_state"=>["type" => "varchar"],
					"invoice_account_postcode"=>["type" => "varchar"],
					"invoice_account_country"=>["type" => "varchar"],
					"invoice_account_ref1"=>["type" => "varchar"],
					"invoice_account_ref2"=>["type" => "varchar"]
				]
			);
	}
}

class invoice_line extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idinvoice_line" =>["type" => "int"],
					"invoice_line_invoice"=>["type" => "int"],
					"invoice_line_product" => ["type" => "int"],
					"invoice_line_description" =>["type" => "varchar"],
					"invoice_line_qty" =>["type" => "double"],
					"invoice_line_unit_cost" =>["type" => "double"],
					"invoice_line_net_cost"=>["type" => "double"],
					"invoice_line_tax_cost"=>["type" => "double"],
					"invoice_line_gross_cost"=>["type" => "double"]
				]
			);
	}
}

class journal extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idjournal" =>["type" => "int"],
					"journal_xtn" =>["type" => "int"],
					"journal_date" =>["type" => "date"],
					"journal_chart" =>["type" => "int"],
					"journal_marker" =>["type" => "varchar"],
					"journal_description"=>["type" => "varchar"],
					"journal_net"=>["type" => "double"],
					"journal_tax"=>["type" => "double"],
					"journal_gross" =>["type" => "double"],
					"journal_wage_tax" => ["type" => "double"],
					"journal_tax_type" => ["type" => "varchar"],
					"journal_link"=>["type" => "int"],
					"journal_source"=>["type" => "int"],
					"journal_source_chart"=>["type" => "int"],
					"journal_folio"=>["type" => "int"],
					"journal_quote"=>["type" => "int"],
					"journal_account"=>["type" => "int"],
					"journal_invoice"=>["type" => "int"],
					"journal_asset"=>["type"  => "int"],
					"journal_attachment_group" => ["type" => "int"],
					"journal_tax_date"=>["type" => "date"],
					"journal_shareholder"=>["type" => "int"],
					"journal_vendor_name"=>["type" => "varchar"],
					"journal_vendor_tax_number" =>["type" => "varchar"],
					"journal_kiwisaver_employee" => ["type" => "double"],
					"journal_kiwisaver_employer" => ["type" => "double"],
					"journal_kiwisaver_esct_tax" => ["type" => "double"],
					"other_chart" => ["type" => "int"],
					"SUMGROSS" => ["type" => "double"],
					"chart_code" =>["type" => "int"],
					"chart_deleted" =>["type" => "boolean"],
					"chart_type" =>["type" => "varchar"],
					"chart_type_name" =>["type" => "varchar"],
					"chart_subtype"=>["type" => "varchar"],
					"chart_subsubtype"=>["type" => "varchar"],
					"chart_description"=>["type" => "varchar"],
					"chart_taxclass"=>["type" => "int"],
					"chart_description_cr"=>["type" => "varchar"],
					"chart_description_dr"=>["type" => "varchar"],
					"chart_balancesheet"=>["type" => "varchar"],
					"chart_balancesheet_subtype"=>["type" => "varchar"]
				]
			);
	}
}

class shareholder extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idshareholder" =>["type" => "int"],
					"shareholder_deleted" =>["type" => "boolean"],
					"shareholder_lastname" => ["type" => "varchar"],
					"shareholder_firstnames" => ["type" => "varchar"]
				]
			);
	}
}

class share extends TableRow
{
	function __construct($tabledata = null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idshare" => ["type" => "int"],
					"share_date" => ["type" => "date"],
					"share_qty" =>["type" => "int"],
					"share_buyprice" => ["type" => "double"],
					"share_shareholder" =>["type" => "int"]
				]
			);
	}
}

class timesheet extends TableRow
{
	function __construct($tabledata = null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idtimesheet" => ["type" => "int", "pk" => true],
					"timesheet_date" => ["type" => "date"],
					"timesheet_type" => ["type" => "enum"],
					"timesheet_staff" => ["type" => "int"],
					"timesheet_direct_gross" => ["type" => "double"],
					"timesheet_hours" => ["type" => "double"],
					"timesheet_processed" =>["type" => "boolean"],
					"timesheet_payxtn" => ["type" => "int"],
					"timesheet_paid_date" => ["type" => "date"],
					"timesheet_pay_cadence" => ["type" => "enum"],
					"timesheet_entry_timestamp" => ["type" => "datetime"],
					"timesheet_entry_user" => ["type" => "int"]
				]
			);
	}
}

class staff extends TableRow
{
	function __construct($tabledata=null)
	{
		if ($tabledata)
			parent::__construct($tabledata);
		else
			parent::__construct
			(
				[
					"idstaff" => ["type" => "int", "pk" => true],
					"staff_deleted" => ["type" => "boolean"],
					"staff_name" => ["type" => "varchar"],
					"staff_type" => ["type" => "enum"],
					"staff_email" => ["type" => "varchar"],
					"staff_phone" => ["type" => "varchar"],
					"staff_start_date" => ["type" => "date"],
					"staff_tax_number" => ["type" => "varchar"],
					"staff_tax_code" => ["type" => "varchar"],
					"staff_bank_acct_number" => ["type" => "varchar"],
					"staff_bank_acct_name" => ["type" => "varchar"],
					"staff_list_on_timesheet" => ["type" => "boolean"],
					"staff_hourly_rate1" => ["type" => "double"],
					"staff_hourly_rate2" => ["type" => "double"],
					"staff_hourly_rate3" => ["type" => "double"],
					"staff_add_holiday_pay" => ["type" => "boolean"],
					"staff_holiday_pay_rate" => ["type" => "double"],
					"staff_has_kiwi_saver" => ["type" => "boolean"],
					"staff_kiwi_save_employer_rate" => ["type" => "double"],
					"staff_kiwi_save_employee_rate" => ["type" => "double"],
					"staff_use_esct_tax" => ["type" => "boolean"],
					"staff_esct_tax_rate" => ["type" => "double"]
				]
			);
	}
}

class stateraDB extends SQLPlus
{
	function __construct($params)
	{
		parent::__construct($params);
	}


	private function var_error_log( $object=null ,$additionaltext='')
	{
		ob_start();
		var_dump( $object );
		$contents = ob_get_contents();
		ob_end_clean();
		error_log("{$additionaltext} {$contents}");
	}

	private function encrypt($data,$key)
	{
		if ($data && $key && strlen($data) > 0)
		{
			$data = "FFFF" . (string) $data;
			$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
			$encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
			$result = $encrypted . '::' . $iv;
			return base64_encode($result);
		}
		else
			return null;
	}

	private function decrypt($data,$key)
	{
		$data = base64_decode($data);
		if ($data && $key && strlen($data) > 0)
		{
			list($encrypted_data, $iv) = explode('::', $data, 2);
			if (strlen($iv) != 16)
			{
				error_log("Error in decrypt iv wrong length, backtrace follows");
				return null;
			}
			$de =  openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
			if (substr($de,0,4) == 'FFFF')
			{
				return substr($de,4);
			}
		}
		return null;
	}

	//*********************************************************************
	// global functions
	//*********************************************************************
	public function getGlobal()
	{
		return $this->o_singlequery("glb","select * from global limit 1",null,null);
	}

	//*********************************************************************
	// rolling
	//*********************************************************************
	public function getRollingByName($name)
	{
		return $this->p_singlequery("select * from rolling where rolling_entity = ?","s",$name);
	}

	public function createRolling($name,$modulus=10,$target=0.1)
	{
		return $this->p_create("insert into rolling (rolling_entity,rolling_modulus,rolling_target,rolling_idx,rolling_disable_seconds) values (?,?,?,0,3600)","sid",$name,$modulus,$target);
	}

	public function updateRolling($name, $count, $values)
	{
		return $this->p_update("update rolling set rolling_idx = ?, rolling_counters = ? where rolling_entity = ?", "iss", $count, $values, $name);
	}

	public function resetRolling($name)
	{
		return $this->p_update("update rolling set rolling_entity_disabled = 0 where rolling_entity = ?","s",$name);
	}

	public function markRollingDisabled($name)
	{
		$dt = new DateTime('now');
		$strTime = $dt->format('Y-m-d H:i:s');
		return $this->p_update("update rolling set rolling_entity_disabled = 1, rolling_disable_timestamp = ? where rolling_entity = ?","ss",$strTime,$name);
	}

	public function createRollingAudit($name,$rate)
	{
		$desc = "Security: Request rate too high for rolling marker {$name}, rate {$rate} per second, function disabled IP: {$_SERVER['REMOTE_ADDR']}";
		$this->createAudit("rolling",$desc);
	}


	//*********************************************************************
	// user functions
	//*********************************************************************
	public function getUser($id)
	{
		return $this->o_singlequery("user","select * from user where iduser = ?","i",$id);
	}

	public function getUserByUsername($username)
	{
		return $this->o_singlequery("user","select * from user where user_username = ? and user_deleted = 0","s",$username);
	}

	public function getUserByRandId($randid)
	{
		return $this->o_singlequery("user", "select * from user where user_randid = ?", "s", $randid);
	}

	public function createUser($username, $lastname, $firstname, $hash, $salt, $security, $timezone)
	{
		$randid = bin2hex(openssl_random_pseudo_bytes(8));
		$session_key = bin2hex(openssl_random_pseudo_bytes(32));
		return $this->p_create("insert into user (user_randid,user_username,user_lastname,user_firstname,user_forcereset,user_hash,user_salt,user_security,user_timezone,user_session_key) values ('{$randid}',?,?,?,1,?,?,?,?,'{$session_key}')","sssssis",$username,$lastname,$firstname,$hash,$salt,$security,$timezone);
	}

	public function createUserWithEmail($username,$lastname,$firstname,$hash,$salt,$security,$timezone,$email)
	{
		$randid = bin2hex(openssl_random_pseudo_bytes(8));
		$session_key = bin2hex(openssl_random_pseudo_bytes(32));
		return $this->p_create("insert into user (user_randid,user_username,user_lastname,user_firstname,user_forcereset,user_hash,user_salt,user_security,user_timezone,user_session_key,user_email) values ('{$randid}',?,?,?,1,?,?,?,?,'{$session_key}',?)","sssssiss",$username,$lastname,$firstname,$hash,$salt,$security,$timezone,$email);
	}

	public function updatePassword($userid,$hash,$salt,$force=false,$renewdays=0)
	{
		$dt = new DateTime('now');
		$strNow = $dt->format('Y-m-d H:i:s');
		$strRenew = '';
		$forceflag = 0;


		//Get the user record so we can update the previous password list
		if ($user = $this->getUser($userid) )
		{

			if (! $user->user_deleted && ! $user->user_disabled)
			{
				if ($renewdays > 0)
				{
					$dtRenew = new DateTime();
					$dtRenew->setTimestamp($dtRenew->getTimestamp() + (3600*24*$renewdays));
					$strRenew = $dtRenew->format('Y-m-d H:i:s');
				}

				$prevhash = '';
				$prevsalt = '';
				if ($user->user_prev_hash)
					$prevhash = $user->user_prev_hash->raw();
				if ($user->user_prev_salt)
					$prevsalt = $user->user_prev_salt->raw();
				$prevhash = substr($hash . $prevhash,0,640);
				$prevsalt = substr($salt . $prevsalt,0,640);
				if ($force)
				{
					$forceflag = 1;
					if ($renewdays > 0)
						return $this->p_update("update user set user_pw_renew_date = ?, user_pw_change_date = null, user_hash = ?, user_salt = ?, user_forcereset = ?, user_prev_hash = ?, user_prev_salt = ? where iduser = ?","sssissi",$strRenew,$hash,$salt,$forceflag,$prevhash,$prevsalt,$userid);
					else
						return $this->p_update("update user set user_pw_change_date = null, user_hash = ?, user_salt = ?, user_forcereset = ?, user_prev_hash = ?, user_prev_salt = ? where iduser = ?","ssissi",$hash,$salt,$forceflag,$prevhash,$prevsalt,$userid);
				}
				else
				{
					if ($renewdays > 0)
						return $this->p_update("update user set user_pw_renew_date = ?, user_pw_change_date = ?, user_hash = ?, user_salt = ?, user_forcereset = ?, user_prev_hash = ?, user_prev_salt = ? where iduser = ?","ssssissi",$strRenew,$strNow,$hash,$salt,$forceflag,$prevhash,$prevsalt,$userid);
					else
						return $this->p_update("update user set user_pw_change_date = ?, user_hash = ?, user_salt = ?, user_forcereset = ?, user_prev_hash = ?, user_prev_salt = ? where iduser = ?","sssissi",$strNow,$hash,$salt,$forceflag,$prevhash,$prevsalt,$userid);
				}
			}
		}
		return false;
	}

	public function updateFailCounter($userid)
	{
		if ($u = $this->getUser($userid))
		{
			$cnt = intval($u->user_failed_signin_count) + 1;
			if ($this->p_update("update user set user_failed_login_count = ? where iduser = ?","ii",$cnt,$userid) )
				return $cnt;
		}
		return 0;
	}

	public function resetFailCounter($id)
	{
		return $this->p_update("update user set user_failed_signin_count = 0 where iduser = ?","i",$id);
	}

	public function updateLastSignIn($id)
	{
		$strT = (new DateTime('now'))->format('Y-m-d H:i:s');
		return $this->p_update("update user set user_last_signin = '{$strT}' where iduser = ?","i",$id);
	}

	public function disableUser($id)
	{
		$strT = (new DateTime('now'))->format('Y-m-d H:i:s');
		return $this->p_update("update user set user_disabled = 1, user_disable_timestamp = ? where iduser = ?","si",$strT,$id);

	}

	public function updateUserRememberMe($id,$v)
	{
		$v = ($v) ? 1 : 0;
		return $this->p_update("update user set user_remember_me = {$v} where iduser = ?","i",$id);
	}

	public function updateUndoList($iduser,$list)
	{
		if (strlen($list) < 4000)
			return $this->p_update("update user set user_undolist = ? where iduser = ?", "si", $list, $iduser);
		else
			return null;
	}

	public function o_everyUserNotifyQuote()
	{
		$ret = null;
		$r = $this->p_query("select * from user where user_notify_quotes = 1", null, null);
		if ($r)
		{
			while ($o = $r->fetch_object("user"))
				$ret[] = $o;
		}
		return $ret;
	}

	//*********************************************************************
	// user session getters and setters functions
	//*********************************************************************
	public function getSession($id)
	{
		$user = $this->getUserByRandId($id);
		if ($user)
		{
			$s = $user->user_session_data->raw();
			$k = $user->user_session_key->raw();
			if ($s && strlen($s) > 0 && $k && strlen($k) > 0)
			{
				$d = $this->decrypt($s,$k);
				return json_decode($d,true);
			}
		}
		return null;
	}

	public function setSession($id,$session)
	{
		$user = $this->getUserByRandId($id);
		if ($user)
		{
			$k = $user->user_session_key->raw();
			if ($k && strlen($k) > 0)
			{
				$s = json_encode($session);
				$d = $this->encrypt($s,$k);
				return $this->p_update("update user set user_session_data = ? where user_randid = ?","ss",$d,$id);
			}
		}
		return false;
	}

	public function testencryption()
	{
		$user = $this->getUser(1);
		$testdata = ["a" =>1,"b" => "2"];
		$d = self::encrypt(json_encode($testdata),$user->user_session_key->raw());
		$data = self::decrypt($d,$user->user_session_key->raw());
		return json_decode($data,true);
	}

	//*********************************************************************
	// asset functions
	//*********************************************************************
	public function getAsset($id)
	{
		return $this->o_singlequery("asset","select * from asset where idasset = ?","i",$id);
	}

	public function createAsset($name,$date,$method,$rate)
	{
		$r = $this->p_create("insert into asset (asset_name,asset_purhcase_date,asset_depreciation_method,asset_depreciation_rate) values (?,?,?,?)","sssd",$name,$date,$method,$rate);
		if ($r)
			return $this->insert_id;
		return null;
	}

	public function allAssets()
	{
		return $this->p_query("select * from asset",null,null);
	}

	public function o_everyAsset()
	{
		$ret = array();
		$r =  $this->p_query("select * from asset order by asset_purhcase_date", null, null);
		if ($r)
		{
			while ($o = $r->fetch_object("asset"))
				$ret[] = $o;
		}
		return $ret;
	}

	public function allAssetJournals()
	{
		$r = $this->p_query("select * from asset left join journal on journal_asset = idasset left join chart on idchart = journal_chart where chart_type = 'asset' and chart_subtype = 'fixed_asset'",null,null);
	}

	//*********************************************************************
	// attachment functions
	//*********************************************************************
	public function o_getAttachmentGroup($id)
	{
		return $this->o_singlequery("attachment_group", "select * from attachment_group where idattachment_group = ?", "i", $id);
	}

	public function createAttachmentGroup($type,$description)
	{
		if ($this->p_create("insert into attachment_group (attachment_group_type,attachment_group_description) values (?,?)", "ss", $type, $description))
		{
			return $this->o_getAttachmentGroup($this->insert_id);
		}
		return null;
	}

	public function addAttachment($groupid,$filename,$orig_name="")
	{
		return $this->p_create("insert into attachment (attachment_group,attachment_filename,attachment_original_name) values (?,?,?)", "iss", $groupid, $filename, $orig_name);
	}

	public function o_everyAttachmentForGroup($id)
	{
		$a = array();
		$r = $this->p_query("select * from attachment where attachment_group = ?", "i", $id);
		if ($r)
		{
			while ($o = $r->fetch_object("attachment"))
				$a[] = $o;
		}
		return $a;
	}

	//*********************************************************************
	// Company functions
	//*********************************************************************
	public function getCompany()
	{
		return $this->o_singlequery("company", "select * from company limit 1",null,null);
	}

	//*********************************************************************
	// taxclass functions
	//*********************************************************************
	public function getTaxClass($id)
	{
		return $this->o_singlequery("taxclass","select * from taxclass where idtaxclass = ?","i",$id);
	}

	public function getTaxClassByName($name)
	{
		return $this->o_singlequery("taxclass","select * from taxclass where taxclass_name = ?","s",$name);
	}

	//*********************************************************************
	// taxrate functions
	//*********************************************************************
	public function getTaxRateForClassAndDate($taxclass,$date)
	{
		if (gettype($date) == "object")
			$strDate = $date->format("Y-m-d");
		else
			$strDate = $date;

		if (gettype($taxclass) == "integer")
			return $this->o_singlequery("taxrate","select * from taxrate left join taxclass on idtaxclass = taxrate_taxclass where taxrate_taxclass = ? and taxrate_from_date < ? order by taxrate_from_date desc limit 1","is",$taxclass,$strDate);
		else
			return $this->o_singlequery("taxrate","select * from taxrate left join taxclass on idtaxclass = taxrate_taxclass where taxclass_name = ? and taxrate_from_date < ? order by taxrate_from_date desc limit 1","ss",$taxclass,$strDate);
	}


	//*********************************************************************
	// chart functions
	//*********************************************************************
	public function getChart($code)
	{
		return $this->o_singlequery("chart","select * from chart where chart_code = ?","i",$code);
	}

	public function getChartFor($type,$subtype=null,$subsubtype=null,$options=0)
	{
		if ($subtype)
		{
			if ($subsubtype)
				$r = $this->p_query("select * from chart where chart_type = ? and chart_subtype = ? and chart_subsubtype = ? order by chart_code","sss",$type,$subtype,$subsubtype);
			else
				$r = $this->p_query("select * from chart where chart_type = ? and chart_subtype = ? order by chart_code","ss",$type,$subtype);
		}
		else
			$r = $this->p_query("select * from chart where chart_type = ? order by chart_code", "s", $type);

		if ($r->num_rows > 0) {
			if (($options & SEARCH_ONEONLY) && $r->num_rows > 1)
				return null;
			return $r->fetch_object("chart");
		}
		return null;
	}

	public function everyChartBank()
	{
		$q = "select * from chart where chart_type = 'cash' and chart_subtype = 'bank' order by chart_code";
		$r = $this->p_query($q,null,null);
		if (!$r) {$this->sqlError($q); return null;}
		if ($r->num_rows > 0)
			return $r->fetch_all(MYSQLI_ASSOC);
		return null;
	}

	public function everyChartExpense()
	{
		$q = "select * from chart left join taxclass on idtaxclass = chart_taxclass where chart_type = 'expense' order by chart_description";
		$r = $this->p_query($q, null, null);
		if (!$r) {
			$this->sqlError($q);
			return null;
		}
		if ($r->num_rows > 0)
			return $r->fetch_all(MYSQLI_ASSOC);
		return null;
	}

	public function everyChartExpenseAndAsset()
	{
		$q = "select * from chart left join taxclass on idtaxclass = chart_taxclass where chart_type = 'expense' or chart_type = 'asset' order by chart_description";
		$r = $this->p_query($q,null,null);
		if (!$r) {$this->sqlError($q); return null;}
		if ($r->num_rows > 0)
			return $r->fetch_all(MYSQLI_ASSOC);
		return null;
	}

	public function everyChartLoan()
	{
		$q = "select * from chart where chart_type = 'non current liability' and chart_subtype = 'loan' order by chart_code";
		$r = $this->p_query($q,null,null);
		if (!$r) {$this->sqlError($q); return null;}
		if ($r->num_rows > 0)
			return $r->fetch_all(MYSQLI_ASSOC);
		return null;
	}

	public function everyChartDescription()
	{
		$ret = array();
		$r = $this->p_query("select * from chart", null, null);
		while ($c = $r->fetch_object("chart"))
		{
			$ret[$c->chart_code] = $c->chart_description->toHTML();
		}
		return $ret;
	}

	public function everyChartType()
	{
		$ret = array();
		$r = $this->p_query("select * from chart", null, null);
		while ($c = $r->fetch_object("chart"))
		{
			$ret[$c->chart_code] = $c->chart_type->raw();
		}
		return $ret;
	}

	public function nextCOAAbove($n)
	{
		$next = $n + 1;

		while ($rec = $this->p_singlequery("select * from chart where chart_code = ?", "i", $next) )
			$next++;

		return $next;
	}

	//*********************************************************************
	// account functions
	//*********************************************************************
	public function getAccount($id)
	{
		return $this->o_singlequery("account","select * from account where idaccount = ?","i",$id);
	}

	public function allAccounts($where='',$order = '')
	{
		return $this->p_query("select * from account left join taxclass on idtaxclass = account_sale_tax_class {$where} {$order}",null,null);
	}

	//*********************************************************************
	// product functions
	//*********************************************************************
	public function getProduct($id)
	{
		return $this->o_singlequery("product","select * from product where idproduct = ?","i",$id);
	}

	public function allProductsByOrder()
	{
		return $this->p_query("select * from product where product_deleted = 0 order by product_order desc,product_description",null,null);
	}

	public function updateProductOrder()
	{
		//This routine counts the products in line items of invoice lines to find the most popular and u[date the product record.
		$r = $this->p_query("select invoice_line_product, count(*) as CNT from invoice_line group by invoice_line_product",null,null);
		while ($i = $r->fetch_assoc())
		{
			if ($i['invoice_line_product'])
				$this->p_update("update product set product_order = {$i['CNT']} where idproduct = {$i['invoice_line_product']}",null,null);
		}
	}

	//*********************************************************************
	// Invoice functions
	//*********************************************************************
	public function getInvoice($id)
	{
		return $this->o_singlequery("invoice","select * from invoice where idinvoice = ?","i",$id);
	}

	public function lastInvoiceNumber()
	{
		$i = $this->o_singlequery("invoice","select invoice_number from invoice order by invoice_number desc limit 1",null,null);
		if ($i)
			return intval($i->invoice_number);
		return 0;
	}

	public function allInvoices($where='',$order='')
	{
		return $this->p_query("select * from invoice {$where} {$order}",null,null);
	}

	public function createCashSaleInvoice($accountid,$date=null)
	{
		$company = $this->getCompany();
		$account = null;
		if ($accountid)
			$account = $this->getAccount($accountid);
		else
			$accountid = null;
		$inv_number = $this->lastInvoiceNumber() + 1;
		$invoice = array();

		if (!$date)
			$date = (new DateTime())->format("Y-m-d");
		else
		{
			if (gettype($date) == "object")
				$date = $date->format("Y-m-d");
		}

		$invoice["invoice_date"] = $date;
		$invoice["invoice_number"] = $inv_number;
		$invoice["invoice_account"] = $accountid;
		$invoice["invoice_cash_sale"] = 1;
		$invoice["invoice_company_name"] = $company->company_name->raw();
		$invoice["invoice_company_address1"] = $company->company_address1->raw();
		$invoice["invoice_company_address2"] = $company->company_address2->raw();
		$invoice["invoice_company_address3"] = $company->company_address3->raw();
		$invoice["invoice_company_address4"] = $company->company_address4->raw();
		$invoice["invoice_company_city"] = $company->company_city->raw();
		$invoice["invoice_company_postcode"] = $company->company_postcode->raw();
		$invoice["invoice_company_country"] = $company->company_country->raw();

		$taxclass = $this->getTaxClassByName($company->company_sales_tax_name->raw());

		if ($accountid != 0)
		{
			if ($account->account_sale_tax_class)
			{
				$taxclass = $this->getTaxClass($account->account_sale_tax_class);
				if ($taxclass)
				{
					$invoice["invoice_sale_tax_class"] = $taxclass->idtaxclass;
					$invoice["invoice_sale_tax_name"] = $taxclass->taxclass_invoice_text->raw();
					$invoice["invoice_tax_number"] = $company->company_tax_number->raw();
				}
			}
		}
		else
		{
			if ($taxclass)
			{
				$invoice["invoice_sale_tax_class"] = $taxclass->idtaxclass;
				$invoice["invoice_sale_tax_name"] = $taxclass->taxclass_invoice_text->raw();
				$invoice["invoice_tax_number"] = $company->company_tax_number->raw();
			}
		}

		if ($account)
		{
			$invoice["invoice_account_name"] = $account->account_name->toHTML();
			$invoice["invoice_account_address1"] = $account->account_address1->toHTML();
			$invoice["invoice_account_address2"] = $account->account_address2->toHTML();
			$invoice["invoice_account_address3"] = $account->account_address3->toHTML();
			$invoice["invoice_account_address4"] = $account->account_address4->toHTML();
			$invoice["invoice_account_city"] = $account->account_city->toHTML();
			$invoice["invoice_account_state"] = $account->account_state->toHTML();
			$invoice["invoice_account_postcode"] = $account->account_postcode->toHTML();
			$invoice["invoice_account_country"] = $account->account_country->toHTML();
		}

		$invoice["invoice_bank_acct_name"] = $company->company_bank_acct_name->raw();
		$invoice["invoice_bank_acct_number"] = $company->company_bank_acct_number->raw();

		$r = $this->p_create_from_array("invoice",$invoice);
		if ($r)
		{
			return $this->getInvoice($this->insert_id);
		}
		return null;

	}

	public function createAccountSaleInvoice($accountid,$date=null)
	{
		$company = $this->getCompany();
		$account = $this->getAccount($accountid);
		if (!$account)
			return null;
		$inv_number = $this->lastInvoiceNumber() + 1;
		$invoice = array();

		if (!$date)
			$date = (new DateTime())->format("Y-m-d");
		else
		{
			if (gettype($date) == "object")
				$date = $date->format("Y-m-d");
		}

		$invoice["invoice_date"] = $date;
		$invoice["invoice_number"] = $inv_number;
		$invoice["invoice_account"] = $accountid;
		$invoice["invoice_cash_sale"] = 0;
		$invoice["invoice_company_name"] = $company->company_name->raw();
		$invoice["invoice_company_address1"] = $company->company_address1->raw();
		$invoice["invoice_company_address2"] = $company->company_address2->raw();
		$invoice["invoice_company_address3"] = $company->company_address3->raw();
		$invoice["invoice_company_address4"] = $company->company_address4->raw();
		$invoice["invoice_company_city"] = $company->company_city->raw();
		$invoice["invoice_company_postcode"] = $company->company_postcode->raw();
		$invoice["invoice_company_country"] = $company->company_country->raw();

		if ($account->account_sale_tax_class)
		{
			$taxclass = $this->getTaxClass($account->account_sale_tax_class);
			if ($taxclass)
			{
				$invoice["invoice_sale_tax_class"] = $taxclass->idtaxclass;
				$invoice["invoice_sale_tax_name"] = $taxclass->taxclass_invoice_text->raw();
				$invoice["invoice_tax_number"] = $company->company_tax_number->raw();
			}

		}

		$invoice["invoice_bank_acct_name"] = $company->company_bank_acct_name->raw();
		$invoice["invoice_bank_acct_number"] = $company->company_bank_acct_number->raw();

		$invoice["invoice_account_name"] = $account->account_name->toHTML();
		$invoice["invoice_account_address1"] = $account->account_address1->toHTML();
		$invoice["invoice_account_address2"] = $account->account_address2->toHTML();
		$invoice["invoice_account_address3"] = $account->account_address3->toHTML();
		$invoice["invoice_account_address4"] = $account->account_address4->toHTML();
		$invoice["invoice_account_city"] = $account->account_city->toHTML();
		$invoice["invoice_account_state"] = $account->account_state->toHTML();
		$invoice["invoice_account_postcode"] = $account->account_postcode->toHTML();
		$invoice["invoice_account_country"] = $account->account_country->toHTML();

		//$invoice["invoice_account_ref1"] =
		//$invoice["invoice_account_ref2"] =

		$r = $this->p_create_from_array("invoice",$invoice);
		if ($r)
		{
			return $this->getInvoice($this->insert_id);
		}
		return null;

	}


	//*********************************************************************
	// Invoice Line functions
	//*********************************************************************
	public function getInvoiceLine($id)
	{
		return $this->o_singlequery("invoice_line", "select * from invoice_line where idinvoice_line = ?", "i",$id);
	}

	public function createInvoiceLine($invoiceid,$productid,$description,$qty,$unitdesc,$unit,$net,$tax,$gross)
	{
		if (!$productid)
			$productid = null;
		$r = $this->p_create("insert into invoice_line (invoice_line_invoice,invoice_line_product,invoice_line_description,invoice_line_qty,invoice_line_unit_desc,invoice_line_unit_cost,invoice_line_net_cost,invoice_line_tax_cost,invoice_line_gross_cost) values (?,?,?,?,?,?,?,?,?)", "iisdsdddd",$invoiceid,$productid,$description,$qty,$unitdesc,$unit,$net,$tax,$gross);
		if ($r)
		{
			return $this->getInvoiceLine($this->insert_id);
		}
		return null;
	}

	public function everyInvoiceLine($idinvoice)
	{
		$idinvoice = intval($idinvoice);
		return $this->every("invoice_line","where invoice_line_invoice = {$idinvoice}", "order by idinvoice_line");
	}

	public function sumInvoiceLines($idinvoice)
	{
		return $this->p_singlequery("select sum(invoice_line_net_cost) as NET, sum(invoice_line_tax_cost) as TAX, sum(invoice_line_gross_cost) as GROSS from invoice_line where invoice_line_invoice = ?", "i", $idinvoice);
	}

	//*********************************************************************
	// staff functions
	//*********************************************************************
	public function o_getStaff($id)
	{
		return $this->o_singlequery("staff", "select * from staff where idstaff = ?", "i", $id);
	}

	public function o_everyStaffOnTimesheet()
	{
		$ret = array();
		$r = $this->p_query("select * from staff where staff_deleted = 0 and staff_list_on_timesheet = 1 order by staff_name", null, null);
		if ($r)
		{
			while ($o = $r->fetch_object("staff"))
				$ret[] = $o;
		}
		return $ret;
	}

	public function allStaffWithUnpaidTime()
	{
		$ret = array();
		$r = $this->p_query("select timesheet_staff , count(*) from timesheet left join staff on idstaff = timesheet_staff where timesheet_processed = 0 and staff_type = 'casual' group by timesheet_staff order by staff_name",null,null);
		if ($r)
		{
			while ($rec = $r->fetch_assoc())
			{
				$ret[] = $this->o_getStaff($rec["timesheet_staff"]);
			}
		}
		return $ret;
	}

	public function o_everyStaff()
	{
		$ret = array();
		$r = $this->p_query("select * from staff where staff_deleted = 0 order by staff_name",null,null);
		if ($r)
		{
			while ($o = $r->fetch_object("staff"))
			{
				$ret[] = $o;
			}
		}
		return $ret;
	}

	//*********************************************************************
	// taxbracket functions
	//*********************************************************************
	public function calcMarginalTax($date,$annual,$currentGross=0)
	{

		$c = 0;
		$o = $this->o_singlequery("taxbracket","select * from taxbracket where taxbracket_from_date <= ? and taxbracket_amount <= ? order by taxbracket_from_date desc, taxbracket_amount desc limit 1", "sd", $date, $annual);
		$v = $o->taxbracket_product;

		if ($annual > $o->taxbracket_amount)
		{

			$o2 = $this->o_singlequery("taxbracket", "select * from taxbracket where taxbracket_from_date <= ? and taxbracket_amount > ? order by taxbracket_from_date desc, taxbracket_amount limit 1", "sd", $date, $annual);
			$remainder = $annual - $o->taxbracket_amount;
			$v += $o2->taxbracket_percent * ($annual - $o->taxbracket_amount);

		}
		$ratio = $v / $annual;
		if ($currentGross > 0)
		{
			$c = $currentGross * $ratio;
		}
		return ["tax" => $v, "ratio" => $ratio, "current" => $c];
	}



	//*********************************************************************
	// timesheet functions
	//*********************************************************************
	public function o_getTimeSheetFor($staffid,$date)
	{
		return $this->o_singlequery("timesheet", "select * from timesheet where timesheet_staff = ? and timesheet_date = ?", "is", $staffid, $date);
	}

	public function createUpdateTimeEntry($staffid,$date,$hours,$userid)
	{
		$o_ts = $this->o_getTimeSheetFor($staffid, $date);
		if ($o_ts && ! $o_ts->timesheet_processed) {
			$o_ts->timesheet_hours = $hours;
			$o_ts->timesheet_entry_user = $userid;
			$o_ts->update($this);
		}
		else
		{
			return $this->p_create("insert into timesheet (timesheet_date,timesheet_staff,timesheet_hours,timesheet_entry_user) values (?,?,?,?)", "sidi", $date, $staffid, $hours, $userid);
		}
	}

	public function createPayDirectEntry($staffid, $date, $gross, $period,$userid)
	{
		return $this->p_create("insert into timesheet (timesheet_date,timesheet_staff,timesheet_type,timesheet_direct_gross,timesheet_pay_cadence,timesheet_entry_user) values (?,?,'direct',?,?,?)", "sidsi", $date, $staffid, $gross, $period,$userid);
	}

	public function deleteTimesheetEntry($staffid, $date)
	{
		return $this->p_delete("delete from timesheet where timesheet_staff = ? and timesheet_date = ?", "is", $staffid, $date);
	}

	public function everyTimeSheetForStaff($staffid, $start, $end)
	{
		error_log("start {$start} end {$end}");
		$ret = array();
		$r = $this->p_query("select * from timesheet where timesheet_staff = ? and timesheet_date >= ? and timesheet_date <= ? order by timesheet_date", "iss", $staffid, $start, $end);
		if ($r)
		{
			while ($o = $r->fetch_object("timesheet"))
				$ret[] = $o;
		}
		return $ret;
	}

	public function totalUnpaidTimesheetHours($idstaff)
	{
		$sum = 0;
		$first = "";
		$last = "";
		$r = $this->p_query("select * from timesheet where timesheet_staff = ? and timesheet_processed = 0 order by timesheet_date", "i", $idstaff);
		if ($r)
		{
			while ($rec = $r->fetch_assoc())
			{
				$sum += floatval($rec["timesheet_hours"]);
				if (strlen($first) == 0)
					$first = $rec["timesheet_date"];
				$last = $rec["timesheet_date"];
			}
		}
		return ["sum" => $sum, "date_first" => $first, "date_last" => $last];
	}

	public function periodTimesheetForStaffAndXtn($staffid,$xtnid)
	{
		$sum = 0.0;
		$gross = 0.0;
		$first = "";
		$last = "";
		$paid_date = "";
		$type = "hours";
		$r = $this->p_query("select * from timesheet where timesheet_staff = ? and timesheet_processed = 1 and timesheet_payxtn = ? order by timesheet_date", "ii", $staffid, $xtnid);
		if ($r)
		{
			while ($rec = $r->fetch_assoc())
			{
				if ($rec["timesheet_type"] == "direct")
				{
					$gross += $rec["timesheet_direct_gross"];
					$type = "direct";
				}
				else
					$sum += floatval($rec["timesheet_hours"]);
				if (strlen($first) == 0)
					$first = $rec["timesheet_date"];
				$last = $rec["timesheet_date"];
				$paid_date = $rec["timesheet_paid_date"];
			}

		}
		return ["sum" => $sum, "date_first" => $first, "date_last" => $last,"date_paid" => $paid_date,"type" => $type,"gross" => $gross];
	}

	public function markAllTimesheetPaidFor($staffid,$xtn,$date)
	{
		return $this->p_update("update timesheet set timesheet_processed = 1 , timesheet_payxtn = ?, timesheet_paid_date = ? where timesheet_staff = ? and timesheet_processed = 0" , "isi", $xtn,$date,$staffid);
	}

	public function allStaffReceivedPay()
	{
		$ret = array();
		$r = $this->p_query("select timesheet_staff from timesheet left join staff on idstaff = timesheet_staff where timesheet_processed = 1 group by timesheet_staff order by staff_name",null,null);
		if ($r)
		{
			while ($rec = $r->fetch_assoc())
			{
				$ret[] = $this->o_getStaff(intval($rec["timesheet_staff"]));
			}
		}
		return $ret;
	}

	public function getTimeSheetsForXtn($xtn,$stafffid)
	{
		$ret = array();
		$r = $this->p_query("select * from timesheet where timesheet_payxtn = ? and timesheet_staff = ? order by timesheet_date", "ii", $xtn, $stafffid);
		if ($r)
		{
			while ($o = $r->fetch_object("timesheet"))
				$ret[] = $o;
		}
		return $ret;
	}

	//*********************************************************************
	// journal functions
	//*********************************************************************
	public function o_getJournal($id)
	{
		return $this->o_singlequery("journal", "select * from journal left join chart on chart_code = journal_chart where idjournal = ?", "i", $id);
	}

	public function o_journalGetTransactionForCOA($xtn,$coa)
	{
		return $this->o_singlequery("journal", "select * from journal left join chart on chart_code = journal_chart where journal_xtn = ? and journal_chart = ?", "ii", $xtn,$coa);
	}

	public function allJournal($where="",$order="")
	{
		return $this->p_query("select * from journal {$where} {$order}",null,null);
	}

	public function firstJournal()
	{
		return $this->p_singlequery("select * from journal order by journal_date, idjournal limit 1",null,null);
	}

	public function getLastXtn()
	{
		$j = $this->p_singlequery("select journal_xtn as XTN from journal order by journal_xtn desc limit 1",null,null);
		if ($j)
			return intval($j['XTN']);
		return 0;
	}

	public function getLastFolio()
	{
		$j = $this->p_singlequery("select journal_folio as FOLIO from journal order by journal_folio desc limit 1",null,null);
		if ($j)
			return intval($j['FOLIO']);
		return 0;
	}


	public function getJournalFirstDate()
	{
		$row = $this->p_singlequery("select journal_date from journal order by journal_date asc limit 1",null,null);
		if ($row)
			return $row["journal_date"];
		return null;
	}

	public function getJournalLastDate()
	{
		$row = $this->p_singlequery("select journal_date from journal order by journal_date desc limit 1",null,null);
		if ($row)
			return $row["journal_date"];
		return null;
	}

	public function getJournalStartAndEndDates()
	{
		return  [$this->getJournalFirstDate(), $this->getJournalLastDate()];
	}

	public function createJournalStartEOYRecord(DateTime $d)
	{
		$strDate = $d->format("Y-m-d H:i:s");
		return $this->p_create("insert into journal (journal_date,journal_marker) values (?,'starteoy')","s",$strDate);
	}

	public function createJournalEndEOYRecord(DateTime $d)
	{
		$strDate = $d->format("Y-m-d H:i:s");
		return $this->p_create("insert into journal (journal_date,journal_marker) values (?,'endeoy')","s",$strDate);
	}

	public function getJournalStartEOYRecord(DateTime $d)
	{
		$strDate = $d->format("Y-m-d H:i:s");
		return $this->p_singlequery("select * from journal where journal_date = ? and journal_marker = 'starteoy'","s",$strDate);
	}

	public function getJournalEndEOYRecord(DateTime $d)
	{
		$strDate = $d->format("Y-m-d H:i:s");
		return $this->p_singlequery("select * from journal where journal_date = ? and journal_marker = 'endeoy'","s",$strDate);
	}


	public function FindCashTransaction(DateTime $d,$amt)
	{
		$cnt = 0;
		$strDate = $d->format("Y-m-d");
		$amt = floatval($amt);

		$r = $this->p_query("select journal_date, journal_folio, sum(journal_gross) as GROSS from journal where journal_chart = 100 and journal_date = ? group by journal_date, journal_folio","s", $strDate);
		if ($r && $r->num_rows > 0)
		{
			while ($a = $r->fetch_assoc() )
			{
				if ($a["GROSS"] == $amt)
				{
					$cnt++;
				}
			}
			return $cnt;
		}
		return 0;
	}

	public function FindCashTransactionNear(DateTime $d, $amt)
	{
		$cnt = 0;
		$a = array();

		$d1 = clone $d;
		$d2 = clone $d;

		$d1 = $d1->sub(new DateInterval('P4D'));
		$d2 = $d2->add(new DateInterval('P4D'));

		$strDate1 = $d1->format("Y-m-d");
		$strDate2 = $d2->format("Y-m-d");
		$amt = floatval($amt);
		$r = $this->p_query("select journal_date, journal_folio, sum(journal_gross) as GROSS from journal where journal_chart = 100 and journal_date >= ? and journal_date <= ?  group by journal_date, journal_folio", "ss", $strDate1, $strDate2);
		//$r = $this->p_query("select * from journal where journal_chart = 100 and journal_date >= ? and journal_date <= ? and journal_gross = ?", "ssd", $strDate1,$strDate2 ,$amt);
		if ($r and $r->num_rows > 0)
		{
			while ($a = $r->fetch_assoc() )
			{
				if ($a["GROSS"] == $amt)
				{
					$save = $a;
					$cnt++;
				}
			}
			if ($cnt == 1)
			{
				return $save;
			}
		}
		return NULL;
	}

	public function createPair($rec,$coa1,$coa2,$source=0,$enterTransaction=true)
	{
		if ($enterTransaction)
			$this->BeginTransaction();

		//Get last xtn id
		$xtn = $this->getLastXtn() + 1;
		$rec["journal_source"] = ($source == 0) ? $xtn : $source;
		$rec["journal_xtn"] = $xtn;
		$rec["journal_chart"] = $coa1;

		$rec2 = $rec;
		$rec2["journal_chart"] = $coa2;

		$rec2["journal_net"] = -($rec["journal_net"] );
		$rec2["journal_tax"] = -($rec["journal_tax"] );
		$rec2["journal_gross"] = -($rec["journal_gross"] );

		$this->p_create_from_array("journal",$rec);
		$rec2["journal_link"] = $this->insert_id;
		$this->p_create_from_array("journal",$rec2);
		$rec["journal_link"] = $this->insert_id;

		//Update the first record with the new link
		$this->p_update("update journal set journal_link = {$rec["journal_link"]} where idjournal = {$rec2['journal_link']}",null,null);

		if ($enterTransaction)
		{
			if ($this->EndTransaction() )
				return $xtn;
			return null;
		}
		return $xtn;
	}

	public function getJournalPair($id)
	{
		$a = array();
		$a[0] = $this->o_singlequery("journal", "select * from journal left join chart on chart_code = journal_chart where idjournal = ?", "i", $id);
		if ($a[0])
			$a[1] = $this->o_singlequery("journal", "select * from journal left join chart on chart_code = journal_chart where idjournal = ?", "i", $a[0]->journal_link);
		return $a;
	}

	public function hasExpensBeenPaid($id)
	{
		$pair = $this->getJournalPair($id);
		if (count($pair) == 2)
		{
			if ($pair[1]->chart_type->raw() == 'cash')
				return true;
		}
		return false;
	}

	public function saleCash($strdate,$description,$account_num,$invoice_num,$ledgerAmount,$chart1=0,$chart2=0,$enterTransaction=true)
	{
		//Get last folio
		$folio = $this->getLastFolio() + 1;

		$rec1 = array();
		$rec1['journal_date'] = $strdate;
		if (strlen($description) == 0)
			$rec1['journal_description'] = "CASH SALE";
		else
			$rec1['journal_description'] = $description;
		$rec1['journal_net'] = $ledgerAmount->net;
		$rec1['journal_tax'] = $ledgerAmount->tax;
		$rec1['journal_gross'] = $ledgerAmount->gross;
		if ($account_num)
			$rec1['journal_account'] = $account_num;
		if ($invoice_num)
			$rec1['journal_invoice'] = $invoice_num;
		$rec1['journal_folio'] = $folio;

		//Now find coa
		if (!$chart1)
		{
			$c = $this->getChartFor('cash',null,null,SEARCH_FIRST);
			if (! $c)
				throw (new Exception("Unable to find chart for current asset/accounts receivable"));
			$chart1 = $c->chart_code;
		}
		if (!$chart2)
		{
			$c = $this->getChartFor('income','sale');
			if (! $c)
				throw (new Exception("Unable to find chart for current income/sale"));
			$chart2 = $c->chart_code;
		}
		$rec1["journal_source_chart"] = $chart2;
		return $this->createPair($rec1,$chart1,$chart2,0,$enterTransaction);

	}

	public function saleAccount($strdate,$description,$account_num,$invoice_num,$ledgerAmount,$chart1=0,$chart2=0,$enterTransaction=true)
	{
		//Get last folio
		$folio = $this->getLastFolio() + 1;

		$rec1 = array();
		$rec1['journal_date'] = $strdate;
		if (strlen($description) == 0)
			$rec1['journal_description'] = "SALE ON ACCOUNT";
		else
			$rec1['journal_description'] = $description;
		$rec1['journal_net'] = $ledgerAmount->net;
		$rec1['journal_tax'] = $ledgerAmount->tax;
		$rec1['journal_gross'] = $ledgerAmount->gross;
		if ($account_num)
			$rec1['journal_account'] = $account_num;
		if ($invoice_num)
			$rec1['journal_invoice'] = $invoice_num;
		$rec1['journal_folio'] = $folio;

		//Now find coa
		if (!$chart1)
		{
			$c = $this->getChartFor('current asset',"accounts receivable",null,SEARCH_FIRST);
			if (! $c)
				throw (new Exception("Unable to find chart for current asset/accounts receivable"));
			$chart1 = $c->chart_code;
		}
		if (!$chart2)
		{
			$c = $this->getChartFor('income','sale');
			if (! $c)
				throw (new Exception("Unable to find chart for current income/sale"));
			$chart2 = $c->chart_code;
		}
		$rec1["journal_source_chart"] = $chart2;

		return $this->createPair($rec1,$chart1,$chart2,0,$enterTransaction);

	}

	public function expensePaid($strdate,$description,$ledgerAmount,$vendname,$vendtax,$chart1=0,$chart2=0,$asset=null,$attachment_group=null,$enterTransaction=true)
	{

		//Get last folio
		$folio = $this->getLastFolio() + 1;
		$rec1 = array();
		$rec1['journal_date'] = $strdate;
		if (strlen($description) == 0)
			$rec1['journal_description'] = "EXPENSE PAID";
		else
			$rec1['journal_description'] = $description;
		if ($asset)
			$rec1['journal_asset'] = $asset;
		$rec1['journal_net'] = -($ledgerAmount->net);
		$rec1['journal_tax'] = -($ledgerAmount->tax);
		$rec1['journal_gross'] = -($ledgerAmount->gross);

		$rec1['journal_folio'] = $folio;
		$rec1['journal_attachment_group'] = $attachment_group;
		$rec1['journal_vendor_name'] = $vendname;
		$rec1['journal_vendor_tax_number'] = $vendtax;

		//Now find coa
		if (!$chart1)
		{
			$c = $this->getChartFor('cash',null,null,SEARCH_FIRST);
			if (! $c)
				throw (new Exception("Unable to find chart for current asset/accounts receivable"));
			$chart1 = $c->chart_code;
		}
		if (!$chart2)
		{
			$c = $this->getChartFor('expense',null,null,SEARCH_FIRST);
			if (! $c)
				throw (new Exception("Unable to find chart for current income/sale"));
			$chart2 = $c->chart_code;
		}
		$rec1["journal_source_chart"] = $chart2;

		return $this->createPair($rec1,$chart1,$chart2,0,$enterTransaction);
	}

	public function expenseUnPaid($strdate,$description,$ledgerAmount,$vendname,$vendtax,$chart1=0,$chart2=0,$asset=null, $attachment_group = null,$enterTransaction = true)
	{

		//Get last folio
		$folio = $this->getLastFolio() + 1;
		$rec1 = array();
		$rec1['journal_date'] = $strdate;
		if (strlen($description) == 0)
			$rec1['journal_description'] = "ACCRUED EXPENSE";
		else
			$rec1['journal_description'] = $description;
		if ($asset)
			$rec1['journal_asset'] = $asset;
		$rec1['journal_net'] = -($ledgerAmount->net);
		$rec1['journal_tax'] = -($ledgerAmount->tax);
		$rec1['journal_gross'] = -($ledgerAmount->gross);

		$rec1['journal_folio'] = $folio;
		$rec1['journal_attachment_group'] = $attachment_group;
		$rec1['journal_vendor_name'] = $vendname;
		$rec1['journal_vendor_tax_number'] = $vendtax;

		//Now find coa
		if (!$chart1) {
			$c = $this->getChartFor('current liability', 'accounts payable', null, SEARCH_FIRST);
			if (!$c)
				throw (new Exception("Unable to find chart for current asset/accounts receivable"));
			$chart1 = $c->chart_code;
		}
		if (!$chart2) {
			$c = $this->getChartFor('expense',null, null, SEARCH_FIRST);
			if (!$c)
				throw (new Exception("Unable to find chart for current income/sale"));
			$chart2 = $c->chart_code;
		}
		$rec1["journal_source_chart"] = $chart2;

		return $this->createPair($rec1,$chart1,$chart2,0,$enterTransaction);
	}

	public function newLoan($date,$amount,$description="Bank Loan")
	{


		//Find next available COA starting at 450
		$coa2 = $this->nextCOAAbove(449);

		$this->BeginTransaction();
		//Create COA
		$chart = array();
		$chart["chart_code"] = $coa2;
		$chart["chart_type"] = "non current liability";
		$chart["chart_type_name"] = "Non Current Liabilities";
		$chart["chart_subtype"] = "loan";
		$chart["chart_description"] = $description;
		$chart["chart_taxclass"] = null;
		$chart["chart_description_dr"] = "";
		$chart["chart_description_cr"] = "";
		$chart["chart_balancesheet"] = "liability";
		$chart["chart_balancesheet_subtype"] = "non_current_liability";

		$this->p_create_from_array("chart",$chart);

		//Create Journal record
		$coa1 = ($this->getChartFor('cash',null,null,SEARCH_FIRST))->chart_code;

		$rec = array();
		$rec['journal_date'] = $date;
		$rec['journal_description'] = $description;
		$rec['journal_net'] = $amount;
		$rec['journal_tax'] = 0.00;
		$rec['journal_gross'] = $amount;
		$rec['journal_folio'] = ($this->getLastFolio() + 1);

		$xtn = $this->createPair($rec, $coa1, $coa2, 0, false);
		if (!$xtn)
			$this->TransactionError();

		return $this->EndTransaction();
	}

	public function LoanCrPrinciple($date, $coa2, $amount, $description)
	{
		$coa1 = ($this->getChartFor('cash', null, null, SEARCH_FIRST))->chart_code;

		$rec = array();
		$rec['journal_date'] = $date;
		$rec['journal_description'] = $description;
		$rec['journal_net'] = -($amount);
		$rec['journal_tax'] = 0.00;
		$rec['journal_gross'] = -($amount);
		$rec['journal_folio'] = ($this->getLastFolio() + 1);

		$this->BeginTransaction();

		$xtn = $this->createPair($rec, $coa1, $coa2, 0, false);
		if (!$xtn)
			$this->TransactionError();

		$this->EndTransaction();

		return $xtn;

	}

	public function LoanDrPrinciple($date, $coa2, $amount, $description)
	{
		$coa1 = ($this->getChartFor('cash', null, null, SEARCH_FIRST))->chart_code;

		$rec = array();
		$rec['journal_date'] = $date;
		$rec['journal_description'] = $description;
		$rec['journal_net'] = $amount;
		$rec['journal_tax'] = 0.00;
		$rec['journal_gross'] = $amount;
		$rec['journal_folio'] = ($this->getLastFolio() + 1);

		$this->BeginTransaction();

		$xtn = $this->createPair($rec, $coa1, $coa2, 0, false);
		if (!$xtn)
			$this->TransactionError();

		$this->EndTransaction();

		return $xtn;

	}

	public function LoanInterest($date, $coa2, $amount, $description)
	{
		$coa1 = ($this->getChartFor('expense', "financial", "interest", SEARCH_ONEONLY))->chart_code;

		$rec = array();
		$rec['journal_date'] = $date;
		$rec['journal_description'] = $description;
		$rec['journal_net'] = $amount;
		$rec['journal_tax'] = 0.00;
		$rec['journal_gross'] = $amount;
		$rec['journal_folio'] = ($this->getLastFolio() + 1);

		$this->BeginTransaction();

		$xtn = $this->createPair($rec, $coa1, $coa2, 0, false);
		if (!$xtn)
			$this->TransactionError();

		$this->EndTransaction();

		return $xtn;

	}

	public function ReceivedInterest($date,$amount,$description)
	{
		$coa1 = ($this->getChartFor('cash', null, null, SEARCH_FIRST))->chart_code;
		$coa2 = ($this->getChartFor('income', "financial", "interest", SEARCH_ONEONLY))->chart_code;

		$rec = array();
		$rec['journal_date'] = $date;
		$rec['journal_description'] = $description;
		$rec['journal_net'] = $amount;
		$rec['journal_tax'] = 0.00;
		$rec['journal_gross'] = $amount;
		$rec['journal_folio'] = ($this->getLastFolio() + 1);

		$this->BeginTransaction();

		$xtn = $this->createPair($rec, $coa1, $coa2, 0, false);
		if (!$xtn)
			$this->TransactionError();

		$this->EndTransaction();

		return $xtn;

	}

	public function allLoanTransactionsFor($coa)
	{
		return $this->p_query("select * from journal where journal_chart = ? order by journal_date", "i", $coa);
	}

	public function loanTransactions()
	{
		$ret = array();
		$ret["chart_descriptions"] = $this->everyChartDescription();
		$ret["chart_types"] = $this->everyChartType();
		$ret["loans"] = array();;

		$allCharts = $this->everyChartLoan();
		foreach ($allCharts as $chart)
		{
			$coa = $chart["chart_code"];
			$ret["loans"] [$coa] = array();
			$ret["loans"] [$coa]["xtns"] = array();
			$ret["loans"] [$coa]["desc"] = $chart["chart_description"];

			$r = $this->allLoanTransactionsFor($coa);
			while ($j = $r->fetch_assoc())
			{
				$a = $this->getJournalPair($j["idjournal"]);
				$a[0]->other_chart = $a[1]->journal_chart;
				$ret["loans"] [$coa]["xtns"] [] = $a[0];
			}
		}
		return $ret;
	}

	public function allWagesForStaff($staffid)
	{
		$coa = ($this->getChartFor("expense", "cost of sale", "wages"))->chart_code;
		$r = $this->p_query("select * from journal where journal_chart =  ? and journal_staff = ? and journal_wage_tax is not null", "ii", $coa, $staffid);
		return $r->fetch_all(MYSQLI_ASSOC);
	}

	public function wagesForPeriod($staffid, $dateFrom, $dateTo)
	{
		$sum_net = 0;
		$sum_tax = 0;
		$sum_ks_employee = 0;
		$sum_ks_employer = 0;
		$sum_ks_esct = 0;
		$coa = ($this->getChartFor("expense", "cost of sale", "wages"))->chart_code;
		$r = $this->p_query("select * from journal where journal_chart =  ? and journal_staff = ? and journal_date >= ? and journal_date <= ? and journal_wage_tax is not null", "iiss", $coa, $staffid, $dateFrom, $dateTo);
		while ($j = $r->fetch_assoc())
		{
			$sum_net += $j["journal_gross"];
			$sum_tax += $j["journal_wage_tax"];
			$sum_ks_employee += $j["journal_kiwisaver_employee"];
			$sum_ks_employer += $j["journal_kiwisaver_employer"];
			$sum_ks_esct += $j["journal_kiwisaver_esct_tax"];

		}
		return ["net" => $sum_net, "tax" => $sum_tax, "gross" => ($sum_net+$sum_tax),"ks_employee" => $sum_ks_employee,"ks_employer" => $sum_ks_employer, "ks_tax" => $sum_ks_esct];
	}

	public function everyPayRun()
	{
		$ret = array();
		$coa = ($this->getChartFor("expense","cost of sale","wages"))->chart_code;
		$r = $this->p_query("select journal_date, count(*) as CNT from journal where journal_chart = ? and journal_wage_tax is not null group by journal_date", "i", $coa);
		if ($r)
		{
			while ($rec = $r->fetch_assoc())
				$ret[] = $rec;
		}
		return $ret;
	}

	public function o_allJournalsForPayRun($date)
	{
		$ret = array();
		$coa = ($this->getChartFor("expense", "cost of sale", "wages"))->chart_code;
		$r = $this->p_query("select * from journal where journal_chart = ? and journal_wage_tax is not null and journal_date = ?", "is", $coa, $date);
		if ($r)
		{
			while ($o = $r->fetch_object("journal"))
				$ret[] = $o;
		}
		return $ret;
	}

	public function journalInvoiceOutstanding($invoiceid)
	{
		//Looks at the jouranl to determine howm much is outsanding on an invoice
		//i.e. what is the balance in accounst receiveable.

		return $this->p_singlequery("select sum(journal_net) as NET, sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where journal_invoice = ? and chart_type = 'current asset' and chart_subtype = 'accounts receivable'","i",$invoiceid);
	}

	public function getBankBalances()
	{
		$ret = array();
		$bankcharts = $this->everyChartBank();
		foreach($bankcharts as $bankchart)
		{
			$j = $this->p_singlequery("select sum(journal_gross) as GROSS from journal where journal_chart = ?","i",$bankchart["chart_code"]);
			$ret[$bankchart["chart_code"]] = $j["GROSS"];
		}
		return $ret;
	}

	public function getAccountPayable($xtn)
	{
		$c = $this->getChartFor('current liability','accounts payable',null,SEARCH_FIRST);
		if ($c)
		{
			$code = $c->chart_code;
			return $this->p_singlequery("select * from journal where journal_chart = {$code} and journal_xtn = ?","i",$xtn);
		}
	}

	public function payAccountsPayable($xtn,$amount,$date)
	{
		$rec = $this->getAccountPayable($xtn);
		if ($rec)
		{

			$j2 = $this->o_getJournal($rec["journal_link"]);

			$rec["journal_date"] = $date;
			$rec["journal_source_chart"] = $j2->journal_chart;

			$chart2 = $rec["journal_chart"];
			unset($rec["idjournal"]);
			unset($rec["journal_xtn"]);
			unset($rec["journal_link"]);
			unset($rec["journal_chart"]);

			$c = $this->getChartFor('cash',null,null,SEARCH_FIRST);
			if (! $c)
				throw (new Exception("Unable to find chart for current asset/accounts receivable"));
			$chart1 = $c->chart_code;
			return $this->createPair($rec,$chart1,$chart2,$xtn);
		}
		return null;
	}

	public function payAccountsPayableCombined($xtn, $amount, $date, $description, $folio)
	{
		$rec = $this->getAccountPayable($xtn);
		if ($rec)
		{
			$j2 = $this->o_getJournal($rec["journal_link"]);

			$rec["journal_date"] = $date;
			$rec["journal_source_chart"] = $j2->journal_chart;

			$chart2 = $rec["journal_chart"];
			unset($rec["idjournal"]);
			unset($rec["journal_xtn"]);
			unset($rec["journal_link"]);
			unset($rec["journal_chart"]);
			$rec["journal_description"] = $description;
			$rec["journal_folio"] = $folio;

			$c = $this->getChartFor('cash',null,null,SEARCH_FIRST);
			if (! $c)
				throw (new Exception("Unable to find chart for current asset/accounts receivable"));
			$chart1 = $c->chart_code;
			return $this->createPair($rec,$chart1,$chart2,$xtn);
		}
		return null;
	}

	public function everyAccountsPayable()
	{
		$ret = array();
		$q = "select * from journal left join chart on chart_code = journal_chart where chart_type = 'current liability' and chart_subtype = 'accounts payable' and journal_gross < 0 order by journal_date";
		$r = $this->p_query($q,null,null);
		if (!$r) {$this->sqlError($q); return null;}
		if ($r->num_rows > 0)
		{
			$list = $r->fetch_all(MYSQLI_ASSOC);
			for($idx = 0;$idx < count($list); $idx++)
			{
				$match = $this->p_singlequery("select sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'current liability' and chart_subtype = 'accounts payable' and journal_gross > 0 and journal_source = {$list[$idx] ['journal_source']}",null,null);
				$list[$idx] ["journal_gross"] = $list[$idx] ["journal_gross"] + $match["GROSS"];
				if ($list[$idx]["journal_gross"] != 0.0)
					$ret[] = $list[$idx];
			}
			return $ret;
		}
		return null;
	}

	public function getAccountReceivable($xtn)
	{
		$c = $this->getChartFor('current asset','accounts receivable',null,SEARCH_FIRST);
		if ($c)
		{
			$code = $c->chart_code;
			return $this->p_singlequery("select * from journal where journal_chart = {$code} and journal_xtn = ?","i",$xtn);
		}
	}

	public function everyAccountsReceivable()
	{
		$ret = array();
		$q = "select * from journal left join chart on chart_code = journal_chart where chart_type = 'current asset' and chart_subtype = 'accounts receivable' and journal_gross > 0 order by journal_date";
		$r = $this->p_query($q,null,null);
		if (!$r) {$this->sqlError($q); return null;}
		if ($r->num_rows > 0)
		{
			$list = $r->fetch_all(MYSQLI_ASSOC);
			for($idx = 0;$idx < count($list); $idx++)
			{
				$match = $this->p_singlequery("select sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'current asset' and chart_subtype = 'accounts receivable' and journal_gross < 0 and journal_source = {$list[$idx] ['journal_source']}",null,null);
				$list[$idx] ["journal_gross"] = $list[$idx] ["journal_gross"] + $match["GROSS"];
				if ($list[$idx]["journal_gross"] != 0.0)
					$ret[] = $list[$idx];
			}
			return $ret;
		}
		return null;
	}

	public function everyAssetJournals($assetid)
	{
		$q = "select * from journal left join asset on idasset = journal_asset left join chart on chart_code = journal_chart where journal_asset = {$assetid} and chart_type = 'asset' and chart_subtype ='fixed_asset'";
		$r = $this->p_query("select * from journal left join asset on idasset = journal_asset left join chart on chart_code = journal_chart where journal_asset = ? and chart_type = 'asset' and chart_subtype ='fixed_asset'","i",$assetid);
		if (!$r) {$this->sqlError($q); return null;}
		if ($r->num_rows > 0)
			return $r->fetch_all(MYSQLI_ASSOC);
		return null;
	}

	public function assetOrginalValue($assetid)
	{
		$q = "select journal_net as NET from journal left join chart on chart_code = journal_chart where chart_type = 'asset' and chart_subtype ='fixed_asset' and journal_asset = ? order by journal_date limit 1";
		$row = $this->p_singlequery($q,"i",$assetid);
		if ($row)
			return $row["NET"];
	}

	public function assetAgeMonths($assetid,DateTime $date)
	{
		$q = "select journal_date from journal left join chart on chart_code = journal_chart where chart_type = 'asset' and chart_subtype ='fixed_asset' and journal_asset = ? order by journal_date limit 1";
		$row = $this->p_singlequery($q,"i",$assetid);
		if ($row)
		{
			$elapsedseconds = $date->getTimestamp() - (new DateTime($row["journal_date"]))->getTimestamp();
			return round($elapsedseconds/2628000,0);
		}
		return null;
	}

	public function assetCurrentValue($assetid,DateTime $yearenddate)
	{
		$strdate = $yearenddate->format("Y-m-d H:i:s");

		$q = "select sum(journal_net) as SUM from journal left join chart on chart_code = journal_chart where chart_type = 'asset' and chart_subtype ='fixed_asset' and journal_asset = ? and journal_date <= ?";
		$row = $this->p_singlequery($q,"is",$assetid,$strdate);
		if ($row)
			return $row["SUM"];
		return null;
	}

	public function payAccountsReceivable($xtn,$amount,$date)
	{
		//We may need to calculate slaes tax
		$company = $this->getCompany();
		$taxclass = $this->getTaxClassByName($company->company_sales_tax_name->raw());
		$rec = $this->getAccountReceivable($xtn);
		if ($rec)
		{
			$salestaxrate = ($this->getTaxRateForClassAndDate($taxclass->idtaxclass, $rec["journal_date"]))->taxrate_rate;

			$j2 = $this->o_getJournal($rec["journal_link"]);

			$this->BeginTransaction();

			if ($amount != $rec["journal_gross"])
			{
				if ($amount < $rec["journal_gross"])
				{
					$rec["journal_gross"] = $amount;
					if ($rec["journal_tax"] != 0.00)
					{
						$rec["journal_net"] = round($rec["journal_gross"] / (1.00 + $salestaxrate), 2);
						$rec["journal_tax"] = $rec["journal_gross"] - $rec["journal_net"];
					}
					else
					{
						$rec["journal_net"] = $amount;
						$rec["journal_tax"] = 0.00;
					}
				}

				if ($amount > $rec["journal_gross"])
				{
					$rec2 = $rec;
					$rec2["journal_gross"] = $amount - $rec["journal_gross"];
					if ($rec["journal_tax"] != 0.00)
					{
						$rec2["journal_net"] = round($rec2["journal_gross"] / (1.00 + $salestaxrate), 2);
						$rec2["journal_tax"] =  $rec2["journal_gross"] - $rec2["journal_net"];
					}
					else
					{
						$rec2["journal_net"] = $rec2["journal_gross"];
						$rec2["journal_tax"] = 0.00;
					}

					//Now we create a new transaction
					$rec2["journal_date"] = $date;
					$rec2["journal_source_chart"] = $j2->journal_chart;
					unset($rec2["idjournal"]);
					unset($rec2["journal_xtn"]);
					unset($rec2["journal_link"]);
					unset($rec2["journal_chart"]);

					$c = $this->getChartFor('cash',null,null,SEARCH_FIRST);
					$chart1 = $c->chart_code;
					$c = $this->getChartFor('current liability','customer credits',null,SEARCH_FIRST);
					$chart2 = $c->chart_code;
					$this->createPair($rec2,$chart1,$chart2,$xtn,false);

				}

			}


			$rec["journal_date"] = $date;
			$rec["journal_source_chart"] = $j2->journal_chart;

			$chart2 = $rec["journal_chart"];
			unset($rec["idjournal"]);
			unset($rec["journal_xtn"]);
			unset($rec["journal_link"]);
			unset($rec["journal_chart"]);

			$c = $this->getChartFor('cash',null,null,SEARCH_FIRST);
			if (! $c)
				throw (new Exception("Unable to find chart for current asset/accounts receivable"));
			$chart1 = $c->chart_code;
			$this->createPair($rec,$chart1,$chart2,$xtn,false);

			return $this->EndTransaction();

		}
		return null;
	}

	public function AllBankTransactions($limit=0)
	{
		$ret = array();
		$limit = intval($limit);
		$bankcharts = $this->everyChartBank();
		foreach($bankcharts as $bankchart)
		{
			$r = $this->p_query("select journal_date, journal_folio, sum(journal_gross) as GROSS from journal where journal_chart = ? group by journal_date, journal_folio order by journal_date desc, journal_folio limit {$limit};","i",$bankchart["chart_code"]);
			$act["r"] = $r;
			$act["name"] = $bankchart["chart_description"];
			$ret[$bankchart["chart_code"]] = $act;
		}
		return $ret;
	}

	public function allDescriptionsForFolio($folio)
	{
		$ret = "";
		$r = $this->p_query("select journal_description from journal left join chart on chart_code = journal_chart where journal_folio = ? and chart_type = 'cash'","i",$folio);
		$recs = $r->fetch_all(MYSQLI_ASSOC);
		foreach ($recs as $rec)
		{
			$desc = htmlspecialchars($rec["journal_description"]);
			if (strlen($ret) == 0)
				$ret = $desc;
			else
			{
				if ($ret != $desc) //Same description
					$ret .= "\n" . htmlspecialchars($rec["journal_description"]);
			}
		}
		return trim($ret,"\n");
	}

	public function issueShares($strdate,$to,$qty,$price,$amount,$shareholderCurrent=0)
	{
		$ret = [0,0,0];
		$r = $this->p_create("insert into share (share_date,share_qty,share_buyprice,share_shareholder) values (?,?,?,?)","sidi",$strdate,$qty,$price,$to);
		if ($r)
			$ret[0] = $this->insert_id;


		//Get last folio
		$folio = $this->getLastFolio() + 1;

		$rec = array();

		$rec['journal_date'] = $strdate;
		$rec['journal_folio'] = $folio;
		$rec['journal_net'] = $amount;
		$rec['journal_tax'] = 0;
		$rec['journal_gross'] = $amount;
		$rec['journal_description'] = "Share purchase";

		$rec['journal_shareholder'] = $to;


		$c = $this->getChartFor('cash',null,null,SEARCH_FIRST);
		if (! $c)
			throw (new Exception("Unable to find chart for cash"));
		$chart1 = $c->chart_code;

		$c = $this->getChartFor('equity','shares',null,SEARCH_FIRST);
		if (! $c)
			throw (new Exception("Unable to find chart for equity/shares"));
		$chart2 = $c->chart_code;

		$c = $this->getChartFor('liability','shareholders',null,SEARCH_FIRST);
		if (! $c)
			throw (new Exception("Unable to find chart for equity/shares"));
		$chart3 = $c->chart_code;

		$rec["journal_source_chart"] = $chart2;

		$ret[1] = $this->createPair($rec,$chart1,$chart2,0,false);


		if ($shareholderCurrent != 0)
		{
			$rec['journal_date'] = $strdate;
			$rec['journal_net'] = $shareholderCurrent;
			$rec['journal_tax'] = 0;
			$rec['journal_gross'] = $shareholderCurrent;
			$rec['journal_description'] = "Shareholder current account";

			$rec['journal_shareholder'] = $to;
			$rec["journal_source_chart"] = $chart3;

			$ret[2] =  $this->createPair($rec,$chart1,$chart3,0,false);
		}
		return $ret;
	}

	public function PaySalesTax($strdate,$amount,$roundoff,$taxname,$taxdate,$usefolio=0)
	{
		//Get last folio if usefolio not passed.
		//If $usefolio is passed then this is a combined slaes tax bank transaction for multiple tax payments.

		if ($usefolio == 0)
			$folio = $this->getLastFolio() + 1;
		else
			$folio = $usefolio;
		$rec = array();
		$v = $amount + $roundoff;
		$rec['journal_date'] = $strdate;
		$rec['journal_folio'] = $folio;
		$rec['journal_net'] = -$v;
		$rec['journal_tax'] = 0;
		$rec['journal_gross'] = -$v;
		if ($v < 0)
			$rec['journal_description'] = "{$taxname} Refund";
		else
			$rec['journal_description'] = "{$taxname} Paid";
		$rec['journal_tax_date'] = $taxdate;

		$c = $this->getChartFor('cash',null,null,SEARCH_FIRST);
		if (! $c)
			throw (new Exception("Unable to find chart for cash"));
		$chart1 = $c->chart_code;

		$c = $this->getChartFor('tax',$taxname,null,SEARCH_FIRST);
		if (! $c)
			throw (new Exception("Unable to find chart for tax/{$taxname}"));
		$chart2 = $c->chart_code;

		$this->BeginTransaction();

		$xtn1 = $this->createPair($rec,$chart1,$chart2,0,false);
		$xtn2 = null;
		if (abs($roundoff) > 0.00)
		{
			$rec = array();
			$rec['journal_date'] = $strdate;
			$rec['journal_folio'] = $folio;
			$rec['journal_net'] = $roundoff;
			$rec['journal_tax'] = 0;
			$rec['journal_gross'] = $roundoff;
			$rec['journal_description'] = "{$taxname} Paid Roundoff";
			$rec['journal_tax_date'] = $taxdate;

			$c = $this->getChartFor('expense','operating','GST Tax rounding',SEARCH_FIRST);
			if (! $c)
				throw (new Exception("Unable to find chart for expense/{$taxname}"));
			$chart3 = $c->chart_code;

			$xtn2 = $this->createPair($rec,$chart1,$chart3,0,false);

		}

		if ($this->EndTransaction() )
			return [$xtn1,$xtn2];
		return null;
	}

	public function o_everyJournalExpense()
	{
		$ret = array();
		$r = $this->p_query("select * from journal left join chart on chart_code = journal_chart where chart_type = 'expense' order by journal_date desc", null, null);
		if ($r)
		{
			while ($a = $r->fetch_object("journal"))
				$ret[] = $a;
		}
		return $ret;
	}

	public function getSalesTaxPaid($strDate)
	{
		$company = $this->getCompany();
		$salestaxname = $company->company_sales_tax_name->raw();
		$j = $this->p_singlequery("select sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'tax' and chart_subtype = ? and journal_tax_date = ?","ss",$salestaxname,$strDate);
		if ($j)
			return floatval($j["GROSS"]);
		return 0.0;
	}

	public function gstReport($from,$to)
	{
		if (gettype($from) == "object")
			$from = $from->format("Y-m-d H:i:s");
		if (gettype($to) == "object")
			$to = $to->format("Y-m-d H:i:s");

		$company = $this->getCompany();
		if ($company->company_sales_tax_name->raw() != "GST")
			throw (new Exception("GST Report can only be run on compaines who has a sales tac name of GST"));
		$rate = ($this->getTaxRateForClassAndDate("GST",$from))->taxrate_rate;

		$ret = array();
		$detail = array();
		$lines = array();
		//Sales inccome
		$j = $this->p_singlequery("select sum(journal_gross) as GROSS from journal left join chart a on a.chart_code = journal_chart left join chart b on b.chart_code = journal_source_chart where journal_date >= ? and journal_date <= ? and a.chart_type = 'cash' and a.chart_subtype = 'bank' and b.chart_type= 'income' and b.chart_subtype = 'sale' ","ss",$from,$to);

		$detail[] = ["line" => 5,"name"=>"TOTAL SALES INCOME","value" => $j["GROSS"]];
		$lines[5] = $j["GROSS"];

		//Zero rated supplies
		$j = $this->p_singlequery("select sum(journal_gross) as GROSS from journal left join chart a on a.chart_code = journal_chart left join chart b on b.chart_code = journal_source_chart where journal_date >= ? and journal_date <= ? and a.chart_type = 'cash' and a.chart_subtype = 'bank' and b.chart_type= 'income' and b.chart_subtype = 'sale' and journal_tax = 0","ss",$from,$to);
		$detail[] = ["line" => 6,"name"=>"ZERO RATED SUPPLIES","value" => $j["GROSS"]];
		$lines[6] = $j["GROSS"];

		$lines[7] = $lines[5] - $lines[6];
		$detail[] = ["line" => 7,"name"=>"SUBTRACT BOX 6 FROM 5","value" => $lines[7] ];

		$lines[8] = round(($lines[7] * $rate) / (1+$rate),2);
		$detail[] = ["line" => 8,"name"=>"GST ON INCOME", "value" => $lines[8] ];

		$lines[9] = 0.0;
		$detail[] = ["line" => 9,"name"=>"ADJUSTMENTS","value" => $lines[9] ];

		$lines[10] = $lines[8] + $lines[9];
		$detail[] = ["line" => 10,"name"=>"	GST COLLECTED","value" => $lines[10] ];


		//Total Purchases
		$j = $this->p_singlequery("select sum(journal_gross) as GROSS from journal left join chart a on a.chart_code = journal_chart left join chart b on b.chart_code = journal_source_chart where journal_date >= ? and journal_date <= ? and a.chart_type = 'cash' and a.chart_subtype = 'bank' and (b.chart_type= 'expense' or b.chart_type= 'asset') and b.chart_taxclass is not null and journal_tax <> 0", "ss", $from, $to);
		$lines[11] = -$j["GROSS"];
		$detail[] = ["line" => 11, "name" => "TOTAL PURCHASES", "value" => $lines[11]];

		$lines[12] = round(($lines[11] * $rate) / (1 + $rate), 2);
		$detail[] = ["line" => 12, "name" => "GST ON PURCHASES", "value" => $lines[12]];

		$lines[13] = 0.00;
		$detail[] = ["line" => 12, "name" => "CREDIT ADJUSTMENTS", "value" => $lines[13]];

		$lines[14] = $lines[12];
		$detail[] = ["line" => 14, "name" => "GST CREDIT", "value" => $lines[14]];

		$lines[15] = $lines[10] - $lines[14];
		$suffix = "TO PAY";
		if ($lines[15] < 0)
			$suffix = "REFUND";
		$detail[] = ["line" => 15, "name" =>"DIFFERENCE BETWEEN BOX 10 AND 14","value" => abs($lines[15]) ,"suffix" => $suffix];

		//Corss check the journal
		$j = $this->p_singlequery("select sum(journal_tax) as TAX from journal left join chart a on a.chart_code = journal_chart where journal_date >= ? and journal_date <= ? and a.chart_type = 'cash' and a.chart_subtype = 'bank'","ss",$from,$to);
		$lines[16] = floatval($j["TAX"]);
		$detail[] = ["line" => 16,"name"=>"Cross check journal","value" => $lines[16]];

		$lines[17] = $lines[16] - $lines[15];
		$detail[] = ["line" => 17, "name"=>"Round off error","value" => $lines[17] ];

		$ret["lines"] = $lines;
		$ret["detail"] = $detail;


		return  $ret;
	}

	public function cashBalanceAt($date)
	{
		$cashbalance = array();
		$r = $this->p_query("select chart_code,chart_description, sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'cash' and journal_date <= ? group by chart_code,chart_description","s",$date);
		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			if (!isset($cashbalance[$code]))
				$cashbalance[$code] = array();
			$cashbalance[$code]["name"] = $j['chart_description'];
			$cashbalance[$code]["end"]["gross"] = $j['GROSS'];
		}
		return $cashbalance;
	}

	public function loanBalacneAt($date)
	{
		$loanbalance = array();
		$r = $this->p_query("select chart_code,chart_description, sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'non current liability' and chart_subtype = 'loan' and journal_date <= ? group by chart_code,chart_description","s",$date);
		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			if (!isset($loanbalance[$code]))
				$loanbalance[$code] = array();
			$loanbalance[$code]["name"] = $j['chart_description'];
			$loanbalance[$code]["end"]["gross"] = $j['GROSS'];
		}
		return $loanbalance;
	}

	public function cashFlow($from,$to)
	{
		$rslt = array();
		$rslt["cr"] = array();
		$rslt["dr"] = array();

		$range = array();

		$r = $this->p_query("select * from journal left join chart on chart_code = journal_chart where chart_type = 'cash' and journal_date >= ? and journal_date <= ? ", "ss", $from,$to);
		if ($r)
		{
			while ($j1 = $r->fetch_object("journal"))
			{
				//Find macthing record
				$j2 = $this->o_getJournal($j1->journal_link);

				$month = (new DateTime($j1->journal_date))->format("Ym");
				$range[$month] = (new DateTime($j1->journal_date))->format("M Y");

				$type = $j2->chart_type->raw();
				$subtype = $j2->chart_subtype->raw();

				$crdr = ($j1->journal_gross > 0) ? "cr" : "dr";

				if ($type == "current liability" && $subtype = "accounts payable")
				{
					//We need to get the orginal record
					$newchart = $this->getChart($j2->journal_source_chart);
					$type = $newchart->chart_type->raw();
					$subtype = $newchart->chart_subtype->raw();
				}

				if ($type == "current asset" && $subtype = "accounts receivable")
				{
					//We need to get the orginal record
					$newchart = $this->getChart($j2->journal_source_chart);
					$type = $newchart->chart_type->raw();
					$subtype = $newchart->chart_subtype->raw();
				}


				if (!isset($rslt [$crdr] [$type]))
					$rslt [$crdr] [$type] = array();
				if (!isset($rslt[$crdr][$type][$subtype]))
					$rslt[$crdr][$type][$subtype] = array();
				if (!isset($rslt[$crdr][$type][$subtype][$month]))
					$rslt[$crdr][$type][$subtype][$month] = 0.0;
				$rslt [$crdr] [$type] [$subtype][$month] += $j1->journal_gross;
			}
		}

		//Sort the range
		ksort($range);
		return ["data" => $rslt, "range" => $range];
	}

	public function retainedFunds($from,$to)
	{
		$v = 0.0;
		$net = 0.0;

		//Income
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'income' group by chart_code, chart_description,chart_type, chart_subtype", "ss", $from, $to);
		while ($j = $r->fetch_assoc())
		{
			$v += $j['NET'];
			$net += $j['NET'];
		}
		error_log("Income for {$from} {$to} {$v}");
		$v = 0.0;

		//Cost of sale
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'expense' and chart_subtype = 'cost of sale' group by chart_code, chart_description,chart_type, chart_subtype", "ss", $from, $to);
		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			if (!isset($costofsale[$code]))
				$costofsale[$code] = array();
			$costofsale[$code]["name"] = $j['chart_description'];
			$v += $j['NET'];
			$net += $j['NET'];
		}

		error_log("Cost of sale {$from} {$to} {$v}");
		$v = 0.0;


		//Operating Expenditure
		$expenditure = array();
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'expense' and chart_subtype = 'operating' group by chart_code, chart_description,chart_type, chart_subtype", "ss", $from, $to);
		while ($j = $r->fetch_assoc())
		{
			$v += $j['NET'];
			$net += $j['NET'];
		}
		error_log("Operating expenditure {$from} {$to} {$v}");
		$v = 0.0;


		//Financial expenditiure
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'expense' and chart_subtype = 'financial' group by chart_code, chart_description,chart_type, chart_subtype", "ss", $from, $to);
		while ($j = $r->fetch_assoc())
		{
			$v += $j['NET'];
			$net += $j['NET'];
		}
		error_log("Financial expenditiure {$from} {$to} {$v}");

		return -($net);
	}

	public function financialreport($from,$to)
	{
		//From and to are strings

		$company = $this->getCompany();
		$salestaxname = $company->company_sales_tax_name->raw();
		$ret = array();
		$ret["cash"] = array();

		//Cash balance
		$cashbalance = array();
		$r = $this->p_query("select chart_code,chart_description, sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'cash' and journal_date < ? group by chart_code,chart_description","s",$from);
		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			if (!isset($cashbalance[$code]))
				$cashbalance[$code] = array();
			$cashbalance[$code] ["name"] = $j['chart_description'];
			$cashbalance[$code] ["start"] ["gross"] = $j['GROSS'];
		}

		$balanceEnd = array();
		$r = $this->p_query("select chart_code,chart_description, sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'cash' and journal_date <= ? group by chart_code,chart_description","s",$to);
		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			if (!isset($cashbalance[$code]))
				$cashbalance[$code] = array();
			$cashbalance[$code] ["name"] = $j['chart_description'];
			if (!isset($cashbalance[$code] ["start"]))
				$cashbalance[$code] ["start"] ["gross"] = 0.0;
			$cashbalance[$code] ["end"] ["gross"] = $j['GROSS'];
		}

		$ret["cash"] ["balance"] = $cashbalance;

		/**********************************************************************************
		   CASH RECEIVED
		*/
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype,sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where journal_date >= ? and journal_date <= ? and chart_type = 'cash' and journal_gross > 0.00 group by chart_code ","ss",$from,$to);

		//$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, j.journal_chart AS LINK_CHART, sum(k.journal_gross) as GROSS from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'cash' and k.journal_gross > 0.00 group by  chart_code, chart_description,chart_type, chart_subtype, j.journal_chart","ss",$from,$to);
		$cashReceived = array();

		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			$cashReceived[$code] = array();
			$cashReceived[$code] ["name"] = $j['chart_description'];
			$cashReceived[$code] ["gross"] = $j['GROSS'];
		}

		$ret["cash"]["received"] = $cashReceived;

		//Cash spent
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype,sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where journal_date >= ? and journal_date <= ? and chart_type = 'cash' and journal_gross < 0.00 group by chart_code ", "ss", $from, $to);
		//$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, j.journal_chart AS LINK_CHART, sum(k.journal_gross) as GROSS from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'cash' and k.journal_gross < 0.00 group by chart_code, chart_description,chart_type, chart_subtype, j.journal_chart","ss",$from,$to);
		$cashSpent = array();

		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			$cashSpent[$code] = array();
			$cashSpent[$code] ["name"] = $j['chart_description'];
			$cashSpent[$code] ["gross"] = $j['GROSS'];
		}

		$ret["cash"] ["spent"] = $cashSpent;


		/***************************************************************************************************************
		 * INCOME
		*/
		$income = array();
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'income' and chart_subtype = 'sale' group by chart_code, chart_description,chart_type, chart_subtype","ss",$from,$to);
		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			if (!isset($income[$code]))
				$income[$code] = array();
			$income[$code] ["name"] = $j['chart_description'];
			$income[$code] ["net"] = $j['NET'];
		}


		$ret["income"] ["sale"] = $income;

		$incomeFinancial = array();
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'income' and chart_subtype = 'financial' group by chart_code, chart_description,chart_type, chart_subtype", "ss", $from, $to);
		while ($j = $r->fetch_assoc()) {
			$code = $j['chart_code'];
			if (!isset($incomeFinancial[$code]))
				$incomeFinancial[$code] = array();
			$incomeFinancial[$code]["name"] = $j['chart_description'];
			$incomeFinancial[$code]["net"] = $j['NET'];
		}

		$ret["income"]["financial"] = $incomeFinancial;

		/***************************************************************************************************************
		 * COST OF SALE
		 */
		$costofsale = array();
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'expense' and chart_subtype = 'cost of sale' group by chart_code, chart_description,chart_type, chart_subtype","ss",$from,$to);
		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			if (!isset($costofsale[$code]))
				$costofsale[$code] = array();
			$costofsale[$code] ["name"] = $j['chart_description'];
			$costofsale[$code] ["net"] = -($j['NET']);
		}

		$ret["costofsale"] = $costofsale;


		/***************************************************************************************************************
		 * EXPENDITURE
		 *
		 * OPERATING
		 */
		$ret["expenditure"] = array();

		$expenditure = array();
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'expense' and chart_subtype = 'operating' group by chart_code, chart_description,chart_type, chart_subtype","ss",$from,$to);
		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			if (!isset($expenditure[$code]))
				$expenditure[$code] = array();
			$expenditure[$code] ["name"] = $j['chart_description'];
			$expenditure[$code] ["net"] = -($j['NET']);
		}

		$ret["expenditure"] ["operating"] = $expenditure;

		$expenditure = array();
		$r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'expense' and chart_subtype = 'financial' group by chart_code, chart_description,chart_type, chart_subtype","ss",$from,$to);
		while ($j = $r->fetch_assoc())
		{
			$code = $j['chart_code'];
			if (!isset($expenditure[$code]))
				$expenditure[$code] = array();
			$expenditure[$code] ["name"] = $j['chart_description'];
			$expenditure[$code] ["net"] = -($j['NET']);
		}

		$ret["expenditure"] ["financial"] = $expenditure;


		 /* Financial
		 */

		/***************************************************************************************************************
		 * ASSETS
		 */
		$assets = array();

		$current_assets = array();
		$r = $this->p_query("select chart_code, chart_type, chart_description, sum(journal_net) as NET , sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_balancesheet = 'asset' and chart_balancesheet_subtype = 'current_asset' and journal_date <= ? group by chart_code,chart_type,chart_description order by chart_description","s",$to);
		while ($j = $r->fetch_assoc())
		{
			$current_assets[$j["chart_code"]] = array();
			$current_assets[$j["chart_code"]] ["name"] = $j["chart_description"];
			if ($j["chart_type"] == "cash")
				$current_assets[$j["chart_code"]] ["amt"] = $j["GROSS"];
			else
				$current_assets[$j["chart_code"]] ["amt"] = $j["NET"];
		}

		$assets["current_assets"] = $current_assets;

		$fixed_assets = array();
		$r = $this->p_query("select chart_code, chart_type, chart_description, sum(journal_net) as NET , sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_balancesheet = 'asset' and chart_balancesheet_subtype = 'fixed_asset' and journal_date <= ? group by chart_code,chart_type,chart_description order by chart_description","s",$to);
		while ($j = $r->fetch_assoc())
		{
			$fixed_assets[$j["chart_code"]] = array();
			$fixed_assets[$j["chart_code"]] ["name"] = $j["chart_description"];
			if ($j["chart_type"] == "cash")
				$fixed_assets[$j["chart_code"]] ["amt"] = $j["GROSS"];
			else
				$fixed_assets[$j["chart_code"]] ["amt"] = $j["NET"];
		}

		$assets["fixed_assets"] = $fixed_assets;

		$ret["assets"] = $assets;



		$liabilities = array();
		$current_liabilities = array();
		$r = $this->p_query("select chart_code, chart_type, chart_description, sum(journal_net) as NET , sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_balancesheet = 'liability' and chart_balancesheet_subtype = 'current_liability' and journal_date <= ? group by chart_code,chart_type,chart_description order by chart_description","s",$to);
		while ($j = $r->fetch_assoc())
		{
			$current_liabilities[$j["chart_code"]] = array();
			$current_liabilities[$j["chart_code"]] ["name"] = $j["chart_description"];
			if ($j["chart_type"] == "cash")
				$current_liabilities[$j["chart_code"]] ["amt"] = $j["GROSS"];
			else
				$current_liabilities[$j["chart_code"]] ["amt"] = $j["NET"];
		}

		//Need to add provision for andy sales TAX
		$j = $this->p_singlequery("select sum(journal_tax) as TAX from journal left join chart on chart_code = journal_chart where chart_type = 'cash' and journal_date <= ?","s",$to);
		$stax = $j["TAX"];

		//And deduct or add sum of slaes tax paid refund.
		$k = $this->p_singlequery("select sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'tax' and chart_subtype = ? and journal_date <= ?","ss",$salestaxname,$to);
		if ($k)
			$stax -= $k["GROSS"];
		$current_liabilities[10000] = array();
		$current_liabilities[10000] ["name"] = "Provision for {$company->company_sales_tax_name->toHTML()}";
		$current_liabilities[10000] ["amt"] = -$stax;

		$liabilities["current_liabilities"] = $current_liabilities;

		$non_current_liabilities = array();
		$r = $this->p_query("select chart_code, chart_type, chart_description, sum(journal_net) as NET , sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_balancesheet = 'liability' and chart_balancesheet_subtype = 'non_current_liability' and journal_date <= ? group by chart_code,chart_type,chart_description order by chart_description", "s", $to);
		while ($j = $r->fetch_assoc())
		{
			$non_current_liabilities[$j["chart_code"]] = array();
			$non_current_liabilities[$j["chart_code"]] ["name"] = $j["chart_description"];
			if ($j["chart_type"] == "cash")
				$non_current_liabilities[$j["chart_code"]] ["amt"] = $j["GROSS"];
			else
				$non_current_liabilities[$j["chart_code"]] ["amt"] = $j["NET"];
		}

		$liabilities["non_current_liabilities"] = $non_current_liabilities;


		//shareholder current accounts
		$shareholder_current = array();
		$r = $this->p_query("select journal_shareholder, shareholder_lastname, shareholder_firstnames, sum(journal_gross) as GROSS from journal left join shareholder on idshareholder = journal_shareholder left join chart on chart_code = journal_chart where chart_type = 'liability' and chart_subtype = 'shareholders' and journal_date <= ? group by journal_shareholder, shareholder_lastname, shareholder_firstnames order by shareholder_lastname, shareholder_firstnames","s",$to);
		$a = $r->fetch_all(MYSQLI_ASSOC);
		foreach($a as $j)
		{
			$idx = $j['journal_shareholder'];
			$shareholder_current[$idx] = array();
			$name = strtoupper($j['shareholder_lastname']) . ", " . $j['shareholder_firstnames'];
			$name = trim($name, ",");
			$name = trim($name);
			$shareholder_current[$idx] ['name'] = $name;
			$shareholder_current[$idx] ['amt'] = $j['GROSS'];
		}

		$liabilities["shareholder_current"] = $shareholder_current;

		$ret["liabilities"] = $liabilities;



		//Equity
		$equity = array();
		$r = $this->p_query("select chart_code, chart_subtype , chart_description, sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'equity' and journal_date <= ? group by chart_code,chart_subtype, chart_description","s",$to);
		$a = $r->fetch_all(MYSQLI_ASSOC);
		foreach($a as $j)
		{
			$code = $j['chart_code'];
			$equity[$code] = array();
			$equity[$code] ["name"] = $j['chart_description'];
			$equity[$code] ["amt"] = -$j['GROSS'];

		}

		$ret["equity"] = $equity;

		//Shares
		$shares = array();
		$r = $this->p_query("select * from share left join shareholder on idshareholder = share_shareholder order by shareholder_lastname,shareholder_firstnames,share_date", null, null);
		while ($s = $r->fetch_assoc()) {
			$pool = array();
			$pool["date"] = $s["share_date"];
			$name = strtoupper($s["shareholder_lastname"]) . ", " . $s["shareholder_firstnames"];
			$name = trim($name);
			$name = trim($name,",");
			$name = trim($name);
			$pool["name"] = $name;
			$pool["qty"] = $s["share_qty"];
			$pool["price"] = $s["share_buyprice"];
			$shares[] = $pool;
		}


		$ret["shares"] = $shares;

		return $ret;
	}

	public function getJournalVendorNameList()
	{
		$q = "select journal_vendor_name, journal_vendor_tax_number from journal where journal_vendor_name IS NOT NULL group by journal_vendor_name,journal_vendor_tax_number order by journal_vendor_name, journal_vendor_tax_number";
		$r = $this->p_query($q,null,null);
		if (!$r) {$this->sqlError($q); return null;}
		if ($r->num_rows > 0)
			return $r->fetch_all(MYSQLI_ASSOC);
		return null;
	}

	public function custAccountBalance($accountid,$strdate)
	{
		$chart1 = ($this->getChartFor("cash",null,null,SEARCH_FIRST))->chart_code;
		$chart2 = ($this->getChartFor("income","sale",null,SEARCH_ONEONLY))->chart_code;

		$j = $this->p_singlequery("select sum(journal_gross) as SUM from journal where journal_date < ? and journal_account = ? and (journal_chart = ? or journal_chart = ?)", "siii", $strdate,$accountid,$chart1, $chart2);
		if ($j["SUM"] === null)
			return 0.00;
		return $j["SUM"];
	}

	public function allCustomerTransaction($accountid,$startDate, $endDate)
	{
		$chart1 = ($this->getChartFor("cash", null, null, SEARCH_FIRST))->chart_code;
		$chart2 = ($this->getChartFor("income", "sale", null, SEARCH_ONEONLY))->chart_code;

		$ret = array();
		$r = $this->p_query("select * from journal where journal_date >= ? and journal_date < ? and journal_account = ? and (journal_chart = ? or journal_chart = ?) order by journal_date", "ssiii", $startDate, $endDate, $accountid, $chart1, $chart2);
		if ($r)
		{
			while ($o = $r->fetch_object("journal"))
			{
				$ret[] = $o;
			}
		}
		return $ret;
	}

	public function allCustomerTransaction2($accountid, $startDate, $endDate)
	{
		$chart1 = ($this->getChartFor("cash", null, null, SEARCH_FIRST))->chart_code;
		$chart2 = ($this->getChartFor("income", "sale", null, SEARCH_ONEONLY))->chart_code;

		$ret = array();
		$r = $this->p_query("select journal_date,journal_chart,journal_invoice,sum(journal_gross) as SUMGROSS from journal where journal_date >= ? and journal_date < ? and journal_account = ? and (journal_chart = ? or journal_chart = ?) group by journal_date,journal_chart,journal_invoice order by journal_date, journal_chart desc", "ssiii", $startDate, $endDate, $accountid, $chart1, $chart2);
		if ($r) {
			while ($o = $r->fetch_object("journal")) {
				$ret[] = $o;
			}
		}
		return $ret;
	}

	//*********************************************************************
	//quote functions
	//*********************************************************************
	public function getQuoteById($id)
	{
		return $this->p_singlequery( "select * from quote where idquote = ?", "i", $id);
	}

	public function o_getQuoteById($id)
	{
		return $this->o_singlequery("quote", "select * from quote where idquote = ?", "i", $id);
	}

	public function o_getQuoteByNum($quote_number)
	{
		return $this->o_singlequery("quote", "select * from quote where quote_number = ?", "i", $quote_number);
	}

	public function getNextQuoteNumber()
	{
		$r = $this->p_query("select * from quote order by quote_number desc limit 1", null, null);
		if ($r && $r->num_rows > 0)
		{
			$a = $r->fetch_array();
			return intval($a["quote_number"]) + 1;
		}
		return 1;
	}

	public function createNewQuote($num)
	{
		$this->p_create("insert into quote (quote_number) values (?)", "i", $num);
		return $this->o_getQuoteById($this->insert_id);
	}

	public function markQuoteAccepted($quoteid)
	{

		$ts = (new DateTime())->format("Y-m-d H:i:s");
		return $this->p_update("update quote set quote_status = 'accepted', quote_accepted_timestamp = ? where idquote = ?", "si", $ts,$quoteid);
	}

	public function markQuoteCompleted($quoteid)
	{
		$ts = (new DateTime())->format("Y-m-d H:i:s");
		return $this->p_update("update quote set quote_status = 'completed', quote_completed_timestamp = ? where idquote = ?", "si", $ts,$quoteid);
	}

	public function markQuoteDeclined($quoteid)
	{
		$ts = (new DateTime())->format("Y-m-d H:i:s");
		return $this->p_update("update quote set quote_status = 'declined', quote_declined_timestamp = ? where idquote = ?", "si", $ts,$quoteid);
	}

	public function o_everyQuoteLine($quoteid)
	{
		$ret = array();
		$r = $this->p_query("select * from quote_line where quote_line_quote = ? order by idquote_line", "i", $quoteid);
		if ($r)
		{
			while ($a = $r->fetch_object("quote_line"))
				$ret[] = $a;
		}
		return $ret;
	}

	public function netSumQuoteLines($quoteid)
	{
		$sum = 0;
		$r = $this->p_query("select * from quote_line where quote_line_quote = ? order by idquote_line", "i", $quoteid);
		if ($r)
		{
			while ($a = $r->fetch_object("quote_line"))
				$sum += $a->quote_line_cost;
		}
		return $sum;

	}

	public function allQuotes()
	{
		return $this->p_query("select * from quote where quote_deleted = 0 order by quote_number", null, null);
	}

	public function allQuotesDesc()
	{
		return $this->p_query("select * from quote where quote_deleted = 0 order by quote_number desc", null, null);
	}

	public function allAcceptedQuotesWithAccountDesc()
	{
		return $this->p_query("select * from quote where quote_deleted = 0 and quote_status = 'accepted' and quote_customer_account IS NOT null order by quote_number desc", null, null);
	}

	public function deleteAllQuoteLinesForQuote($id)
	{
		return $this->p_delete("delete from quote_line where quote_line_quote =  ?", "i", $id);
	}

	public function countAllAcceptedQuotes()
	{
		$r = $this->p_query("select count(*) as CNT from quote where quote_status = 'accepted'", null, null);
		if ($r) {
			$s = $r->fetch_assoc();
			return intval($s["CNT"]);
		}
		return 0;
	}

	public function netSumAllAcceptedQuotes()
	{
		$r = $this->p_query("select sum(quote_line_cost) as SUM from quote_line left join quote on idquote = quote_line_quote where quote_status = 'accepted'", null, null);
		if ($r)
		{
			$s = $r->fetch_assoc();
			return floatval($s["SUM"]);
		}
		return 0.00;
	}

	//*********************************************************************
	//quote_request functions
	//*********************************************************************
	public function createQuoteRequest($name,$phone,$addr1,$addr2,$addr3,$addr4)
	{
		$strDate = (new DateTime())->format("Y-m-d");
		$r = $this->p_create("insert into quote_request (quote_request_date,quote_request_name,quote_request_phone,quote_request_addreess1,quote_request_addreess2,quote_request_addreess3,quote_request_addreess4) values (?,?,?,?,?,?,?)", "sssssss", $strDate,$name, $phone, $addr1, $addr2, $addr3, $addr4);
		if ($r)
			return $this->insert_id;
		return false;
	}

	public function LastQuoteRequests()
	{
		$ret = array();
		$r = $this->p_query("select * from quote_request order by idquote_request desc limit 100", null, null);
		if($r)
		{
			while ($o = $r->fetch_object("quote_request"))
				$ret[] = $o;
		}
		return $ret;
	}

	//*********************************************************************
	//shareholder functions
	//*********************************************************************
	public function createShareHolder($lastname,$firstname)
	{
		$r = $this->p_create("insert into shareholder (shareholder_lastname,shareholder_firstnames) values (?,?)", "ss", $lastname, $firstname);
		if ($r)
			return $this->insert_id;
		return false;
	}

	public function allShareholders($where,$order)
	{
		return $this->p_query("select * from shareholder {$where} {$order}",null,null);
	}

	//*********************************************************************
	// audit functions
	//*********************************************************************
	public function createAudit($type,$description,$userid=null)
	{
		if ($userid)
			return $this->p_create("insert into audit (audit_type,audit_description,audit_user) value (?,?,?)","ssi",$type,$description,$userid);
		else
			return $this->p_create("insert into audit (audit_type,audit_description) value (?,?)","ss",$type,$description);
	}

	public function allAudits($limit=null)
	{
		if ($limit === null)
			return $this->p_query("select * from audit left join user on iduser = audit_user order by audit_timestamp desc",null,null);
		else
			return $this->p_query("select * from audit left join user on iduser = audit_user order by audit_timestamp desc limit {$limit}", null, null);
	}

	public function allAuditsByType($type)
	{
		return $this->p_query("select * from audit where audit_type = ? order by audit_timestamp","s",$type);
	}

	//*********************************************************************
	// static functions
	//*********************************************************************
	public static function createRandomPW($length = 6)
	{
		$p = '';
		$characters = '23456789abcdefghjkmnprstuwxyzABCDEFGHJKLMNPQRSTUWXYZ';
		for($i = 0 ; $i < $length; $i++)
		{
			$p .= substr($characters, rand(0,51) , 1);
		}
		return strval($p);
	}

}

?>