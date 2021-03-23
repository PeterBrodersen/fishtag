<?php
require("setup.inc.php");

// Lake Lucerne
require("connect.inc.php");

$folders = [
	'reports' => 'reports/',
	'cap_upload_path' => '../data/imported/'
];

// Fish logic
function getDirectionName($antenna) {
	$direction = [
		0 => "Down",
		1 => "Up"
	];
	if (!preg_match('/^A(\d+)$/',$antenna,$match) ) {
		return false;
	}
	return $direction[$match[1] % 2];
}

function reverseDirectionName($directionName) {
	if ($directionName == "Up") {
		return "Down";
	} elseif ($directionName == "Down") {
		return "Up";
	}
	return false;
}

// DB Logic
function dbquote($string) {
	global $db;
	$string = $db->qstr($string);
	return $string;
}

// HTML logic
function htmlstart($title = "Lake Lucerne", $map = FALSE) {
	global $folders;
	$mapinclude = "";
	if ($map) {
		$mapinclude = <<<EOD
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
  integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
  crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
  integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
  crossorigin=""></script>
EOD;
	}
	$unwritable = "";
	foreach ($folders AS $folder) {
		if ( ! is_writable( $folder) ) {
			$unwritable .= '<div class="error">Folder <code>' . $folder . '</code> is unwritable. Reports can not be created and saved. Please <code>chmod 777 ' . $folder . '</code> to make folder world writable.</div>';
		}
	}
	$html = <<<EOD
<!DOCTYPE html>
<html>
<head>
<title>
$title
</title>
<link rel="stylesheet" type="text/css" href="style.css">
$mapinclude
</head>
<body>
<div id="menu">
<a href="./">Start</a>
 | <a href="timereport.php">Time report</a>
 - <a href="individual.php">Individual</a>
 | <a href="fish.php">Fish</a>
 - <a href="species.php">Species</a>
 - <a href="locations.php">Locations</a>
 - <a href="observations.php">Observations</a>
 - <a href="files.php">Files</a>
 - <a href="map.php">Map</a>
</div>

$unwritable

<h1>$title</h1>

EOD;
	return $html;
}

function htmlend() {
	$html = <<<EOD
</body>
</html>
EOD;
	return $html;
}

function dataToTable ($dataset) {
	if (!$dataset) {
		$html = '<b>Resultset is empty</b>';
		return $html;
	}
	$html = "<table border='1' cellspacing='0' cellpadding='3'>" . PHP_EOL;
	$html .= "<tr>";
	foreach($dataset[0] AS $key => $value) {
		if (is_scalar($value) ) {
			$html .= "<th>" . $key . "</th>";
		}
	}
	$html .= "</tr>" . PHP_EOL;
	foreach($dataset AS $row) {
		$html .= "<tr>";
		foreach($row AS $field) {
			if (is_scalar($field) ) {
				$html .= "<td" . (is_numeric($field) ? " align=\"right\"" : "") .">";
				$html .= htmlspecialchars($field);
				$html .= "</td>";
			}
		}
		$html .= "</tr>" . PHP_EOL;
	}
	$html .= "</table>" . PHP_EOL;
	return $html;
}

function dataToCsv ($dataset, $separator = ",") {
	$csv = "";
	if (!$dataset) {
		return false;
	}
	$fields = [];
	foreach($dataset[0] AS $key => $value) {
		if (is_numeric($value) ) {
			$fields[] = $key;
		} elseif(is_scalar($value) ) {
			$fields[] = '"' . $key . '"';
		}
	}
	$csv .= implode($separator,$fields) . PHP_EOL;
	foreach($dataset AS $row) {
		$fields = [];
		foreach($row AS $field) {
			if (is_numeric($field) ) {
				$fields[] = $field;
			} elseif(is_scalar($field) ) {
				$fields[] = '"' . $field . '"';
			}
		}
		$csv .= implode($separator,$fields) . PHP_EOL;
	}
	return $csv;

}

function createMultipleOptions ($section_name, $values, $html_key) {
	$html  = '<p>' . $section_name . "<br>" . PHP_EOL;
	$html .= '<select name="' . $html_key . '[]" multiple>' . PHP_EOL;
	foreach($values AS $value) {
		$html .= "<option value=\"" . htmlspecialchars($value) . "\" selected>" . ($value === "" ? "[blank]" : htmlspecialchars($value) ) . "</option>" . PHP_EOL;
	}
	$html .= '</select></p>' . PHP_EOL;
	return $html;	

}
?>
