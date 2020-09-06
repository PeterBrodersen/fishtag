<?php
// Basic checks

function hasConnectFile() {
	return file_exists( "connect.inc.php" );

}

function hasADOdb() {
	foreach ( explode(":", ini_get("include_path") ) AS $path ) {
		$path .= '/adodb/adodb.inc.php';
		if ( file_exists( $path ) ) {
			return true;
		}
	}
	return false;
}


// Database
if ( ! hasConnectFile() ) {
	header("Content-Type: text/plain");
	print "MySQL credentials connection file is missing. Please copy 'default.connect.inc.php' to 'connect.inc.php' and edit credentials.";
	exit; 
}

// ADOdb

if ( ! hasADOdb() ) {
	header("Content-Type: text/plain");
	print "ADOdb PHP abstraction layer is missing. Download or clone it from  https://github.com/ADOdb/ADOdb  and put it in 'www' folder (ending up at www/adodb/adodb.inc.php)";
	exit; 
}
?>
