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


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (!$session->checkCSRF())
	{
		$DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
		header("Location: SecurityError.php");
		exit();
	}

	$username = FormList::getField("username");
	$lastname = FormList::getField("lastname");
	$firstname = FormList::getField("firstname");
	$email = FormList::getField("email");
	$salt = Secure::createSalt();
	$SEC = new Secure();
	$randpw = Secure::createRandomPW();
	$hash = $SEC->passwordHash($randpw, $salt);
	$DB->createUserWithEmail($username, $lastname, $firstname, $hash, $salt, SECURITY_ADMIN, "Pacific/Auckland",$email);
    $DB->createAudit("security", "New user created for username {$username}", $user->iduser);
	
	echo "New user created temp password: {$randpw}";
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width" />
	<meta name="viewport" content="initial-scale=1.0" />
	<title>CREATE USER</title>
	<link href="css/base.css" rel="stylesheet" />
	<link href="css/heading.css" rel="stylesheet" />
	<link href="css/menu.css" rel="stylesheet" />
	<link href="css/footer.css" rel="stylesheet" />
	<style>
		#main {margin: 20px;}
		#form {padding: 20px;border: solid 1px #888;border-radius: 8px;}
		#heading1 h1 {color: #6b6ba7;font-family: Akshar;font-weight: 300;}
	</style>
</head>
<body>
	<div id="container">
		<?php include ("./includes/heading.html");?>
		<?php include ("./includes/menu.html");?>
		<div id="main">
			<div id="heading1">
				<h1>CREATE NEW USER</h1>
			</div>
		</div>
		<div id="form">
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<div class="formfield">
					<label for="username">USERNAME</label>
					<input id="username" type="text" name="username" size="15" />
				</div>
				<div class="formfield">
					<label for="lastname"LAST NAME</label>
					<input id="lastname" type="text" name="lastname" size="15" />
				</div>
				<div class="formfield">
					<label for="firstname"LAST NAME</label>
					<input id="firstname" type="text" name="firstname" size="15" />
				</div>
				 <div class="formfield">
					<label for="email"LAST NAME</label>
					<input id="email" type="email" name="email" size="30" />
				</div>
			   <?php echo "<input type='hidden' name='formtoken' value='{$session->csrf_key}'>"; ?>
				<button>CREATE NEW USER </button>
			</form>
		</div>
	</div>
</body>
</html>