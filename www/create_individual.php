<?php
$use_timestep = FALSE;
require("common_create.php");

$dataset = [];

if ($debug) {
	print "<pre>Got " . count($result) . " rows</pre>";
	flush();
}
foreach($result AS $row) {
	$pit_id = $row['original_pit_id'];
	if ( ! isset($dataset[$pit_id]) )  {
		$dataset[$pit_id] = [
			'antenna' => [ 'latest_time' => FALSE, 'latest_date' => FALSE, 'latest_antenna_direction' => FALSE ],
			'records' => [],
			'passages' => [ 'total' => 0, 'unique_days' => 0 ]
		];
	}

//	if ($debug) print "pit_id: " . $pit_id . PHP_EOL;
	$datestring = $row['date'] . " " . $row['time'] . "." . $row['time_fraction'];
	$current_time = new DateTime($datestring);
	// :TODO: Should ignore test tag types of "R"
	// Probably not a problem as tables are joined through fish table
	$latest_antenna_direction = ($dataset[$pit_id]['antenna']['latest_antenna_direction'] ?? FALSE);
	$latest_time = ($dataset[$pit_id]['antenna']['latest_time'] ?? FALSE);
	$latest_date = ($dataset[$pit_id]['antenna']['latest_date'] ?? FALSE);

	if ( ! isset($dataset[$pit_id]['records']['first_record']) ) {
		$dataset[$pit_id]['records']['first_record'] = $datestring;
		$dataset[$pit_id]['antenna']['first_antenna_direction'] = getDirectionName($row['antenna_local']);
		$dataset[$pit_id]['antenna']['first_antenna'] = $row['antenna_local'];
	}
	$dataset[$pit_id]['records']['latest_record'] = $datestring;

	if ($latest_antenna_direction != getDirectionName($row['antenna_local']) ) { // fish is on new antenna/position
		$dataset[$pit_id]['antenna']['latest_antenna_direction'] = getDirectionName($row['antenna_local']);
		$dataset[$pit_id]['antenna']['latest_time'] = $current_time;
//		if ($debug) print "Antenna switch: From $latest_antenna to " . $row['antenna_local'] . "\n";
		if ($latest_time) { // passage
			$diff = $current_time->getTimestamp() - $latest_time->getTimestamp();
			if (isset($dataset[$pit_id]['antenna']['total_time'][$latest_antenna_direction]) ) {
				$dataset[$pit_id]['antenna']['total_time'][$latest_antenna_direction] += $diff;
			} else {
				$dataset[$pit_id]['antenna']['total_time'][$latest_antenna_direction] = $diff;
			}
			$dataset[$pit_id]['passages']['total']++;
			if ($latest_date != $row['date']) {
				$dataset[$pit_id]['passages']['unique_days']++;
				$dataset[$pit_id]['antenna']['latest_date'] = $row['date'];
			}
		}
	}

//	if ($debug) print PHP_EOL;

}

// output:
// Fish ID, registered on antenna, First record, Last record, [Downstream in 2015-09, Downstream 2015-10, …], Total days downstream, Days with migration, Total migrations, End position


// add fish
$fishcount = count($dataset);

$timing['report'] = time();

foreach($dataset AS $key => $row) { // add beginning and end times outside observation
	if ( ! isset($dataset[$key]['antenna']['total_time']['Up']) ) {
		$dataset[$key]['antenna']['total_time']['Up'] = 0;
	}
	if ( ! isset($dataset[$key]['antenna']['total_time']['Down']) ) {
		$dataset[$key]['antenna']['total_time']['Down'] = 0;
	}
	if ($antenna_from) {
		$startdate = new DateTime($antenna_from);
		$first_record = new DateTime($row['records']['first_record']);
		$dataset[$key]['antenna']['total_time']['Up'] += $first_record->getTimestamp() - $startdate->getTimestamp();
	}
	if ($antenna_to) {
		$enddate = new DateTime($antenna_to);
		$dataset[$key]['antenna']['total_time'][$dataset[$key]['antenna']['latest_antenna_direction']] += $enddate->getTimestamp() - $row['antenna']['latest_time']->getTimestamp();
	}
}

print "<pre>";

print "Possible fish: " . ($timing['sqlfish'] - $timing['start']) . " sec.\n";
print "Fish count: " . ($timing['sql'] - $timing['sqlfish']) . " sec.\n";
print "Report: " . ($timing['report'] - $timing['sql']) . " sec.\n\n";
print "Fish in result: " . $fishcount . "\n";
print "Possible fish: " . $fishpossible . "\n";

if ($debug) var_dump($dataset);
print "</pre>";


$resultset = [];
foreach ($dataset AS $key => $row) {
	$resultset[] = [
		'Fish ID' => $key,
		'First record' => $row['records']['first_record'],
		'Last record' => $row['records']['latest_record'],
		'First antenna' => $row['antenna']['first_antenna'],
		'Total days downstream' => ((int) $row['antenna']['total_time']['Down']) / 86400,
		'Days with passage' => (int) $row['passages']['unique_days'],
		'Total passages' => (int) $row['passages']['total'],
		'End position' => $row['antenna']['latest_antenna_direction']
	];
}

$filename = $folders['reports'] . "individual_" . date("Ymd_His") . ".csv";
$csvdata = dataToCsv($resultset, ",");
file_put_contents($filename, $csvdata);
print '<p><a href="' . $filename . '">[Download]</a></p>' . PHP_EOL;

print dataToTable($resultset);
/*

print "<pre>";
print $csvdata;

var_dump($dataset);
print "</pre>";
*/
?>
