<?php
namespace Genex;

class Tokenizer {
	public function __construct(\ReflectionMethod $method) {
		$file = $method->getFileName();
		$start = $method->getStartLine();
		$end =  $method->getEndLine();
	}
}
