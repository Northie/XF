<?php

require_once('../app/start.php');

if (strpos($_SERVER['REQUEST_URI'], '/private/') === 0) {
	core\System_Settings::Load()->addSetting('CONTEXT', 'backend');
} else {
	core\System_Settings::Load()->addSetting('CONTEXT', 'frontend');
}

$web = new controllers\packageController;

$web->Execute();
