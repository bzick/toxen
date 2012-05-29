<?php
namespace Genex\Element\Cls;

class Constant extends \Genex\Element\Constant {
	public $class;

	public function __construct(\Genex\Element\Cls $class, $name, $value) {
		$this->class = $class;
		parent::__construct($name, $value);
	}

	public function __toString() {
		return $class->name."::".$this->name;
	}

}
