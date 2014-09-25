<?php

namespace libs\wizard;

class wizard {

	protected $steps;

	public function __construct() {
		$this->steps = new \libs\misc\DoublyLinkedList;
	}

	public function getSteps() {
		return $this->steps;
	}

	public function getStepList($r = false) {
		if ($r) {
			return $this->steps->exportBackward();
		}
		return $this->steps->exportForward();
	}

	public function addStep($step) {
		$this->newStep = $step;
		return $this;
	}

	protected function asStart() {
		try {
			if ($this->newStep) {
				$this->steps->insertFirst($this->newStep);
				$this->newStep = false;
			} else {
				throw new \Exception("New Step Not Set");
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
			die();
		}
	}

	protected function afterStep($key) {

		try {
			if (!$this->steps) {
				throw new Exeception("Steps Not Initialised");
			} else {
				try {
					if ($this->newStep) {
						$this->steps->insertAfter($key, $this->newStep);
						$this->newStep = false;
					} else {
						throw new \Exception("New Step Not Set");
					}
				} catch (\Exception $e) {
					echo $e->getMessage();
					die();
				}
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			die();
		}
	}

	protected function beforeStep($key) {
		try {
			if (!$this->steps) {
				throw new Exeception("Steps Not Initialised");
			} else {
				try {
					if ($this->newStep) {
						$this->steps->insertBefore($key, $this->newStep);
						$this->newStep = false;
					} else {
						throw new \Exception("New Step Not Set");
					}
				} catch (\Exception $e) {
					echo $e->getMessage();
					die();
				}
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			die();
		}
	}

	public function attemptStep($step) {
		//look for $step in $this->steps
		//if $step is first - ok
		//else get all prior steps
		//if all prior steps complete - ok
		//else not ok
	}

	public function stepCompleted($step) {

	}

	public function stepFailed($step) {

	}

}
