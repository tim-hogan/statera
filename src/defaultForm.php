<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
require dirname(__FILE__) . "/includes/classFormList2.php";
$formdata = require("./config/StateraForm.php");
$tablename = "taxclass";

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

$pageData = ['select' => $tablename, 'form' => ['display' => false, 'mode' => "", 'recid' => ""] ];
$formspecificdata = array();

FormList::handleGet($pageData);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (!$session->checkCSRF())
    {
        $DB->createAudit("security","{$selff} [" .__LINE__. "] Invalid CSRF on form input");
        header("Location: SecurityError.php");
        exit();
    }
    FormList::handlePost($DB,$formdata,$pageData);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta name="viewport" content="initial-scale=1.0" />
    <title>FORM</title>
    <link href="css/base.css" rel="stylesheet" />
    <link href="css/list.css" rel="stylesheet" />
    <link href="css/form.css" rel="stylesheet" />
    <link href="css/footer.css" rel="stylesheet" />
    <style>
    </style>
    <script>

    function deleteButtonChange(tbl) {
        let but = document.getElementById("del" + tbl);
        let l = document.getElementsByClassName('listcheck' + tbl);
        but.disabled = true;
        for (let i = 0; i < l.length; i++) {
            if (l[i].checked) {
                but.disabled = false;
            }
        }
    }

    </script>
</head>
<body>
    <div id="container">
        <div id="heading">

        </div>
        <div id="main">
                <?php
                if (! $pageData['form'] ['display'])
                {
                    echo "<div id='list'>";
                    FormList::buildPanel($DB,null,$tablename,$formdata,false);
                    echo "</div>";
                }
                if ($pageData['form'] ['display'])
                {
                    echo "<div class='formouter'>";
                    $session->createNewCSRF();
                    FormList::buildForm($DB,$formspecificdata,$tablename,$formdata,$pageData);
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
    <?php include ("./includes/footer.html");?>
</body>
</html>
