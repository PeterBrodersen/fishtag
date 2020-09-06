<?php
require("base.inc.php");

$files = $db->getall("
	SELECT files.id, filename, import_time, antenna_code, observations, imported, import_result, deleted, delete_time, antennas.location
	FROM files
	LEFT JOIN antennas ON files.antenna_code = antennas.code
	ORDER BY id DESC
");

print htmlstart("Files");

print "<form enctype=\"multipart/form-data\" action=\"import_cap.php\" method=\"post\">";
print "<p>Upload CAP file: ";
print '<input type="hidden" name="' . ini_get("session.upload_progress.name") . '" value="myfile" />';
print "<input type=\"file\" name=\"capfile\">";
print "<input type=\"submit\" value=\"Upload\">";
print "</p>";
print "</form>\n\n";

print '<table class="filetable">' . PHP_EOL;
print '<thead><tr><th>ID</th><th>Filename</th><th>Import date</th><th>Antenna code</th><th>Observations</th><th>Imported?</th><th>Import note</th><th>Delete</th></tr></thead>' . PHP_EOL;

foreach($files AS $file) {
	$location = ($file['location'] ? $file['location'] : "?");
	print "<tr" . ($file['deleted'] == 1 ? " class=\"deleted\"" : "") .">";
	print "<td class=\"number\">" . $file['id'] . "</td>";
	print "<td>" . $file['filename'] . "</td>";
	print "<td>" . $file['import_time'] . "</td>";
	print "<td title=\"" . $location . "\">" . $file['antenna_code'] . ($file['location'] ? '' : ' <span title="Unknown antenna">⚠</span>') . "</td>";
	print "<td class=\"number\">" . $file['observations'] . "</td>";
	print "<td class=\"symbol\">" . ($file['imported'] ? "✓" : "") . "</td>";
	print "<td>" . $file['import_result'] . "</td>";
	if ($file['deleted'] == 0) {
		print "<td><a href=\"#\" onclick=\"if(confirm('Delete?'))window.location='import_cap.php?action=delete&amp;file_id=" . $file['id']."';\">[delete]</a></td>";
	} else {
		print "<td><span title=\"" . $file['delete_time'] . "\">Deleted</span></td>";
	}
	print "</tr>" . PHP_EOL;
}

print "</table>\n\n";


print htmlend();

?>
