<?php
require("base.inc.php");

function arrayToList($array) {
	$text = [];
	foreach($array AS $k => $v) {
		$text[] = "$k: ". number_format($v);
	}
	$text = implode("; ", $text);
	return $text;
}

print htmlstart();

// move to new "check integrity" PHP file or optimize otherwise
#$orphan_file_ids = $db->getone("SELECT GROUP_CONCAT(DISTINCT file_id) AS file_ids from observations left join files on observations.file_id = files.id WHERE files.id IS NULL");


// $observations = $db->getassoc("SELECT tag_type, COUNT(*) AS c FROM observations GROUP BY tag_type ORDER BY c DESC");

print "<p><a href=\"https://docs.google.com/document/d/1jP_lBUpE3OyW4I7WEiXuFDmrbxoW1yGV0u3lzqZsEJE/edit?usp=sharing\">Work document</a></p>\n";

print "<h2>Summary:</h2>" . PHP_EOL;


print "<ul>";
#print "<li>Observations: " . number_format(array_sum($observations) ) . " (" . arrayToList($observations) . ")</li>" . PHP_EOL;
print "<li>Observations: " . number_format($db->getone("SELECT COUNT(*) FROM observations") ) . "</li>" . PHP_EOL;
print "<li>Fish: " . number_format($db->getone("SELECT COUNT(*) FROM fish") ) . "</li>" . PHP_EOL;
print "<li>Antennas (sites): " . $db->getone("SELECT COUNT(*) FROM antennas") . "</li>" . PHP_EOL;
print "<li>Species: " . $db->getone("SELECT COUNT(*) FROM species") . "</li>" . PHP_EOL;
print "<li>Files: " . $db->getone("SELECT COUNT(*) FROM files") . "</li>" . PHP_EOL;
/*
if ($orphan_file_ids) {
	print "<li class=\"error\">Error: Non-existent file ids mentioned in observations: " . $orphan_file_ids . " (should be blank)</li>" . PHP_EOL;
}
*/
print "</ul>";

print htmlend();

?>
