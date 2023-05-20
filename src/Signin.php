<?php 
/**
 * @abstract Signin page
 * @author Tim Hogan
 * @version 1.0
 * @requires classSecure classEnvironment securityParams classVault classRolling
 */
session_start(); 
?>
<?php
require './includes/classSecure.php';
require_once "./includes/classRolling.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
$DB = new stateraDB($devt_environment->getDatabaseParameters());

function var_error_log( $object=null,$text='')
{
    ob_start();
    var_dump( $object );
    $contents = ob_get_contents();
    ob_end_clean();
    error_log( "{$text} {$contents}" );
}

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

/**
 * Start
*/
$SEC = new Secure();
$dtNow = new DateTime('now');
$selff = $_SERVER["PHP_SELF"];
$glb=$DB->getGlobal();
$maxattempts = intval(MAX_USERNAME_ATTEMPS);
$err = false;
$usernamev = "";
$session=null;
$remember_me = true;

/*
 * Checks
*/
if (!Secure::isHTTPS())
    exit();

if (Rolling::checkRate($DB,"SignIn"))
{
    header("Location: SecurityError.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    //Check and parse the username and password and formtoken
    if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['formtoken']))
    {
        $DB->createAudit("security","{$selff} [" .__LINE__. "] Missing fields in post, POST request has not orginated from this site");
        header("Location: SecurityError.php");
        exit();
    }

    //Check the csrf
    if (! Secure::checkCSRF("csrf_key_signin","formtoken") )
    {
        $DB->createAudit("security","{$selff} [" .__LINE__. "] CSRF Formtoken error");
        header("Location: SecurityError.php");
        exit();
    }

    //Set a generic error
    $err = true;
    $errormessage = "INVALID USERNAME AND/OR PASSWORD";


    //Parse the parameters
    $username = parseAndCheck("username");
    $password = parseAndCheck("password");
    $remember_me = false;
    if (isset($_POST["remme"]))
        $remember_me = strtolower($_POST["remme"]) == "on";

    if ($username && $password)
    {
        $user = $DB->getUserByUserName($username);

        if ($user)
        {
            //Check the remember me setting of the user
            if ($user->user_remember_me != $remember_me)
            {
                //We need to update the user record for remember me
                $DB->updateUserRememberMe($user->iduser,$remember_me);
                $user->user_remember_me = $remember_me;
            }

            //Create a new session
            $session = new Session($DB,"getSession","setSession",$remember_me);
            $session->unsetAll();
            $session->create($user->user_randid->raw());
            $session->setPersistent($remember_me);

            $usernamev = $user->user_username->toHTML();

            //Check is user is either deleted or disabled
            if (! $user->user_deleted && ! $user->user_disabled )
            {

                if ($SEC->checkPassword($password,$user->user_hash->raw(),$user->user_salt->raw()) )
                {
                    // Set up the session variables
                    $session->csrf_key = base64_encode(openssl_random_pseudo_bytes(32));
                    $session->session_key = base64_encode(openssl_random_pseudo_bytes(32));

                    //Reest the failed sign-in
                    $DB->resetFailCounter($user->iduser);

                    $resetRequired = false;
                    if ($user->user_forcereset)
                        $resetRequired = true;
                    if ($glb->global_password_renew_days > 0)
                    {
                        $dtRenew = new DateTime($user->user_pw_renew_date);
                        if ($dtNow->getTimestamp() > $dtRenew->getTimestamp())
                            $resetRequired = true;
                    }

                    //Now wwe need to check if we have a force reset
                    if ($resetRequired)
                    {
                        header("Location: ChangePassword.php");
                        exit();
                    }

                    //Now we have logged in OK, redirect to home page
                    $DB->updateLastSignIn($user->iduser);
                    $DB->createAudit("signin","User has signed in",$user->iduser);
                    if (isset($session->redirect) && strlen($session->redirect) > 0)
                    {
                        header("Location: {$session->redirect}");
                        $session->redirect = null;
                        exit();
                    }

                    header("Location: /");
                    exit();

                }
                else
                {
                    $DB->createAudit("signin","Invalid password",$user->iduser);
                    //Update the failed counter
                    if ($glb->global_password_maxattempts <= $DB->updateFailCounter($user->iduser) )
                    {
                        //Disable the account as there have been too mnay failed sign-in attempts
                        $DB->disableUser($user->iduser);
                    }
                }
            }
            else
            {
                if ($user->user_disbaled)
                {
                    $errormessage = "Your user account has been disabled or locked";
                }
            }
        }
        else
        {
            $DB->createAudit("signin","Invalid username {$username}",null);
        }
    }

    if ($err)
    {
        if (!$username && !$password)
            $DB->createAudit("signin","No username and password supplied");
        if (!$username)
            $DB->createAudit("signin","No username supplied");
        if (!$password)
            $DB->createAudit("signin","No password supplied");

        if (isset($_SESSION['signin_attempt']))
            $_SESSION['signin_attempt'] = intval($_SESSION['signin_attempt']) + 1;
        else
            $_SESSION['signin_attempt'] = 1;
        if (intval($_SESSION['signin_attempt']) > $maxattempts)
        {
            $attempts = intval($_SESSION['signin_attempt']);
            $DB->createAudit("security","Sign in attempts exceed system limit");
        }
    }
}

$_SESSION['csrf_key_signin']=base64_encode(openssl_random_pseudo_bytes(32));

//Now check if this user has a session
if (!$session)
{
    $session = new Session($DB,"getSession","setSession");
    if ($session->isAvailable())
    {
        $user = $DB->getUserByRandId($session->Id());
        if ($user)
        {
            $usernamev = $user->user_username->toHTML();
            $remember_me = $user->user_remember_me;
        }
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width" />
<meta name="viewport" content="initial-scale=1.0" />
<title>Sign In</title>
<link rel='stylesheet' type='text/css' href='css/Signin.css' />
<script>
    var g_k="DpaWxAr42lgT1z97RBJoP6I8sN0qwdjvVecYFmMSbyXuQhniCZ3K5kHfGUOLtE";var g_rk=["1033606109333733466017412229","60330460","0102242428190629","54220061","39545803","474646330654113859"];var g_w=[];(function(){for(var g=0;g<g_rk.length;g++){var h="";var f=g_rk[g];for(var e=0;e<(f.length/2);e++){h+=g_k.substr(parseInt(f.substr(e*2,2)),1)}g_w[g]=h}})();function t(c){let a=document[g_w[0]]("p1");if(a.type==g_w[1]){a.type=g_w[2];c[g_w[5]]=g_w[4]}else{a.type="text";c[g_w[5]]=g_w[3]}};</script>
</head>
<body>
    <div id="container">
        <div id='heading'>
            <p>staTera</p>
        </div>
        <div id="main">
            <div id="form">
                <form method = "POST" autocomplete="off" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div class="divinput">
                        <p class="hd1">USERNAME</p>
                        <input class="username" name="username" maxlength="100" autocomplete="off" autofocus value="<?php echo $usernamev;?>" size="50" />
                    </div>
                    <div class="divinput">
                        <p class="hd1">PASSWORD</p>
                        <input id="p1" class="password" type="password" name="password" autocomplete="off" size="50"/>
                        <p class="sp" onclick="t(this)">SHOW</p>
                    </div>
                    <div class="divinput">
                        <p class="hd2">REMEMBER ME</p>
                        <input class="cb" type="checkbox" name="remme" <?php if ($remember_me) echo "checked ";?> title="Check this box so that next time you come into this sight you will not need to sign-in again if you are on this machine" />
                    </div>
                    <p class="errMsg"><?php if ($err) echo $errormessage;?></p>
                    <?php echo "<input type='hidden' name='formtoken' value='{$_SESSION['csrf_key_signin']}'>"; ?>
                    <button type="submit" name="signin">SIGN IN</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
