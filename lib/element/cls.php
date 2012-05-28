<?php
namespace Genex\Element;

class Cls extends \ReflectionClass {

	public $namespace;
	public $constants = array();
	public $props = array();
	public $methods = array();
	public $extends;
	public $interfaces = array();

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
		$extends = class_parents($this->name);
		if($extends) {
			$this->extends = $extends[0];
		}
		//$this->props[] = new
	}
}
