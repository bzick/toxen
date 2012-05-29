<?php
trait Def {
	public function __toString() {
		return "Stub";
	}
}

class Back extends ArrayObject implements Countable {
	use Def;
	const FOO = 1;
	public $encode = 55;
	public $back_foo = "button";
	protected $back_bar = 42;
	private $back_baz = array(17);

	public function count() {
		return count($this->back_baz);
	}

	public function view() {
		return $this->back_foo;
	}

	private function _reset() {
		$this->encode = null;
	}
}

class Adv extends Back implements Serializable, Traversable {

	public $encode = array("get target");
	public $adv_foo = "arrow";
	protected $adv_bar = 13;
	private $adv_baz = "arrayzz";

	public function unserialize($serialized) {
		$this->encode = unserialize($serialized);
	}

	public function serialize()	{
		return serialize($this->encode);
	}

	protected function listing() {
		return $this->adv_baz;
	}

	public function __toString() {
		return "custom";
	}
}


$adv = new ReflectionClass("Adv");
?>