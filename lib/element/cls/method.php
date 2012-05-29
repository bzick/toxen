<?php
namespace Genex\Element\Cls;

class Method extends \ReflectionMethod {
	use \Genex\FunctionParser;

	public $class;
	public $name;
	public $access;
	public $args = array();
	public $required = 0;

	public $tokenizer;
	public $vars = array();
	public $opcode = array();

	public function __construct(\Genex\Element\Cls $class, $method) {
		parent::__construct($class->name, $method);
		$this->class = $class;
		foreach($this->getParams() as $param) {
			$this->args[] = $arg = new Method\Arg($method, $param->name);
			$this->vars[] = $arg->getVar();
		}
		$this->tokenizer = new \Genex\Tokenizer($this);
	}

	public function __toString() {
		return $class->name."::".$this->name;
	}

	public function getArgInfo() {
	}

	public function getDefenition() {

	}

	public function getBody($compile_code = false) {

	}
}
