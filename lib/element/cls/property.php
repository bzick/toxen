<?php
namespace Genex\Element\Cls;
use Genex\Log,
	Genex\Element\Cls,
	Genex\Element\Variable;

class Property extends \ReflectionProperty {
	use \Genex\Stubs;

	public $cls;
	public $variable;

	public function __construct(Cls $class, $name) {
		$this->cls = $class;
		parent::__construct($this->cls->name, $name);
		$this->variable = new Variable();
		Log::debug("Propery $this created");
	}

	public function __toString() {
		return $this->cls->name."::\$".$this->name;
	}

}
