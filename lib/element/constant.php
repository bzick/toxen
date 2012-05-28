<?php
namespace Genex\Element;

class Constant {
	const TYPE_LONG = 1;
	const TYPE_FLOAT = 2;
	const TYPE_STRING = 3;
	const TYPE_BOOLEAN = 3;

	public $name;
	public $value;
	public $type;

	function __construct($name, $value) {

	}


	public function getCode() {

	}
}
