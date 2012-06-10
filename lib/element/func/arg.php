<?php
namespace Genex\Element\Func;
use Genex\Log;

class Arg extends \ReflectionParameter {
	const TYPE_MIXED = 0;

	public $callable;
	public $position;
	public $optional = false;
	public $variable;
	public $char = "z";

	public function __construct($func, $name) {
		$this->callable = $func;
		parent::__construct($func->name, $name);
		Log::debug("Argument $this created");
		$this->variable = new \Genex\Element\Variable();
	}


}
