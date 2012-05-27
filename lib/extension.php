<?php
namespace Genex;

class Extension {
	public $name;
	public $m4;
	public $classes = array();

	public function __construct($name) {
		$this->name = $name;
		$this->m4 = new File\M4($name);
	}

	public function addClass($class_name) {
		$this->classes = new ElementClass($class_name);
		$this->m4->addSrc( $this->classes->getCFileName());
	}

	public function addConst($const_name) {
		$this->classes = new ElementConst($const_name);
	}
}
