<?php
ini_set('display_errors',"1");
ini_set('memory_limit','1024M');
require("base.inc.php");

$db->SetFetchMode(ADODB_FETCH_ASSOC);

$timing = [];
$timing['start'] = time();

$antennas = (array) ($_REQUEST['antenna_id'] ?? []);
$antenna_from = (string) ($_REQUEST['antenna_from'] ?? '');
$antenna_to = (string) ($_REQUEST['antenna_to'] ?? '');
$tagtime_from = (string) ($_REQUEST['tagtime_from'] ?? '');
$tagtime_to = (string) ($_REQUEST['tagtime_to'] ?? '');
$species_id = (array) ($_REQUEST['species_id'] ?? []);
$species_size_min = (float) ($_REQUEST['species_size_min'] ?? 0);
$species_size_max = (float) ($_REQUEST['species_size_max'] ?? 0);

$fish_origins = (array) ($_REQUEST['fish_origins'] ?? []);
$hatcheries = (array) ($_REQUEST['hatcheries'] ?? []);
$parental_origins = (array) ($_REQUEST['parental_origins'] ?? []);

$rivers = (array) ($_REQUEST['rivers'] ?? []);
$sections = (array) ($_REQUEST['sections'] ?? []);

$timestep = (int) ($_REQUEST['timestep'] ?? 0);

$debug = isset($_REQUEST['debug']);

if ($debug) {
	$db->debug = true;
}

if ($use_timestep) {
	if ($timestep < 1 || $timestep > 24) {
		die("Wrong timestep");
	}
}

$sql_where = [];
$sql_tagging_where = [];

if ($antennas) {
	$sql_where[] = "antennas.id IN (" . implode(",", array_map("intval", $antennas) ) . ")";
}

if ($antenna_from) {
	$sql_where[] = "observations.date >= '" . ($antenna_from) . "'";
}

if ($antenna_to) {
	$sql_where[] = "observations.date <= '" . ($antenna_to) . "'";
}

if ($tagtime_from) {
	$sql_where[] = "fish.capture_date >= '" . ($tagtime_from) . "'";
	$sql_tagging_where[] = "fish.capture_date >= '" . ($tagtime_from) . "'";
}

if ($tagtime_to) {
	$sql_where[] = "fish.capture_date <= '" . ($tagtime_to) . "'";
	$sql_tagging_where[] = "fish.capture_date <= '" . ($tagtime_to) . "'";
}

if ($species_id) {
	$sql_where[] = "species.id IN (" . implode(",", array_map("intval", $species_id) ) . ")";
	$sql_tagging_where[] = "species.id IN (" . implode(",", array_map("intval", $species_id) ) . ")";
}

if ($species_size_min) {
	$sql_where[] = "fish.total_length >= " . $species_size_min;
	$sql_tagging_where[] = "fish.total_length >= " . $species_size_min;
}

if ($species_size_max) {
	$sql_where[] = "fish.total_length <= " . $species_size_max;
	$sql_tagging_where[] = "fish.total_length <= " . $species_size_max;
}

if ($fish_origins) {
	$sql_where[] = "fish.fish_origin IN(" . implode(",", array_map("dbquote", $fish_origins) ) . ")";
	$sql_tagging_where[] = "fish.fish_origin IN(" . implode(",", array_map("dbquote", $fish_origins) ) . ")";
}

if ($hatcheries) {
	$sql_where[] = "fish.hatchery IN(" . implode(",", array_map("dbquote", $hatcheries) ) . ")";
	$sql_tagging_where[] = "fish.hatchery IN(" . implode(",", array_map("dbquote", $hatcheries) ) . ")";
}

if ($parental_origins) {
	$sql_where[] = "fish.parental_origin IN(" . implode(",", array_map("dbquote", $parental_origins) ) . ")";
	$sql_tagging_where[] = "fish.parental_origin IN(" . implode(",", array_map("dbquote", $parental_origins) ) . ")";
}

if ($rivers) {
	$sql_where[] = "fish.river IN(" . implode(",", array_map("dbquote", $rivers) ) . ")";
	$sql_tagging_where[] = "fish.river IN(" . implode(",", array_map("dbquote", $rivers) ) . ")";
}

if ($sections) {
	$parts = [];
	foreach($sections AS $section) {
		list($river, $part) = explode(",", $section);
		$parts[] = "(fish.river = " . dbquote($river) . " AND fish.section = " . dbquote($part) . ")";
	}
	$sql_where[] = "(" . implode(" OR ", $parts) . ")";
	$sql_tagging_where[] = "(" . implode(" OR ", $parts) . ")";
}

// count possible fish
$tagging_query = "
	SELECT COUNT(DISTINCT pit_id) FROM fish
	INNER JOIN species ON fish.species_code = species.code
";

if ($sql_tagging_where) {
	$tagging_query .= " WHERE " . implode(" AND ",$sql_tagging_where);
}

$fishpossible = $db->getone($tagging_query);

$timing['sqlfish'] = time();

// observations query
// :TODO: Do not select *

if ($use_timestep) { // timereport
	$fields = "original_pit_id";
} else { // individual
	$fields = "observations.pit_id";
}

$query = "
	SELECT original_pit_id, antenna_local, date, time, time_fraction FROM observations
	INNER JOIN antennas ON observations.site_code = antennas.code
	INNER JOIN fish ON observations.pit_id = fish.pit_id
	INNER JOIN species ON fish.species_code = species.code
";

if ($sql_where) {
	$query .= " WHERE " . implode(" AND ",$sql_where);
}

$query .= "
	ORDER BY observations.date, observations.time, observations.time_fraction
";

if ($debug) {
	print "<pre>Query:\n" . htmlspecialchars($query) . "\n</pre>";
	print "<pre>Memory usage:\n" . memory_get_usage() . "\n</pre>";
	flush();
}

$result = $db->getall($query);

if ($debug) {
	print "<pre>Query done!</pre>";
	print "<pre>Memory usage:\n" . memory_get_usage() . "\n</pre>";
	flush();
}

$timing['sql'] = time();
?>

