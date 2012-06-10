<?php
namespace Genex\Element\Cls;

class Constant extends \Genex\Element\Constant {
	public $cls;

	public function __construct(\Genex\Element\Cls $class, $name, $value) {
		$this->cls = $class;
		parent::__construct($name, $value);
	}

	public function __toString() {
		return $this->cls->name."::".$this->name;
	}

}
