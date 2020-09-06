<?php
require("base.inc.php");
$max = (int) $_REQUEST['max'];
$defaultlimit = 100;
$totalfish = (int) $db->getone("SELECT COUNT(*) FROM fish");

if ($max == -1) {
	$limit = $totalfish;
} elseif ($max > 0) { // 
	$limit = $max;
} else {
	$limit = $defaultlimit;
}

$fish = $db->getall("
	SELECT fish.id, capture_date, river, section, fishec, species_code, species.name_latin, species.name_english, standard_length, total_length, weight, pit_id, comments, fish_origin, hatchery, parental_origin, sex, ripeness, recapture, laketrout, original_site_name
	FROM fish
	LEFT JOIN species ON fish.species_code = species.code
	ORDER BY capture_date DESC, id DESC
	LIMIT ?
", [ $limit ] );

print htmlstart("Fish");

print "<p>Showing " . ($max == -1 ? "all ($limit)" : $limit) . " out of $totalfish fish. (show <a href=\"fish.php?max=100\">100</a>, <a href=\"fish.php?max=1000\">1000</a>, <a href=\"fish.php?max=-1\">all</a>)</p>" . PHP_EOL;

print '<table border="1" cellspacing="0" cellpadding="3">' . PHP_EOL;
print '<thead><tr><th>Date</th><th>River</th><th>Section</th><th>Fishec</th><th>Species</th><th>Standard length (mm)</th><th>Total length (mm)</th><th>Weight (g)</th><th>PIT ID</th><th>Comments</th><th>Fish origin</th><th>Hatchery</th><th>Parental origin</th><th>Sex</th><th>Ripeness</th><th>Recapture</th><th>Laketrout</th><th>Original site name</th></tr></thead>' . PHP_EOL;

print '<tbody>' . PHP_EOL;
foreach($fish AS $f) {
	print "<tr>";
	print "<td>" . $f['capture_date'] . "</td>";
	print "<td>" . $f['river'] . "</td>";
	print "<td class=\"number\">" . $f['section'] . "</td>";
	print "<td>" . $f['fishec'] . "</td>";
	print "<td>" . ($f['name_english'] ? '<span title="' . $f['species_code'] . '">' . $f['name_english'] . '</span>' : '<span title="Unknown species">' . $f['species_code'] . ' ⚠</span>') . "</td>";
	print "<td class=\"number\">" . (int) $f['standard_length'] . "</td>";
	print "<td class=\"number\">" . (int) $f['total_length'] . "</td>";
	print "<td class=\"number\">" . $f['weight'] . "</td>";
	print "<td>" . $f['pit_id'] . "</td>";
	print "<td>" . $f['comments'] . "</td>";
	print "<td>" . $f['fish_origin'] . "</td>";
	print "<td>" . $f['hatchery'] . "</td>";
	print "<td>" . $f['parental_origin'] . "</td>";
	print "<td>" . $f['sex'] . "</td>";
	print "<td>" . $f['ripeness'] . "</td>";
	print "<td>" . $f['recapture'] . "</td>";
	print "<td>" . $f['laketrout'] . "</td>";
	print "<td>" . $f['original_site_name'] . "</td>";
	print "</tr>";

}

print '</tbody></table>';

print htmlend();

?>
