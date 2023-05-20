<?php
require "./includes/classAccounts.php";

$start = 2;
$cadence = 12;

$d = new DateTime("2022-01-01 00:00:00");
for ($i = 0; $i < 365; $i++)
{
    $se = AccountDate::startEndForDate($d->format('Y-m-d'),$cadence,$start);
    echo $d->format("d/m/Y") . " {$se[0]} {$se[1]} <br/>";
    $d->add(new DateInterval("P1D"));
}

?>