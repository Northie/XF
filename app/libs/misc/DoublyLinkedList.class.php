<?php
namespace libs\misc;

class DoublyLinkedList {
	private $firstNode;
	private $lastNode;
	private $count;
 
	function __construct() {
		$this->firstNode = NULL;
		$this->lastNode = NULL;
		$this->count = 0;
	}
 
	public function isEmpty() {
		return ($this->firstNode == NULL);
	}
 
	public function insertFirst($data) {
		$newLink = new DLLNode($data);
 
		if($this->isEmpty()) {
			$this->lastNode = $newLink;
		} else {
			$this->firstNode->previous = $newLink;
		}
 
		$newLink->next = $this->firstNode;
		$this->firstNode = $newLink;
		$this->count++;
	}
 
 
	public function insertLast($data) {
		$newLink = new DLLNode($data);
 
		if($this->isEmpty()) {
			$this->firstNode = $newLink;
		} else {
			$this->lastNode->next = $newLink;
		}
 
		$newLink->previous = $this->lastNode;
		$this->lastNode = $newLink;
		$this->count++;
	}
 
 
	public function insertAfter($key, $data) {
		$current = $this->firstNode;
 
		while($current->data != $key) {
			$current = $current->next;
 
			if($current == NULL)
				return false;
		}
 
		$newLink = new DLLNode($data);
 
		if($current == $this->lastNode) {
			$newLink->next = NULL;
			$this->lastNode = $newLink;
		} else {
			$newLink->next = $current->next;
			$current->next->previous = $newLink;
		}
 
		$newLink->previous = $current;
		$current->next = $newLink;
		$this->count++;
 
		return true;
	}
 
	public function insertBefore($key, $data) {
		$current = $this->firstNode;
 
		while($current->data != $key) {
			$current = $current->next;
 
			if($current == NULL)
				return false;
		}
 
		$newLink = new DLLNode($data);
 
		if($current == $this->firstNode) {
			$newLink->next = NULL;
			$this->firstNode = $newLink;
		} else {
			$newLink->previous = $current->previous;
			$current->previous->next = $newLink;
		}
 
		$newLink->next = $current;
		$current->previous = $newLink;
		$this->count++;
 
		return true;
	}
 
	public function deleteFirstNode() {
 
		$temp = $this->firstNode;
 
		if($this->firstNode->next == NULL) {
			$this->lastNode = NULL;
		} else {
			$this->firstNode->next->previous = NULL;
		}
 
		$this->firstNode = $this->firstNode->next;
		$this->count--;
		return $temp;
	}
 
 
	public function deleteLastNode() {
 
		$temp = $this->lastNode;
 
		if($this->firstNode->next == NULL) {
			$this->firtNode = NULL;
		} else {
			$this->lastNode->previous->next = NULL;
		}
 
		$this->lastNode = $this->lastNode->previous;
		$this->count--;
		return $temp;
	}
 
 
	public function deleteNode($key) {
 
		$current = $this->firstNode;
 
		while($current->data != $key) {
			$current = $current->next;
			if($current == NULL)
				return null;
		}
 
		if($current == $this->firstNode) {
			$this->firstNode = $current->next;
		} else {
			$current->previous->next = $current->next;
		}
 
		if($current == $this->lastNode) {
			$this->lastNode = $current->previous;
		} else {
			$current->next->previous = $current->previous;
		}
 
		$this->count--;
		return $current;
	}
 
 
	public function exportForward() {
 
		$current = $this->firstNode;
 
		$a = array();
		
		while($current != NULL) {
			$a[] = $current->readNode();
			$current = $current->next;
		}
		
		return $a;
	}
 
 
	public function exportBackward() {
 
		$current = $this->lastNode;
 
		$a = array();
		
		while($current != NULL) {
			$a[] = $current->readNode();
			$current = $current->previous;
		}
		
		return $a;
	}
 
	public function total() {
		return $this->count;
	}
}