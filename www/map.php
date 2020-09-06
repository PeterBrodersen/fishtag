<?php
require("base.inc.php");

print htmlstart("Map", TRUE);
?>

<div id="mapid" style="height: 100%"></div>
<script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
<script type="text/javascript">
	var mymap = L.map('mapid');
</script>


<?php

print htmlend();

?>
