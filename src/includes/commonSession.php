<?php
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

$glb = $DB->getGlobal();
$undolist = new UndoList($user->user_undolist->raw());
$undolist->removeOldTime($glb->global_undolist_max_age);
$undolist->removeOldCount($glb->global_undolist_max_depth);

if ($session->session_key)
    $_SESSION["session_key"] = $session->session_key;
$session->last = $selff;
?>