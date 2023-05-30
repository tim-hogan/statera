<?php
require_once  './includes/classAccounts.php';

echo "Testing addMonths\n";

$month = 1;
echo "Testing from month 1 by adding 1 month\n";
for($i = 0;$i < 12;$i++)
{
    echo "{$month} ";
    $month = AccountDate::addMonths($month,1);
    echo "{$month}\n";
}

$month = 1;
echo "Testing from month 1 by adding 2 months\n";
for($i = 0;$i < 12;$i++)
{
    echo "{$month} ";
    $month = AccountDate::addMonths($month,2);
    echo "{$month}\n";
}

$month = 1;
echo "Testing from month 1 by adding 5 months\n";
for($i = 0;$i < 12;$i++)
{
    echo "{$month} ";
    $month = AccountDate::addMonths($month,5);
    echo "{$month}\n";
}

$month = 1;
echo "Testing from month 1 by suntracting 1 months\n";
for($i = 0;$i < 12;$i++)
{
    echo "{$month} ";
    $month = AccountDate::subMonths($month,1);
    echo "{$month}\n";
}

$month = 1;
echo "Testing from month 1 by suntracting 2 months\n";
for($i = 0;$i < 12;$i++)
{
    echo "{$month} ";
    $month = AccountDate::subMonths($month,2);
    echo "{$month}\n";
}

$month = 1;
echo "Testing from month 1 by suntracting 5 months\n";
for($i = 0;$i < 12;$i++)
{
    echo "{$month} ";
    $month = AccountDate::subMonths($month,5);
    echo "{$month}\n";
}

?>