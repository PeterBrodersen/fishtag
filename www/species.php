<?php
require("base.inc.php");

$sites = $db->getall("
	SELECT species.id, code, name_latin, name_english, COUNT(fish.id) AS count
	FROM species
	LEFT JOIN fish ON species.code = fish.species_code
	GROUP BY species.id
	ORDER BY id
");

print htmlstart("Species");

print '<table border="1" cellspacing="0" cellpadding="3" id="speciestable">' . PHP_EOL;
print '<tr><th onclick="sortTable(0,true)">ID</th><th onclick="sortTable(1)">Code</th><th onclick="sortTable(2)">Name (Latin)</th><th onclick="sortTable(3)">Name (English)</th><th onclick="sortTable(4,true)">Individuals</th></tr>' . PHP_EOL;

foreach($sites AS $site) {
	print "<tr>";
	print "<td>" . $site['id'] . "</td>";
	print "<td>" . $site['code'] . "</td>";
	print "<td>" . $site['name_latin'] . "</td>";
	print "<td>" . $site['name_english'] . "</td>";
	print "<td class=\"number\">" . $site['count'] . "</td>";
	print "</tr>" . PHP_EOL;
}

print "</table>";

print <<<EOD
<script>
// sort code from https://www.w3schools.com/howto/howto_js_sort_table.asp
// numeric sort option added by pb
function sortTable(n,numeric) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("speciestable");
  switching = true;
  // Set the sorting direction to ascending:
  dir = "asc";
  /* Make a loop that will continue until
  no switching has been done: */
  while (switching) {
    // Start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /* Loop through all table rows (except the
    first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      // Start by saying there should be no switching:
      shouldSwitch = false;
      /* Get the two elements you want to compare,
      one from current row and one from the next: */
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /* Check if the two rows should switch place,
      based on the direction, asc or desc: */
      if (dir == "asc") {
        if (! numeric && (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) || numeric && (Number(x.innerHTML) > Number(y.innerHTML) ) ) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (! numeric && (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) || numeric && (Number(x.innerHTML) < Number(y.innerHTML) ) ) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch
      and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase this count by 1:
      switchcount ++;
    } else {
      /* If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again. */
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>
EOD;


print htmlend();

?>
