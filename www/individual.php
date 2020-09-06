<?php
// Result: based in time interval

require("base.inc.php");

print htmlstart("Individual");

$antennas = $db->getall("SELECT id, location FROM antennas ORDER BY location");
$species = $db->getall("SELECT id, name_latin, name_english FROM species ORDER BY name_latin");
$rivers = $db->getall("SELECT river, GROUP_CONCAT(DISTINCT section ORDER BY (section+0), LENGTH(section) ) AS sections FROM fish GROUP BY river ORDER BY river");
$fish_origins = $db->getcol("SELECT DISTINCT fish_origin FROM fish");
$hatcheries = $db->getcol("SELECT DISTINCT hatchery FROM fish");
$parental_origins = $db->getcol("SELECT DISTINCT parental_origin FROM fish");
$sections = [];
foreach($rivers AS $river) {
	$sections[$river['river']] = explode(",",$river['sections']);
}
?>
<script type="text/javascript">
function updateSections() {
	var rivers = document.getElementById('rivers');
	var sections = document.getElementById('sections')
	sections.length = 0;
	var l = 0;
	for (var i=0; i<rivers.length; i++) {
		if(rivers.options[i].selected) {
			var as = allSections[rivers.options[i].text];
			for(var j=0; j<as.length; j++) {
				var value = rivers.options[i].text + "," + as[j];
				var name = rivers.options[i].text + ": " + as[j];
				if (as[j].length == 0) {
					name = rivers.options[i].text + ": (blank)";
				}
				sections.options[l++] = new Option(name, value, true, true);
				sections.size = l;
			}
		}
	}
}

var allSections = <?php print json_encode($sections); ?>

</script>

<form action="create_individual.php">
<div style="float: left;">
<fieldset>
<legend>Observation data</legend>
<p>
Antenna:<br>
<select name="antenna_id[]" multiple size="10">
<?php
foreach($antennas AS $antenna) {
	print "<option value=\"" . $antenna['id'] . "\" selected>" . $antenna['location'] . "</option>";
}
?>
</select>
<br>
(CTRL or SHIFT for multiple)
</p>
<table>
<tr><td>From:</td><td><input type="date" name="antenna_from" placeholder="YYYY-MM-DD"></td></tr>
<tr><td>To:</td><td><input type="date" name="antenna_to" placeholder="YYYY-MM-DD"></td></tr>
</table>
</fieldset>
</div>

<div style="float: left;">
<fieldset>
<legend>Tagging data</legend>
<fieldset>
<legend>Tag time period</legend>
<table>
<tr><td>From:</td><td><input type="date" name="tagtime_from" placeholder="YYYY-MM-DD"></td></tr>
<tr><td>To:</td><td><input type="date" name="tagtime_to" placeholder="YYYY-MM-DD"></td></tr>
</table>
</fieldset>
<p>
Species:<br>
<select name="species_id[]" multiple size="10">
<?php
foreach($species AS $spec) {
	print "<option value=\"" . $spec['id'] . "\" selected>" . $spec['name_latin'] . "</option>";
}
?>
</select>
<fieldset>
<legend>Size (TL), mm</legend>
<table>
<tr><td>Min:</td><td><input type="number" name="species_size_min" min="0" step="any"></td></tr>
<tr><td>Max:</td><td><input type="number" name="species_size_max" min="0" step="any"></td></tr>
</table>
</fieldset>

<?php
print createMultipleOptions("Fish origin", $fish_origins, 'fish_origins');
print createMultipleOptions("Hatchery", $hatcheries, 'hatcheries');
print createMultipleOptions("Parental origin", $parental_origins, 'parental_origins');
?>

<p>
River:<br>
<select name="rivers[]" multiple size="10" onchange="updateSections()" id="rivers">
<?php
foreach($rivers AS $river) {
	print "<option value=\"" . $river['river'] . "\" selected>" . $river['river'] . "</option>";
}
?>
</select>
</p>


<p>
Section:<br>
<select name="sections[]" multiple id="sections">
</select>

</p>
</fieldset>
</div>

<!--
<div style="float: left;">
<fieldset>
<legend>Time settings</legend>

<fieldset>
<legend>Time step</legend>
<p>
Hours: <select name="timestep">
<option value="1">1</a>
<option value="2">2</a>
<option value="3">3</a>
<option value="4">4</a>
<option value="6">6</a>
<option value="8">8</a>
<option value="12">12</a>
<option value="24" selected>24</a>
</select>
</fieldset>

<fieldset>
<legend>Minimum time between observations for migration</legend>
<p>
Minutes: <input type="number" name="min_migration_time" min="1" value="5" style="width: 3em">
</p>
</fieldset>
</fieldset>
</div>
-->

<div style="float: left;">
<fieldset>
<legend><label for="debug">Debug</label></legend>
<p>
<label for="debug">Debug:</label> <input type="checkbox" name="debug" id="debug">
</p>
</fieldset>
</div>

<div style="clear: both;">

<input type="hidden" name="action" value="report">
<input type="submit">
</div>
</form>

<?php

print htmlend();

?>
