<?php
namespace Genex\Element;
use Genex\Log;

class Variable {
	const INT = 1;
	const STR = 2;
	const FLOAT = 3;
	const ARR = 4;
	const OBJECT = 5;
	const CB = 6;
	const NULL = 7;
	const BOOL = 8;

	public $proto;
	public $is_zval = true;
	public $is_ref = false;
	public $name;
	public $func;

}
