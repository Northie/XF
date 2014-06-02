<?php

require_once('../app/start.php');

if(strpos($_SERVER['REQUEST_URI'],'/private/') === 0) {
	core\System_Settings::Load()->addSetting('CONTEXT','private');
} else {
	core\System_Settings::Load()->addSetting('CONTEXT','public');
}

$web = new controllers\packageController;

$web->Execute();
