<?php
namespace Genex\Element\Func;

class Arg extends \ReflectionParameter {
	const TYPE_MIXED = 0;

	public $callable;
	public $position;
	public $optional = false;
	public $variable;

	public function __construct(Genex\Element\Callable $callable, $name) {
		$this->callable = $callable;
		parent::__construct($callable->name, $name);
		$this->variable = new \Genex\Element\Variable();
	}


}
