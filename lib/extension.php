<?php
namespace Genex;

class Extension {
	public $name;
	public $config;
	public $classes = array();
	public $consts = array();
	public $funcs = array();
	public $depends = array();

	public function __construct($name) {
		$this->name = $name;
		$this->config = new Config($name);
	}

	public function addClass($class_name) {
		if(!class_exists($class_name, true)) {
			throw new \Exception("Class $class_name not found");
		}
		$this->classes[] = $class = new ElementClass($class_name);
		$this->config->addClassSrc( $class );
	}

	public function addConst($const_name, $value) {
		$this->consts[] = new ElementConst($const_name, $value);
	}

	public function addFunc($function_name) {
		$this->funcs[] = new ElementFunction($function_name);
	}

	public function setDepends(array $extensions) {
		$this->depends = $extensions;
		$this->config->setDepends($extensions);
	}
}
