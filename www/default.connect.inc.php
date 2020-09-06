<?php
// Insert the correct credentials and move this file to connect.inc.php
define('DB_TYPE','mysqli');
define('DB_USER','fish');
define('DB_PASS','');
define('DB_HOST','localhost');
define('DB_NAME','fish');

define('ADODB_PATH','adodb/adodb.inc.php');

require(ADODB_PATH);
$db = NewADOConnection(DB_TYPE.'://'.DB_USER.':'.DB_PASS.'@'.DB_HOST.'/'.DB_NAME) or die('Temporary unavailable - please reload.');
$db->query("set character set utf8");

// Tables exist?
$baseTables = [ 'antennas', 'files', 'fish', 'observations', 'species' ];
$tableList = implode(', ', array_map(function($val){return sprintf("'%s'", $val);}, $baseTables));
$testQuery = "SELECT COUNT(*) from INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME IN(" . $tableList . ")";
if ( count($baseTables) != $db->getone($testQuery) ) {
	header("Content-Type: text/plain");
	print "Database structure has not been imported. Import file 'dbstructure.fish.sql' into MySQL database.";
	exit;
}
?>
