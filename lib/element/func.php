<?php
namespace Genex\Element;
use Genex\Log;

class Func extends \ReflectionFunction {
	use \Genex\Stubs;

	public $args = array();
	public $required = 0;
	public $return_reference = 0;

	public $tokenizer;
	public $vars = array();
	public $opstack = array();

	function __construct($name) {
		//$this->name = $name;
		/*foreach($this->getParams() as $param) {
			$this->args[] = $arg = new Func\Arg($method, $param->name);
			$this->vars[] = $arg->getVar();
		}*/
		parent::__construct($name);
		$this->return_reference = $this->returnsReference();
		Log::debug("Function $this created");
		$this->tokenizer = new \Genex\Tokenizer($this);
	}

	public function __toString() {
		return $this->name."()";
	}
}
