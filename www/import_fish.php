<?php
//die("Safety valve. Exiting!" . PHP_EOL);

# 2017 july : data should be utf-8
# some pit ids are too long? 7 characters

require("connect.inc.php");
$file = "../data/tagging.csv";

$separator = ",";

$db->debug = false;

if (($fp = fopen($file, "r")) === FALSE) {
	die("Can't open file");
}
$dummy = fgetcsv($fp, 1000, $separator); // :TODO: Check column names and match them to corresponding fields in database instead of assuming position

$db->query("CREATE TABLE IF NOT EXISTS tmp_fish LIKE fish");
$db->query("TRUNCATE TABLE tmp_fish");

$count = 0;
while (($data = fgetcsv($fp, 1000, $separator)) !== FALSE) {
	$count++;
	if ($count % 1000 === 0) {
		print $count . " rows" . PHP_EOL;
	}
# :TODO: This should auto-check the format. mm/dd/yyyy or dd-mm-yyyy
/*
	list($day,$month,$year) = explode(".", $data[0]);
*/
	list($month,$day,$year) = explode("/", $data[0]);
	$ymd = sprintf("%04d-%02d-%02d", $year, $month, $day);
	$river = $data[4];
	$section = $data[5];
	$fishec = $data[6];
	$species = $data[7];
	$sl = $data[8];
	$tl = $data[9];
	$weight = $data[10];
	$pit_id = substr($data[11], -6);
	$original_pit_id = $data[11];
	$comments = $data[12];
	$fish_origin = $data[18];
	$hatchery = $data[19];
	$parental_origin = $data[20];
	$sex = $data[21];
	$ripeness = $data[22];
	$recapture = $data[23];
	$laketrout = $data[24];
	$original_site_name = $data[25];
	if ($fishec == "" || $fishec == "-") $fishec = NULL;
	if ($sl == "" || $sl == "-") $sl = NULL;
	if ($tl == "" || $tl == "-") $tl = NULL;
	if ($weight == "" || $weight == "-") $weight = NULL;
	#	print $pit . PHP_EOL;
	$db->query("INSERT INTO tmp_fish (capture_date, river, section, fishec, species_code, standard_length, total_length, weight, pit_id, original_pit_id, comments, fish_origin, hatchery, parental_origin, sex, ripeness, recapture, laketrout, original_site_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [ $ymd, $river, $section, $fishec, $species, $sl, $tl, $weight, $pit_id, $original_pit_id, $comments, $fish_origin, $hatchery, $parental_origin, $sex, $ripeness, $recapture, $laketrout, $original_site_name ] );
	$error = $db->errorMsg();
	if ($error) {
		print "Row $count: " . $error . PHP_EOL;
	}
	
}
$db->query("DROP TABLE IF EXISTS old_fish");
$db->query("RENAME TABLE fish TO old_fish, tmp_fish to fish");
?>

