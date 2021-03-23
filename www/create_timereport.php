<?php
$use_timestep = TRUE;
require("common_create.php");

$dateinterval = 'PT' . $timestep . 'H';

$count = 0;

$dataset = [
	0 => []
];

if ($debug) {
	print "<pre>Got " . count($result) . " rows</pre>";
	flush();
}
foreach($result AS $row) {
	$datestring = $row['date'] . " " . $row['time'] . "." . $row['time_fraction'];
	$date = new DateTime($datestring);
	if (! isset($timelimit) ) { // initialize
		$timelimit = new DateTime($datestring);
		$timelimit->modify('today'); // set start row to 00:00:00
		$dataset[$count] = [
			'Date' => $timelimit->format("Y-m-d H:i:s"),
			'Up' => 0,
			'Down' => 0,
			'Total' => 0,
			'direction' => []
		];
		$timelimit->add(new DateInterval($dateinterval));
	}
	if ($date >= $timelimit) { // row is in next step
		$failcheck = 0;
		while ($date >= $timelimit) { 
			$last = $timelimit->format("Y-m-d H:i:s");
			$count++;
			$dataset[$count] = $dataset[$count-1]; // copy last set
			$dataset[$count]['Date'] = $last;

			$timelimit->add(new DateInterval($dateinterval));
			$failcheck++;
			if ($failcheck > 10000) {
				die("Too many steps when adding time");
			}
		}
	}
//	print_r($row);
	$old_direction = ($dataset[$count]['direction'][$row['original_pit_id']] ?? 0);
	$direction = getDirectionName($row['antenna_local']);
	if ($direction != $old_direction) {
		if ($old_direction) { // subtract one
			$dataset[$count][$old_direction]--;
		}
		$dataset[$count][$direction]++;
		$dataset[$count]['direction'][$row['original_pit_id']] = $direction;
		$dataset[$count]['Total'] = $dataset[$count]['Up'] + $dataset[$count]['Down'];
	}
}

// add fish
$fishcount = isset($dataset[$count]) ? count($dataset[$count]['direction']) : 0;

foreach($dataset AS $key => $value) {
	$dataset[$key]['Up %'] = round($dataset[$key]['Up'] / $fishpossible * 100, 1);
	$dataset[$key]['Down %'] = round($dataset[$key]['Down'] / $fishpossible * 100, 1);
	$dataset[$key]['Total %'] = round($dataset[$key]['Total'] / $fishpossible * 100, 1);
}

$timing['report'] = time();

//print dataToTable($result);
print "<pre>";

print "Possible fish: " . ($timing['sqlfish'] - $timing['start']) . " sec.\n";
print "Fish count: " . ($timing['sql'] - $timing['sqlfish']) . " sec.\n";
print "Report: " . ($timing['report'] - $timing['sql']) . " sec.\n\n";
print "Fish in result: " . $fishcount . "\n";
print "Possible fish: " . $fishpossible . "\n";
print "</pre>";

$filename = $folders['reports'] . "timereport_" . date("Ymd_His") . ".csv";
$csvdata = dataToCsv($dataset, ",");
file_put_contents($filename, $csvdata);
print '<p><a href="' . $filename . '">[Download]</a></p>' . PHP_EOL;

print dataToTable($dataset);
/*

print "<pre>";
print $csvdata;

var_dump($dataset);
print "</pre>";
*/
?>
