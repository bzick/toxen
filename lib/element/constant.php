<?php
namespace Genex\Element;
use Genex\State;

class Constant {
	use \Genex\Stubs;

	const TYPE_LONG = 1;
	const TYPE_FLOAT = 2;
	const TYPE_STRING = 3;
	const TYPE_BOOLEAN = 3;

	public $name;
	public $value;
	public $type;

	function __construct(\Genex\Extension $extension, $name, $value) {
		$this->name = $name;
		$this->value = $value;
		switch(true) {
			case is_int($value):
				$this->type = self::TYPE_LONG;
				break;
			case is_float($value):
				$this->type = self::TYPE_FLOAT;
				break;
			case is_string($value):
				$this->type = self::TYPE_STRING;
				break;
			case is_bool($value):
				$this->type = self::TYPE_BOOLEAN;
				break;
			default:
				throw State::badArgs("Constant $this must be integer, float, string or boolean (%s given)", gettype($value));
		}
	}

	public function __toString() {
		return $this->name;
	}
}
