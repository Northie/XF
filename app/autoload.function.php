<?php

function xeneco_autoloader($cls) {

	$file = core\System_Settings::Load()->getFileForClass($cls);

	$c = core\System_Settings::Load()->getSettings('context');

	//var_dump($cls);
	//var_dump($file);

	if ($c != 'DEV') {

		//if mode is stage or live

		if ($file != '') {
			require_once($file);
		} else {
			header("HTTP/1.1 404 Not Found");
			echo "<h1>404 File Not Found</h1>";
			echo "<h3>If you're sure the file exists, try running again in DEV mode</h3>";
			die();
		}
	} else {

		if ($file != '') {
			require_once($file);
		} else {

			//scan files and recompile file list
			libs\misc\Tools::CompileFiles();

			//include the file list again;
			core\System_Settings::Load()->includeClassList($cls);

			$file = core\System_Settings::Load()->getFileForClass($cls);

			if ($file != '') {
				//include the file
				require_once($file);
			} else {

				return;

				//*
				debug_print_backtrace();

				die($cls . " Has not been defined yet or cannot be found");
				//*/

				$m = explode("\\", $cls);

				$class_name = array_pop($m);

				$missing = "namespace " . implode("\\", $m) . ";

				class " . $class_name . " extends \modules\Default_Action {

					public function __construct() {

					}

					public function Execute() {
						\Plugins\Plugins::Load()->DoPlugins('onMissingModule',\$this);
					}
				}

				";

				eval($missing);

				//echo $missing;
			}
		}
	}
}

spl_autoload_register('xeneco_autoloader');
