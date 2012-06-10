<?php
namespace Genex;

class Tokenizer {
	use Stubs;

	public $func;

	public function __construct(\ReflectionFunctionAbstract $func) {
		$this->func = $func;
		$file = $func->getFileName();
		$start = $func->getStartLine();
		$end =  $func->getEndLine();
		Log::debug("$this created");
	}

	public function __toString() {
		return "Tokenizer[$this->func]";
	}
}
