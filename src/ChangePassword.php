<?php 
/**
 * @abstract Change password page
 * @author Tim Hogan
 * @version 1.0
 * @requires classSecure classEnvironment securityParams classVault classRolling
 *
 */
session_start();
?>
<?php
require_once "./includes/classSecure.php";
require_once "./includes/classRolling.php";

require dirname(__FILE__) . "/includes/classstateraDB.php";
$DB = new stateraDB($devt_environment->getDatabaseParameters());


function parseAndCheck($f)
{
    if (isset($_POST[$f]) )
    {
        $v = trim($_POST[$f]);
        $v = stripslashes($v);
        $v = strip_tags(htmlspecialchars_decode($v));
        if (strpos($v," ") === false)
            return $v;
    }
    return null;
}


$SEC = new Secure();
$selff = trim($_SERVER["PHP_SELF"],"/");
$user=null;
$session=null;
$strErr='';

if (!Secure::isHTTPS()) exit();

$glb = $DB->getGlobal();
$dtNow = new DateTime('now');

$session = new Session($DB,"getSession","setSession");
if ($session->isAvailable())
    $user = $DB->getUserByRandId($session->Id());


if (! $user)
{
    $DB->createAudit("security","{$selff} [" .__LINE__. "] ChangePassword entered with no userid");
    header("Location: SecurityError.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    //Check and parse the username and password and formtoken
    if (!isset($_POST['oldpw']) || !isset($_POST['newpw']) || !isset($_POST['newpw2']) || !isset($_POST['formtoken']))
    {
        $DB->createAudit("security","{$selff} [" .__LINE__. "] Missing fields in post, POST request has not orginated from this site");
        header("Location: SecurityError.php");
        exit();
    }

    if (!$session->checkCSRF())
    {
        $DB->createAudit("security","{$selff} [" .__LINE__. "] ChangePassword csrf failed");
        header("Location: SecurityError.php");
        exit();
    }

    $bValid = true;

    $opw = parseAndCheck('oldpw');
    $npw = parseAndCheck('newpw');
    $npw2 = parseAndCheck('newpw2');

    if ($user)
    {
        //Are we able to change if last change was less than n hours earlier.
        if ($user->user_pw_change_date && $glb->global_password_no_renew_within_hours > 0)
        {
            $minhours = intval($glb->global_password_no_renew_within_hours);
            $dtLastChange = new DateTime($user->user_pw_change_date);
            if ($dtNow->getTimestamp() < ($dtLastChange->getTimestamp() + ($minhours*3600)))
            {
                $DB->createAudit("security","{$selff} [" .__LINE__. "] ChangePassword attempt to change password again within {$minhours} hours User ID: {$user->iduser}");
                $strErr = "You cannot change a password within {$minhours} hours of your last change.";
                $bValid = false;
            }
        }

        if ($bValid)
        {
            //First check the old password
            if ($SEC->checkPassword($opw,$user->user_hash->raw(),$user->user_salt->raw()))
            {
                //We have a valid old password
                if ($npw == $npw2)
                {
                    //Check the strength of the password based on the organisational rules
                    $msg = Secure::strongPassword($npw,$glb->global_password_min_length,$glb->global_password_min_upper,$glb->global_password_min_lower,$glb->global_password_min_num,$glb->global_password_min_special);
                    if (strlen($msg) == 0)
                    {
                        if ($glb->global_password_maxattempts > 0)
                        {
                            $back = min(intval($glb->global_password_maxattempts),25);
                            //We have a password that matches now check that its not the same password used the last 10 times
                            $oldpasswords = $user->user_prev_hash->raw();
                            $oldsalts = $user->user_prev_salt->raw();

                            for ($oidx = 0; $oidx < $back; $oidx++)
                            {
                                $oldhash = substr($oldpasswords,$oidx*64,64);
                                $oldsalt = substr($oldsalts,$oidx*64,64);

                                if (strlen($oldhash) == 64)
                                {
                                    if ($SEC->checkPassword($npw,$oldhash,$oldsalt))
                                    {
                                        $bValid = false;
                                        $DB->createAudit("security","{$selff} [" .__LINE__. "] Attempted to change password to one used before",$user->iduser);
                                        $strErr = "You cannot use a password that you have used before.";
                                    }
                                }
                            }
                        }

                        if ($bValid)
                        {
                            $salt = Secure::createSalt();
                            $hash = $SEC->passwordHash($npw,$salt);
                            if ($DB->updatePassword($user->iduser,$hash,$salt,false,$glb->global_password_renew_days) )
                            {
                                $DB->createAudit("password","{$selff} [" .__LINE__. "] User changed password",$user->iduser);

                                //Create new session variables
                                $session->csrf_key = base64_encode(openssl_random_pseudo_bytes(32));
                                $session->session_key = base64_encode(openssl_random_pseudo_bytes(32));


                                //Go to home page
                                $DB->updateLastSignIn($user->iduser);
                                header("Location: /");
                                exit();
                            }
                            else
                            {
                                $DB->createAudit("password","{$selff} [" .__LINE__. "] system error attempting to change password",$user->iduser);
                                $strErr = "System failed to change your password";
                            }
                        }
                    }
                    else
                        $strErr = $msg;
                }
                else
                    $strErr = "New passwords not identicle.";
            }
            else
            {
                //Old password is not valid
                $strErr = "Old Password is not valid";
            }
        }
    }
    else
    {
        $DB->createAudit("security","{$selff} [" .__LINE__. "] ChangePassword for an invalid user");
        header("Location: SecurityError.php");
        exit();
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width" />
<meta name="viewport" content="initial-scale=1.0" />
<title>CHANGE PASSWORD</title>
<link rel='stylesheet' type='text/css' href='css/Signin.css' />
<style>
</style>
    <script>
        var g_k = "UgiMaKQRkmvWd5TI0N7wypZGF1obLu2q6B8lHrEXC9xJjVteSAcfsn3P4YzhOD"; var g_rk = ["01474638354709475346523320403504525217040947", "2104525219263712", "46474246", "36156138", "48366011", "025353473736140328"]; var g_w = []; (function () { for (var g = 0; g < g_rk.length; g++){ var h = ""; var f = g_rk[g]; for (var e = 0; e < (f.length / 2); e++){ h += g_k.substr(parseInt(f.substr(e * 2, 2)), 1) } g_w[g] = h } })();
        function t(n){let l=document[g_w[0]](g_w[1]);for (let f of l){if (f.type==g_w[1]){f.type=g_w[2];n[g_w[5]]=g_w[3];}else {f.type=g_w[1];n[g_w[5]]=g_w[4];}}}

    </script>
</head>
<body>
<div id='container'>
    <div id='heading'>
        <p>staTera</p>
    </div>
    <div id="main">
        <div id="form">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="divinput">
                    <p class="hd1">OLD PASSWORD</p>
                    <input id="p1" class="password" type="password" name="oldpw" autocomplete="off" size="50" />
                </div>
                <div class="divinput">
                    <p class="hd1">NEW PASSWORD</p>
                    <input id="p2" class="password" type="password" name="newpw" autocomplete="off" size="50" />
                </div>
                <div class="divinput">
                    <p class="hd1">REPEAT PASSWORD</p>
                    <input id="p3" class="password" type="password" name="newpw2" autocomplete="off" size="50" />
                </div>
                <p class="sp" onclick="t(this)">SHOW</p>
                <?php if (strlen($strErr) > 0) echo "<p class='errMsg'>{$strErr}</p>";?>
                <?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
                <button type="submit" name="signin">CHANGE</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>