<?php
namespace Genex\Element;

class Cls extends \ReflectionClass {
	use \Genex\Stubs;

	public $namespace;
	public $constants = array();
	public $props = array();
	public $methods = array();
	public $parent;
	public $interfaces = array();
	public $traits = array();

	public function __construct($class_name) {
		parent::__construct($class_name);

		$this->namespace = $this->getNamespaceName();
		foreach($this->getConstants() as $const => $value) {
			$this->constants[] = new Cls\Constant($this, $const, $value);
		}
		foreach($this->getProperties() as $property) {
			if($property->class == $this->name) {
				$this->props[] = new Cls\Property($this, $property->name);
			}
		}
		foreach($this->getMethods() as $method) {
			if($method->class == $this->name) {
				$this->methods[] = new Cls\Method($this, $method->name);
			}
		}
		$this->interfaces = $this->getInterfaceNames();
		$parents = class_parents($this->name);
		if($parents) {
			$this->parent = $parents[0];
		}
	}

	public function __toString() {
		return $this->name;
	}

	public function getSourceConstants() {
		
	}
}
