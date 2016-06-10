<?php

require getcwd().DIRECTORY_SEPARATOR.'bootstrap.php';

date_default_timezone_set('GMT');

$datatime   = new \DateTime();
$hash = "51+c0172ea66506f59c8c435eb66176fb67+2090+1510+6+";

$hour        = $datatime->format("G");
$minutes     = $datatime->format("i");
$day_of_year = $datatime->format("z") + 1;

$day_in_utc1 = $day_of_year;
$day_in_utc2 = $day_of_year;

if ($hour == "23" && $minutes > "49") $day_in_utc2 = $day_in_utc1 + 1;
if ($hour == "00" && $minutes < "11") $day_in_utc2 = $day_in_utc1 - 1;

$hash1 = hash("sha512", ($hash . $day_in_utc1));
$hash2 = hash("sha512", ($hash . $day_in_utc2));

// Hash decalarion for only one game
define('HASH1',$hash1);
define('HASH2',$hash2);