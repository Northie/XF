<?php

ignore_user_abort();

$mode = 'LIVE';

switch (true) {
	case (strpos($_SERVER['SCRIPT_FILENAME'], 'dev.') > -1):
		$mode = 'DEV';

		break;
	case (strpos($_SERVER['SCRIPT_FILENAME'], 'stage.') > -1):
		$mode = 'STAGE';
		break;
}

error_reporting(E_ALL ^ (E_NOTICE | E_STRICT));

//define('MODE',$mode);
//define('MODE','DEV');

define('XENECO_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

ini_set('include_path', XENECO_PATH);

require_once(XENECO_PATH . 'core/System_Settings.class.php');
require_once(XENECO_PATH . 'libs/misc/Tools.class.php');
require_once(XENECO_PATH . 'autoload.function.php');
//require_once(ZEST_PATH.'libs/rackspace/api/OpenCloud/Globals.php');
