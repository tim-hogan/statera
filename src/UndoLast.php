<?php
session_start();
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classRolling.php";
require dirname(__FILE__) . "/includes/classstateraDB.php";
require dirname(__FILE__) . "/includes/classFormList2.php";

function var_error_log( $object=null , $text='')
{
    ob_start();
    var_dump( $object );
    $contents = ob_get_contents();
    ob_end_clean();
    error_log( "{$text} {$contents}" );
}

require dirname(__FILE__) . "/includes/commonSession.php";

$count = 1;
if (isset($_GET["n"]))
    $count = intval($_GET["n"]);

for ($i = 0; $i < $count;$i++)
    ($undolist->pop())->undo($DB);
$DB->updateUndoList($user->iduser,$undolist->toJSON());
header("Location: /");

?>