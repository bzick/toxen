<?php
namespace Tests\Basic;

class Operators implements \Countable {
	const INT_CONST = 17;
	const STRING_CONST = "str";
	const FLOAT_CONST = 1.1;
	const BOOLEAN_CONST = true;

	public static $prop1 = "is prop1";
	public static $prop2 = 33;
	protected static $prop3 = 3;
	private static $prop4 = 36.6;

	public $prop5 = "some prop5";
	public $prop6 = 23;
	protected $prop7 = true;
	private $prop8 = -9;

	public function incr($a, $b) {
		return $a + $b;
	}

	public function decr($a, $b) {
		return $a - $b;
	}

	public function multi($a, $b) {
		return $a * $b;
	}

	public function div($a, $b) {
		return $a / $b;
	}

	public function count()	{
		return self::$prop3;
	}


}
