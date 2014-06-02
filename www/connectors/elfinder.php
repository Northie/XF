<?php

require_once('../../app/start.php');

$api = new \controllers\apiController;

/*
error_reporting(0); // Set E_ALL for debuging

include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderConnector.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinder.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeDriver.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeLocalFileSystem.class.php';
// Required for MySQL storage connector
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
// Required for FTP connector support
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';
*/

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

if($_SESSION['user']['id'] > 0) {

	$opts = array();


	
	$opts['roots'][0]['uploadAllow'] = array();
	$opts['roots'][0]['attributes'] = array(
		array(
			'pattern' => '/(.*)/',
			'read'=>true, //always true
			'write'=>true, //only when allowed memory within limit
			'locked'=>false
		)
	);
	//*/
	$opts['roots'][0]['driver']        = 'LocalFileSystem';   // driver for accessing file system (REQUIRED)
	$opts['roots'][0]['path']          = $_SERVER['DOCUMENT_ROOT']."/files/".$_SESSION['domain']['id']."";         // path to files (REQUIRED)
	$opts['roots'][0]['URL']           = "/files/".$_SESSION['domain']['id'].""; // URL to files (REQUIRED)
	$opts['roots'][0]['accessControl'] = 'access';             //CALL BACK - uses function defined above disable and hide dot starting files (OPTIONAL)
	//$opts['roots'][0]['alias']	   = "Your Files";
	//*/
	
	$cmd = "du -s ".$opts['roots'][0]['path'];

	$io = exec($cmd);
	
	$used = (int) trim($io);
	
	if($_SESSION['domain']['level'] == 1) {
		$a = 20;
	} else {
		$a = 200;
	}
	
	$allowed = $a * 1024;
	
	$f = $used / $allowed;
	if($f >= 1) {
		$opts['roots'][0]['attributes'][0]['write'] = false;
	} 
	$f = (int) (100 * $f);
	$opts['roots'][0]['alias']	   = "Your Files (".$f."% Full; ".$a."Mb Limit)";
	
	/*
	$opts['roots'][] = array(
		'driver'        => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
		'path'          => $_SERVER['DOCUMENT_ROOT']."/files/".$_SESSION['domain']['id']."",         // path to files (REQUIRED)
		'URL'           => "/files/".$_SESSION['domain']['id']."", // URL to files (REQUIRED)
		'accessControl' => 'access',             // disable and hide dot starting files (OPTIONAL)
		'alias'		=> "Your Files"
	);
	//*/
}
// run elFinder
$connector = new \libs\elfinder\elFinderConnector(new \libs\elfinder\elFinder($opts));
$connector->run();

