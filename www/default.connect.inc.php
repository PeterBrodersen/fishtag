<?php
define('DB_TYPE','mysqli');
define('DB_USER','fish');
define('DB_PASS','');
define('DB_HOST','localhost');
define('DB_NAME','fish');

define('ADODB_PATH','adodb/adodb.inc.php');

require(ADODB_PATH);
$db = NewADOConnection(DB_TYPE.'://'.DB_USER.':'.DB_PASS.'@'.DB_HOST.'/'.DB_NAME) or die('Temporary unavailable - please reload.');
$db->query("set character set utf8");
?>
