<?php
namespace Genex\Element;

class Func extends \ReflectionFunction {
	use \Genex\Stubs;

	public $name;
	public $value;
	public $type;

	function __construct(\Genex\Extension $extension, $name) {
		$this->name = $name;
		foreach($this->getParams() as $param) {
			$this->args[] = $arg = new Func\Arg($method, $param->name);
			$this->vars[] = $arg->getVar();
		}
		$this->tokenizer = new \Genex\Tokenizer($this);
	}

	public function __toString() {
		return $this->name."()";
	}
}
