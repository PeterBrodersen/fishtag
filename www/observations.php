<?php
require("base.inc.php");

$defaultlimit = 100;
$max = (int) $_REQUEST['max'] ?? 0;
$totalobservations = (int) $db->getone("SELECT COUNT(*) FROM observations");
if ($max >= 1 && $max <= 10000) {
	$limit = $max;
} else {
	$limit = $defaultlimit;
}
$data = $db->getall ("
	SELECT a.id, a.file_id, a.pit_id, a.date, a.time, b.river, c.filename, d.name_english, e.location
	FROM observations a
	INNER JOIN fish b ON a.pit_id = b.pit_id
	LEFT JOIN files c ON a.file_id = c.id
	LEFT JOIN species d ON b.species_code = d.code
	LEFT JOIN antennas e ON a.site_code = e.code
	ORDER BY a.date DESC, a.time DESC, a.time_fraction DESC
	LIMIT ?
", [ $limit ] );

print htmlstart("Observations");

print "<p>Showing " . ($max == -1 ? "all ($limit)" : $limit) . " newest out of $totalobservations observations based on timestamp. (show <a href=\"observations.php?max=100\">100</a>, <a href=\"observations.php?max=1000\">1000</a>)</p>" . PHP_EOL;

print '<table border="1" cellspacing="0" cellpadding="3">';
print '<tr><th>ID</th><th>pit_id</th><th>date</th><th>time</th><th>River</th><th>Filename</th><th>Species name</th><th>Location</th></tr>';
foreach($data AS $row) {
	print "<tr>";
	foreach ($row AS $name => $field) {
		if ($name === 'filename') {
			print '<td><a href="files.php#file_' . $row['file_id'] . '">' . htmlspecialchars($field) . '</a></td>';
		} elseif (!is_numeric($name) && $name != 'file_id' ){
			print "<td>" . htmlspecialchars($field) . "</td>";
		}
	}
	print "</tr>";
}
print "</table>";

print htmlend();
?>
