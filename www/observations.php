<?php
require("base.inc.php");

$query = "
SELECT a.id, a.pit_id, a.date, a.time, b.river, c.filename, d.name_english, e.location
FROM observations a
INNER JOIN fish b ON a.pit_id = b.pit_id
LEFT JOIN files c ON a.file_id = c.id
LEFT JOIN species d ON b.species_code = d.code
LEFT JOIN antennas e ON a.site_code = e.code
ORDER BY a.date, a.time, a.time_fraction
";

$data = $db->getall($query);

print htmlstart("Observations");

print "<p>Observations: " . count($data) . "</p>";

print '<table border="1" cellspacing="0" cellpadding="3">';
print '<tr><th>ID</th><th>pit_id</th><th>date</th><th>time</th><th>River</th><th>Filename</th><th>Species name</th><th>Location</th></tr>';
foreach($data AS $row) {
	print "<tr>";
	foreach ($row AS $name => $field) {
		if (!is_numeric($name) ){
			print "<td>" . htmlspecialchars($field) . "</td>";
		}
	}
	print "</tr>";
}
print "</table>";

print htmlend();
?>
