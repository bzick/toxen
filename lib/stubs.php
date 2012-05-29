<?php
namespace Genex;

trait Stubs {

	public function __toString() {
		return "Object(".get_called_class().")";
	}

	public function __call($name, $params) {
		throw new \BadMethodCallException("Undefined method $this::$name");
	}

	public static function __callStatic($name, $params) {
		throw new \BadMethodCallException("Undefined method ".get_called_class()."::$name");
	}

	public function __get($name) {
		return null;
	}

	public function __invoke() {
		throw new \BadFunctionCallException("Can't use $this as function");
	}
}
