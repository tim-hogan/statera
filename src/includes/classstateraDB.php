<?php
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
                    "company_sales_tax_cadence" =>["type" => "int"],
                    "company_sales_tax_first_month"=>["type" => "int"]
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
                    "user_remember_me" =>["type" => "boolean"],
                    "user_session_key" =>["type" => "varchar"],
                    "user_session_data" =>["type" => "varchar"],
                    "user_undolist" =>["type" => "varchar"]
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
                    "taxrate_comments"=>["type" => "varchar"]
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
                    "idaccount" =>["type" => "int"],
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
                    "invoice_line_total_cost"=>["type" => "double"]
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
                    "journal_description"=>["type" => "varchar"],
                    "journal_net"=>["type" => "double"],
                    "journal_tax"=>["type" => "double"],
                    "journal_gross"=>["type" => "double"],
                    "journal_link"=>["type" => "int"],
                    "journal_source"=>["type" => "int"],
                    "journal_source_chart"=>["type" => "int"],
                    "journal_folio"=>["type" => "int"],
                    "journal_account"=>["type" => "int"],
                    "journal_invoice"=>["type" => "int"],
                    "journal_asset"=>["type"  => "int"],
                    "journal_tax_date"=>["type" => "date"],
                    "journal_shareholder"=>["type" => "int"],
                    "journal_vendor_name"=>["type" => "varchar"],
                    "journal_vendor_tax_number"=>["type" => "varchar"]
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
                    "shareholder_lastname" =>["type" => "varchar"],
                    "shareholder_firstnames" =>["type" => "varchar"]
                ]
            );
    }
}

class share extends TableRow
{
    function __construct($tabledata=null)
    {
        if ($tabledata)
            parent::__construct($tabledata);
        else
            parent::__construct
            (
                [
                    "idshare" =>["type" => "int"],
                    "share_date" =>["type" => "date"],
                    "share_qty" =>["type" => "int"],
                    "share_buyprice" =>["type" => "double"],
                    "share_shareholder" =>["type" => "int"]
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

    public function updateRolling($name,$count,$values)
    {
        return $this->p_update("update rolling set rolling_idx = ?, rolling_counters = ? where rolling_entity = ?","iss",$count,$values,$name);
    }

    public function resetRolling($name)
    {
        return $this->p_update("update rolling set rolling_entity_disabled = 0 where rolling_entity = ?","s",$name);
    }

    public function markRollingDisabled($name)
    {
        $dt = new DateTime('now');
        $strTime = $dt->format('Y-m-d H:i:s');
        return $this->p_update("update rolling set rolling_disabled = 1, rolling_disable_timestamp = ? where rolling_entity = ?","ss",$strTime,$name);
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
        return $this->o_singlequery("user","select * from user where user_randid = ?","s",$randid);
    }

    public function createUser($username,$lastname,$firstname,$hash,$salt,$security,$timezone)
    {
        $randid = bin2hex(openssl_random_pseudo_bytes(8));
        $session_key = bin2hex(openssl_random_pseudo_bytes(32));
        return $this->p_create("insert into user (user_randid,user_username,user_lastname,user_firstname,user_forcereset,user_hash,user_salt,user_security,user_timezone,user_session_key) values ('{$randid}',?,?,?,1,?,?,?,?,'{$session_key}')","sssssis",$username,$lastname,$firstname,$hash,$salt,$security,$timezone);
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
        return $this->p_update("update user set user_undolist = ? where iduser = ?","si",$list,$iduser);
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
    // Company functions
    //*********************************************************************
    public function getCompany()
    {
        return $this->o_singlequery("company","select * from company limit 1",null,null);
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
            return $this->o_singlequery("taxrate","select * from taxrate where taxrate_taxclass = ? and taxrate_from_date < ? order by taxrate_from_date desc limit 1","is",$taxclass,$strDate);
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

    public function getChartFor($type,$subtype=null,$options=0)
    {
        if ($subtype)
            $r = $this->p_query("select * from chart where chart_type = ? and chart_subtype = ? order by chart_code","ss",$type,$subtype);
        else
            $r = $this->p_query("select * from chart where chart_type = ? order by chart_code","s",$type);

        if ($r->num_rows > 0)
        {
            if (($options & SEARCH_ONEONLY) &&  $r->num_rows > 1)
                return null;
            return $r->fetch_object("chart");
        }
        return null;
    }

    public function everyChartBank()
    {
        $r = $this->p_query("select * from chart where chart_type = 'cash' and chart_subtype = 'bank' order by chart_code",null,null);
        if (!$r) {$this->sqlError($q); return null;}
        if ($r->num_rows > 0)
            return $r->fetch_all(MYSQLI_ASSOC);
        return null;
    }

    public function everyChartExpense()
    {
        $r = $this->p_query("select * from chart left join taxclass on idtaxclass = chart_taxclass where chart_type = 'expense' order by chart_description",null,null);
        if (!$r) {$this->sqlError($q); return null;}
        if ($r->num_rows > 0)
            return $r->fetch_all(MYSQLI_ASSOC);
        return null;
    }

    public function everyChartExpenseAndAsset()
    {
        $r = $this->p_query("select * from chart left join taxclass on idtaxclass = chart_taxclass where chart_type = 'expense' or chart_type = 'asset' order by chart_description",null,null);
        if (!$r) {$this->sqlError($q); return null;}
        if ($r->num_rows > 0)
            return $r->fetch_all(MYSQLI_ASSOC);
        return null;
    }

    //*********************************************************************
    // account functions
    //*********************************************************************
    public function getAccount($id)
    {
        return $this->o_singlequery("account","select * from account where idaccount = ?","i",$id);
    }

    public function allAccounts($where='',$order='')
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
        return $this->o_singlequery("invoice_line","select * from invoice_line where idinvoice_line = ?","i",$id);
    }

    public function createInvoiceLine($invoiceid,$productid,$description,$qty,$unitdesc,$unit,$total)
    {
        if (!$productid)
            $productid = null;
        $r = $this->p_create("insert into invoice_line (invoice_line_invoice,invoice_line_product,invoice_line_description,invoice_line_qty,invoice_line_unit_desc,invoice_line_unit_cost,invoice_line_total_cost) values (?,?,?,?,?,?,?)","iisdsdd",$invoiceid,$productid,$description,$qty,$unitdesc,$unit,$total);
        if ($r)
        {
            return $this->getInvoiceLine($this->insert_id);
        }
        return null;
    }

    public function everyInvoiceLine($idinvoice)
    {
        $idinvoice = intval($idinvoice);
        return $this->every("invoice_line","where invoice_line_invoice = {$idinvoice}","order by idinvoice_line");
    }

    public function sumInvoiceLines($idinvoice)
    {
        return $this->p_singlequery("select sum(invoice_line_total_cost) as NET from invoice_line where invoice_line_invoice = ?","i",$idinvoice);
    }

    //*********************************************************************
    // journal functions
    //*********************************************************************
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
            $c = $this->getChartFor('cash',null,SEARCH_FIRST);
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
            $c = $this->getChartFor('current asset',"accounts receivable",SEARCH_FIRST);
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

    public function expensePaid($strdate,$description,$ledgerAmount,$vendname,$vendtax,$chart1=0,$chart2=0,$enterTransaction=true)
    {

        //Get last folio
        $folio = $this->getLastFolio() + 1;
        $rec1 = array();
        $rec1['journal_date'] = $strdate;
        if (strlen($description) == 0)
            $rec1['journal_description'] = "EXPENSE PAID";
        else
            $rec1['journal_description'] = $description;
        $rec1['journal_net'] = -($ledgerAmount->net);
        $rec1['journal_tax'] = -($ledgerAmount->tax);
        $rec1['journal_gross'] = -($ledgerAmount->gross);

        $rec1['journal_folio'] = $folio;
        $rec1['journal_vendor_name'] = $vendname;
        $rec1['journal_vendor_tax_number'] = $vendtax;

        //Now find coa
        if (!$chart1)
        {
            $c = $this->getChartFor('cash',null,SEARCH_FIRST);
            if (! $c)
                throw (new Exception("Unable to find chart for current asset/accounts receivable"));
            $chart1 = $c->chart_code;
        }
        if (!$chart2)
        {
            $c = $this->getChartFor('expense',null,SEARCH_FIRST);
            if (! $c)
                throw (new Exception("Unable to find chart for current income/sale"));
            $chart2 = $c->chart_code;
        }
        $rec1["journal_source_chart"] = $chart2;

        return $this->createPair($rec1,$chart1,$chart2,0,$enterTransaction);
    }

    public function expenseUnPaid($strdate,$description,$ledgerAmount,$vendname,$vendtax,$chart1=0,$chart2=0,$enterTransaction=true)
    {

        //Get last folio
        $folio = $this->getLastFolio() + 1;
        $rec1 = array();
        $rec1['journal_date'] = $strdate;
        if (strlen($description) == 0)
            $rec1['journal_description'] = "ACCRUED EXPENSE";
        else
            $rec1['journal_description'] = $description;
        $rec1['journal_net'] = -($ledgerAmount->net);
        $rec1['journal_tax'] = -($ledgerAmount->tax);
        $rec1['journal_gross'] = -($ledgerAmount->gross);

        $rec1['journal_folio'] = $folio;
        $rec1['journal_vendor_name'] = $vendname;
        $rec1['journal_vendor_tax_number'] = $vendtax;

        //Now find coa
        if (!$chart1)
        {
            $c = $this->getChartFor('current liability','accounts payable',SEARCH_FIRST);
            if (! $c)
                throw (new Exception("Unable to find chart for current asset/accounts receivable"));
            $chart1 = $c->chart_code;
        }
        if (!$chart2)
        {
            $c = $this->getChartFor('expense',null,SEARCH_FIRST);
            if (! $c)
                throw (new Exception("Unable to find chart for current income/sale"));
            $chart2 = $c->chart_code;
        }
        $rec1["journal_source_chart"] = $chart2;

        return $this->createPair($rec1,$chart1,$chart2,0,$enterTransaction);
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
        $c = $this->getChartFor('current liability','accounts payable',SEARCH_FIRST);
        if ($c)
        {
            $code = $c->chart_code;
            return $this->p_singlequery("select * from journal where journal_chart = {$code} and journal_xtn = ?","i",$xtn);
        }
    }

    public function payAccountsPayable($xtn,$amount)
    {
        $rec = $this->getAccountPayable($xtn);
        if ($rec)
        {
            $chart2 = $rec["journal_chart"];
            unset($rec["idjournal"]);
            unset($rec["journal_xtn"]);
            unset($rec["journal_link"]);
            unset($rec["journal_chart"]);

            $c = $this->getChartFor('cash',null,SEARCH_FIRST);
            if (! $c)
                throw (new Exception("Unable to find chart for current asset/accounts receivable"));
            $chart1 = $c->chart_code;
            return $this->createPair($rec,$chart1,$chart2,$xtn);
        }
        return null;
    }

    public function everyAccountsPayable()
    {
        $r = $this->p_query("select * from journal left join chart on chart_code = journal_chart where chart_type = 'current liability' and chart_subtype = 'accounts payable' and journal_gross < 0 order by journal_date",null,null);
        if (!$r) {$this->sqlError($q); return null;}
        if ($r->num_rows > 0)
        {
            $list = $r->fetch_all(MYSQLI_ASSOC);
            for($idx = 0;$idx < count($list); $idx++)
            {
                $match = $this->p_singlequery("select sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'current liability' and chart_subtype = 'accounts payable' and journal_gross > 0 and journal_source = {$list[$idx] ['journal_source']}",null,null);
                $list[$idx] ["journal_gross"] = $list[$idx] ["journal_gross"] + $match["GROSS"];
                if ( $list[$idx] ["journal_gross"]  == 0.0)
                    unset($list[$idx]);
            }
            return $list;
        }
        return null;
    }

    public function getAccountReceivable($xtn)
    {
        $c = $this->getChartFor('current asset','accounts receivable',SEARCH_FIRST);
        if ($c)
        {
            $code = $c->chart_code;
            return $this->p_singlequery("select * from journal where journal_chart = {$code} and journal_xtn = ?","i",$xtn);
        }
    }

    public function everyAccountsReceivable()
    {
        $r = $this->p_query("select * from journal left join chart on chart_code = journal_chart where chart_type = 'current asset' and chart_subtype = 'accounts receivable' and journal_gross > 0 order by journal_date",null,null);
        if (!$r) {$this->sqlError($q); return null;}
        if ($r->num_rows > 0)
        {
            $list = $r->fetch_all(MYSQLI_ASSOC);
            for($idx = 0;$idx < count($list); $idx++)
            {
                $match = $this->p_singlequery("select sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where chart_type = 'current asset' and chart_subtype = 'accounts receivable' and journal_gross < 0 and journal_source = {$list[$idx] ['journal_source']}",null,null);
                $list[$idx] ["journal_gross"] = $list[$idx] ["journal_gross"] + $match["GROSS"];
                if ( $list[$idx] ["journal_gross"]  == 0.0)
                    unset($list[$idx]);
            }
            return $list;
        }
        return null;

    }

    public function payAccountsReceivable($xtn,$amount)
    {
        $rec = $this->getAccountReceivable($xtn);
        if ($rec)
        {
            $chart2 = $rec["journal_chart"];
            unset($rec["idjournal"]);
            unset($rec["journal_xtn"]);
            unset($rec["journal_link"]);
            unset($rec["journal_chart"]);

            $c = $this->getChartFor('cash',null,SEARCH_FIRST);
            if (! $c)
                throw (new Exception("Unable to find chart for current asset/accounts receivable"));
            $chart1 = $c->chart_code;
            return $this->createPair($rec,$chart1,$chart2,$xtn);
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
        foreach($recs as $rec)
            $ret .= htmlspecialchars($rec["journal_description"]) . "\n";
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


        $c = $this->getChartFor('cash',null,SEARCH_FIRST);
        if (! $c)
            throw (new Exception("Unable to find chart for cash"));
        $chart1 = $c->chart_code;

        $c = $this->getChartFor('equity','shares',SEARCH_FIRST);
        if (! $c)
            throw (new Exception("Unable to find chart for equity/shares"));
        $chart2 = $c->chart_code;

        $c = $this->getChartFor('liability','shareholders',SEARCH_FIRST);
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

    public function PaySalesTax($strdate,$amount,$roundoff,$taxname,$taxdate)
    {
        //Get last folio
        $folio = $this->getLastFolio() + 1;

        $rec = array();
        $v = $amount + $roundoff;
        $rec['journal_date'] = $strdate;
        $rec['journal_folio'] = $folio;
        $rec['journal_net'] = -$v;
        $rec['journal_tax'] = 0;
        $rec['journal_gross'] = -$v;
        $rec['journal_description'] = "{$taxname} Paid";
        $rec['journal_tax_date'] = $taxdate;

        $c = $this->getChartFor('cash',null,SEARCH_FIRST);
        if (! $c)
            throw (new Exception("Unable to find chart for cash"));
        $chart1 = $c->chart_code;

        $c = $this->getChartFor('tax',$taxname,SEARCH_FIRST);
        if (! $c)
            throw (new Exception("Unable to find chart for tax/{$taxname}"));
        $chart2 = $c->chart_code;

        $this->BeginTransaction();

        $xtn1 = $this->createPair($rec,$chart1,$chart2,0,false);
        $xtn2 = null;
        if ($roundoff > 0)
        {
            $rec = array();
            $rec['journal_date'] = $strdate;
            $rec['journal_folio'] = $folio;
            $rec['journal_net'] = $roundoff;
            $rec['journal_tax'] = 0;
            $rec['journal_gross'] = $roundoff;
            $rec['journal_description'] = "{$taxname} Paid Roundoff";
            $rec['journal_tax_date'] = $taxdate;

            $c = $this->getChartFor('expense','GST Tax rounding',SEARCH_FIRST);
            if (! $c)
                throw (new Exception("Unable to find chart for expense/{$taxname}"));
            $chart3 = $c->chart_code;

            $xtn2 = $this->createPair($rec,$chart1,$chart3,0,false);

        }

        if ($this->EndTransaction() )
            return [$xtn1,$xtn2];
        return null;
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
        $detail[] = ["line" => 8,"name"=>"GST ON INCOME","value" => $lines[8] ];

        $lines[9] = 0.0;
        $detail[] = ["line" => 9,"name"=>"ADJUSTMENTS","value" => $lines[9] ];

        $lines[10] = $lines[8] + $lines[9];
        $detail[] = ["line" => 10,"name"=>"	GST COLLECTED","value" => $lines[10] ];


        //Total Purchases
        $j = $this->p_singlequery("select sum(journal_gross) as GROSS from journal left join chart a on a.chart_code = journal_chart left join chart b on b.chart_code = journal_source_chart where journal_date >= ? and journal_date <= ? and a.chart_type = 'cash' and a.chart_subtype = 'bank' and b.chart_type= 'expense' and b.chart_taxclass is not null","ss",$from,$to);
        $lines[11] = -$j["GROSS"];
        $detail[] = ["line" => 11,"name"=>"TOTAL PURCHASES","value" => $lines[11] ];

        $lines[12] = round(($lines[11] * $rate) / (1+$rate),2);
        $detail[] = ["line" => 12,"name"=>"GST ON PURCHASES","value" => $lines[12] ];

        $lines[13] = 0.00;
        $detail[] = ["line" => 12,"name"=>"CREDIT ADJUSTMENTS","value" => $lines[13] ];

        $lines[14] =$lines[12];
        $detail[] = ["line" => 14,"name"=>"GST CREDIT","value" => $lines[14] ];

        $lines[15] = $lines[10] - $lines[14];
        $suffix = "TO PAY";
        if ($lines[15] < 0)
            $suffix = "REFUND";
        $detail[] = ["line" => 15,"name"=>"DIFFERENCE BETWEEN BOX 10 AND 14","value" => abs($lines[15]) ,"suffix" => $suffix];

        //Corss check the journal
        $j = $this->p_singlequery("select sum(journal_tax) as TAX from journal left join chart a on a.chart_code = journal_chart where journal_date >= ? and journal_date <= ? and a.chart_type = 'cash' and a.chart_subtype = 'bank'","ss",$from,$to);
        $lines[16] = floatval($j["TAX"]);
        $detail[] = ["line" => 16,"name"=>"Cross check journal","value" => $lines[16]];

        $lines[17] = $lines[16] - $lines[15];
        $detail[] = ["line" => 17,"name"=>"Round off error","value" => $lines[17] ];

        $ret["lines"] = $lines;
        $ret["detail"] = $detail;


        return  $ret;
    }

    public function financialreport($from,$to)
    {
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

        $ret["cash"] ["received"] = $cashReceived;

        //Cash spent
        $r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype,sum(journal_gross) as GROSS from journal left join chart on chart_code = journal_chart where journal_date >= ? and journal_date <= ? and chart_type = 'cash' and journal_gross < 0.00 group by chart_code ","ss",$from,$to);
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
        $r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'income' group by chart_code, chart_description,chart_type, chart_subtype","ss",$from,$to);
        while ($j = $r->fetch_assoc())
        {
            $code = $j['chart_code'];
            if (!isset($income[$code]))
                $income[$code] = array();
            $income[$code] ["name"] = $j['chart_description'];
            $income[$code] ["net"] = $j['NET'];
        }

        $ret["income"] = $income;

        /***************************************************************************************************************
         * COST OF SALE
         */
        $costofsale = array();
        $ret["costofsale"] = $costofsale;


        /***************************************************************************************************************
         * EXPENDITURE
         */
        $expenditure = array();
        $r = $this->p_query("select chart_code, chart_description,chart_type, chart_subtype, sum(k.journal_net) as NET from journal as k left join chart on chart_code = journal_chart left join journal as j on j.idjournal = k.journal_link where k.journal_date >= ? and k.journal_date <= ? and chart_type = 'expense' group by chart_code, chart_description,chart_type, chart_subtype","ss",$from,$to);
        while ($j = $r->fetch_assoc())
        {
            $code = $j['chart_code'];
            if (!isset($expenditure[$code]))
                $expenditure[$code] = array();
            $expenditure[$code] ["name"] = $j['chart_description'];
            $expenditure[$code] ["net"] = -($j['NET']);
        }

        $ret["expenditure"] = $expenditure;

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


        //shareholder current accounts
        $shareholder_current = array();
        $r = $this->p_query("select journal_shareholder, shareholder_lastname, shareholder_firstnames, sum(journal_gross) as GROSS from journal left join shareholder on idshareholder = journal_shareholder left join chart on chart_code = journal_chart where chart_type = 'liability' and chart_subtype = 'shareholders' and journal_date <= ? group by journal_shareholder, shareholder_lastname, shareholder_firstnames order by shareholder_lastname, shareholder_firstnames","s",$to);
        $a = $r->fetch_all(MYSQLI_ASSOC);
        foreach($a as $j)
        {
            $idx = $j['journal_shareholder'];
            $shareholder_current[$idx] = array();
            $name = strtoupper($j['shareholder_lastname']) . ", " . $j['shareholder_firstnames'];
            $name = trim($name,",");
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
        $r = $this->p_query("select * from share left join shareholder on idshareholder = share_shareholder order by shareholder_lastname,shareholder_firstnames,share_date",null,null);
        while ($s = $r->fetch_assoc())
        {
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
        $r = $this->p_query("select journal_vendor_name, journal_vendor_tax_number from journal where journal_vendor_name IS NOT NULL group by journal_vendor_name,journal_vendor_tax_number order by journal_vendor_name, journal_vendor_tax_number",null,null);
        if (!$r) {$this->sqlError($q); return null;}
        if ($r->num_rows > 0)
            return $r->fetch_all(MYSQLI_ASSOC);
        return null;
    }

    //*********************************************************************
    //shareholder functions
    //*********************************************************************
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