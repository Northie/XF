<?php

namespace controllers;

class packageController extends defaultController {

	private $request;
	private $response;
	private $package;
	private $view;

	public function __construct() {

		parent::__construct();

		session_regenerate_id();

		$this->request = new \core\request(__CLASS__);
	}

	public function __destruct() {

		if ($this->package->viewRequired) {

			$mem = round((memory_get_peak_usage(true) / (1024 * 1024)), 2);

			$this->t2 = microtime(1);

			$t = $this->t2 - $this->t1;

			echo "<!-- \n", print_r(get_included_files(), 1), "\n -->\n";
			echo "<!-- \n", $mem, " Mb\n -->\n";
			echo "<!-- \n", round($t * 1000, 2), " milliseconds\n -->\n";
			echo "<!-- \n", print_r(\core\Events::Load()->getAllEvents(), 1), "\n -->\n";
		}
	}

	public function Execute() {

		$file = \core\System_Settings::Load()->getFileForClass(ltrim($this->request->REQUEST, '\\'));

		try {
			if (is_null($file)) {
				//throw new \Exception("Package File Not Found for ".$this->request->REQUEST);
				$this->request->REQUEST = '\packages\frontend\pageNotFound\index';
			}

			//var_dump($this->request->REQUEST);

			$this->package = \packages\Factory::build($this->request->REQUEST);

			$this->view = $this->package->getView();

			try {
				$view = \core\System_Settings::Load()->getFileForClass(ltrim($this->view, "\\"));

				if ($this->package->viewRequired && is_null($view)) {
					throw new \Exception("View File (" . $this->view . ") Not Found");
				}

				\Plugins\Plugins::RegisterPlugins();

				$this->response = new \core\response;

				$this->package->setRequest($this->request);
				$this->package->setResponse($this->response);

				$filter_list = $this->package->getFilterList();

				$filter_manager = new \core\filterManager($this->package, $filter_list);

				$filter_manager->Execute();
			} catch (fileNotFoundException $e) {
				echo $e->getMessage();
			}
		} catch (\Exception $e) {
			//print_r($this->package);
			echo $e->getMessage();
		}
	}

}
