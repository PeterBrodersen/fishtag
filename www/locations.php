<?php
// :TODO: Optimize locations, add observation count field
require("base.inc.php");

$getcount = (int) ($_REQUEST['getcount'] ?? 0);

if ($getcount == 1) {
	$sites = $db->getall("
		SELECT antennas.id, location, code, COUNT(fish.id) AS count
		FROM antennas
		LEFT JOIN observations ON antennas.code = observations.site_code
		LEFT JOIN fish ON observations.pit_id = fish.pit_id
		GROUP BY antennas.id
		ORDER BY antennas.id 
	");
} else {
	$sites = $db->getall("
		SELECT antennas.id, location, code, NULL AS count
		FROM antennas
	");
}

print htmlstart("Locations");

print '<table border="1" cellspacing="0" cellpadding="3">' . PHP_EOL;
print '<tr><th>ID</th><th>Location</th><th>Code</th>';
if ($getcount == 1 ) {
	print '<th>Observations</th>';
}
print '</tr>' . PHP_EOL;

foreach($sites AS $site) {
	print "<tr>";
	print "<td>" . $site['id'] . "</td>";
	print "<td>" . $site['location'] . "</td>";
	print "<td>" . $site['code'] . "</td>";
	if ($getcount == 1) {
		print "<td class=\"number\">" . $site['count'] . "</td>";
	}
	print "</tr>" . PHP_EOL;
}

print "</table>";

print "<p>";
print "<a href=\"locations.php?getcount=1\">Show observation count</a><br>";
print "Note: This operation is slow. Expect a couple of minutes waiting time.";
print "</p>" . PHP_EOL;

print htmlend();

?>
