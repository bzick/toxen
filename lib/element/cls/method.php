<?php
namespace Genex\Element\Cls;
use Genex\Log;

class Method extends \ReflectionMethod {
	//use \Genex\FunctionParser;
	//use \Genex\Element\Func;

	public $cls;
	public $access;
	public $args = array();
	public $required = 0;

	public $tokenizer;
	public $vars = array();
	public $opstack = array();

	public function __construct(\Genex\Element\Cls $class, $method) {
		$this->cls = $class;
		parent::__construct($class->name, $method);
		Log::debug("Method $this created");
		/*foreach($this->getParams() as $param) {
			$this->args[] = $arg = new \Genex\Element\Method\Argument($this, $param->name);
			$this->vars[] = $arg->getVar();
		}*/
		$this->tokenizer = new \Genex\Tokenizer($this);
	}

	public function __toString() {
		return $this->cls->name."::".$this->name."() { }";
	}

	public function getArgInfo() {
	}

	public function getDefenition() {

	}

	public function getBody($compile_code = false) {

	}
}
