<?php
require("connect.inc.php");
$db->debug = true;

$upload_path = "../data/imported/";
$action = $_REQUEST['action'];
$file_id = (int) $_REQUEST['file_id'];

if ($file_id) {
	$row = $db->getrow("SELECT id, imported, deleted, delete_time FROM files WHERE id = ?", [$file_id]);
	if (!$row) {
		die("File ID $file_id does not exist.");
	}
	if ($row['deleted'] == 1) {
		die("File ID $file_id is already marked as deleted at ". $row['delete_time']);
	}
	$db->query("UPDATE files SET delete_time = NOW(), import_result = ? WHERE id = ?", ["Currently deleting ...", $file_id]);
	$db->query("DELETE FROM observations WHERE file_id = ?", [$file_id]);
	$affected = $db->Affected_Rows();
	$db->query("UPDATE files SET deleted = 1, import_result = ? WHERE id = ?", ["Rows deleted: " . $affected, $file_id]);

	header("Location: files.php");
	exit;
	
}

$tmp = $_FILES['capfile']['tmp_name'];
$target = $upload_path . $_FILES['capfile']['name'];

if (!is_uploaded_file($tmp) ) {
	die("No uploaded file");
} 

if (!move_uploaded_file($tmp, $target) ) {
	die("Can't move file");
}

$filename = basename($target);

if (($fp = fopen($target, "r")) === FALSE) {
	die("Can't open file");
}

// Find site id:
$site = "";
$site_regex = '[A-Za-z]{3,4}';
while (($data = fgets($fp, 1000)) !== FALSE) {
	if (preg_match('/^Site code: (' . $site_regex . ')\b/', $data, $match) ) {
		if ($site != "" && $site != $match[1] ) {
			die("More than one different site code!");
		}
		$site = $match[1];
		$format = "old";
		$note = "Old format";
	}
	if (preg_match('/^Reader: Noname  Site: (' . $site_regex . ')\b/', $data, $match) ) {
		if ($site != "" && $site != $match[1] ) {
			die("More than one different site code!");
		}
		$site = $match[1];
		$format = "new";
		$note = "New format";
	}
}

var_dump($tmp);
if (!$site) {
	die("No site code found");
}

// :TODO: Check if site exists
print "Site: $site" . PHP_EOL;

fseek($fp, 0);

$db->query("INSERT INTO files (filename, import_time, antenna_code, import_result) VALUES (?,NOW(),?,?)", [ $filename, $site, "Currently importing ..." ] );
$filename_id = $db->getone("SELECT LAST_INSERT_ID()");

$fetch = FALSE;
$rows = 0;
$regex_oldformat = '!^(\d{2})/(\d{2})/(\d{4}) (\d{2}:\d{2}:\d{2})\.(\d{2})  (\d{2}:\d{2}:\d{2})\.(\d{2}) +H?([ARW]) ([0-9_]+) (A[1-4])\s+(\d+)\s+(\d+)!';
$regex_newformat = '!^D (\d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2})\.(\d{2}) (\d{2}:\d{2}:\d{2})\.(\d{2}) +H?([ARW])\s+([0-9_]+) (A[1-4])\s+(\d+)\s+(\d+)!';
while (($data = fgets($fp, 1000)) !== FALSE) {
	if ($fetch) {
		if (preg_match($regex_newformat, $data, $match) ) {
			$date = $match[1];
			$time = $match[2];
			$time_fraction = $match[3];
			$period = $match[4];
			$period_fraction = $match[5];
			$tag_type = $match[6];
			$pit_id = substr($match[7],-6);
			$original_pit_id = $match[7];
			$antenna_local = $match[8]; // ::TODO:: Antenna direction
			$observations = $match[9];
			$last_observation = $match[10];
			$db->query("INSERT INTO observations (file_id, site_code, date, time, time_fraction, period, period_fraction, tag_type, pit_id, original_pit_id, antenna_local, observations, last_observation) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", [ $filename_id, $site, $date, $time, $time_fraction, $period, $period_fraction, $tag_type, $pit_id, $original_pit_id, $antenna_local, $observations, $last_observation ] );
			$rows++;
			
		} elseif (preg_match($regex_oldformat, $data, $match) ) {
			$date = $match[3] . "-" . $match[1] . "-" . $match[2];
			$time = $match[4];
			$time_fraction = $match[5];
			$period = $match[6];
			$period_fraction = $match[7];
			$tag_type = $match[8];
			$pit_id = substr($match[9],-6);
			$original_pit_id = $match[9];
			$antenna_local = $match[10]; // ::TODO:: Antenna direction
			$observations = $match[11];
			$last_observation = $match[12];
			$db->query("INSERT INTO observations (file_id, site_code, date, time, time_fraction, period, period_fraction, tag_type, pit_id, original_pit_id, antenna_local, observations, last_observation) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", [ $filename_id, $site, $date, $time, $time_fraction, $period, $period_fraction, $tag_type, $pit_id, $original_pit_id, $antenna_local, $observations, $last_observation ] );
			$rows++;
			
#		} else {
#			print "No match" . PHP_EOL;
		}
	}
	if (preg_match('/^\s*--------- upload \d+ start/', $data) ) {
		$fetch = TRUE;
	}
	if (preg_match('/^\s*--------- upload \d+ done/', $data) ) {
		$fetch = FALSE;
	}
	
}

$db->query("UPDATE files SET observations = ?, imported = 1, import_result = ? WHERE id = ?", [ $rows, $note, $filename_id ] );

// header("Location: files.php");
exit;
?>
