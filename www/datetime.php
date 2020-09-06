<?php
print "<pre>";
$d = new DateTime("2015-03-08 12:34:56.7890123");
$d2 = new DateTime("2015-03-08 12:34:56.7890123");
$d2->add(new DateInterval('PT1H'));
print $d->format("Y-m-d H:i:s.u");
print PHP_EOL;
print $d2->format("Y-m-d 00:00:00");
var_dump($d2 < $d);
