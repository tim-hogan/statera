<?php
session_start();
require dirname(__FILE__) . "/includes/classstateraDB.php";
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/commonSession.php";
require dirname(__FILE__) . "/includes/classInputParam.php";


function var_error_log($object = null, $text = '')
{
    ob_start();
    var_dump($object);
    $contents = ob_get_contents();
    ob_end_clean();
    error_log("{$text} {$contents}");
}


$urlpart = InputParam::encryptFromString("w=cash", $session->session_key);
echo "<p>$urlpart</p>";

$inputParams = null;
$inputParams = InputParam::load($urlpart, $session->session_key);

echo "<p>";
var_dump($inputParams);
echo "</p>";


echo "<p>Done</p>";
?>