<?php
namespace Genex;

class Extension {
	use Stubs;

	public $name;
	public $code;
	public $config;
	public $version_int = 1;
	public $version = "1.0";
	public $classes = array();
	public $consts = array();
	public $funcs = array();
	public $depends = array();
	private $_codes = array();

	public static function factory($name) {
		return new static($name);
	}

	public function __construct($name) {
		$this->name = $name;
		$this->code = preg_replace('![^a-z0-9]+!i', '_', $name);
		$this->_codes[ $this->code ] = $this;
		$this->config = new Config($this);
		$this->config->addSources("php_{$this->code}.c");
	}

	public function addClass($class_name) {
		if(!class_exists($class_name, true)) {
			throw new \Exception("Class $class_name not found");
		}
		if(isset($this->classes[ $class_name ])) {
			throw new \Exception("Class $class_name already added");
		}
		$frags = explode(NS, $class_name);
		$code = strtolower(array_pop($frags));
		if(isset($this->_codes[$code])) {
			do {
				if(!$frags) {
					throw new \Exception("Can't add class $class_name: code conflict. Upgrade algorithm?");
				}
				$code = strtolower(array_pop($frags))."_".$code;
			} while(isset($this->_codes[$code]));
		}
		$this->_codes[$code] = $this->classes[$class_name] = $class = new Element\Cls($class_name);
		$class->code = $code;
		$this->config->addSources("class_{$class->code}.c");
		return $this;
	}

	public function addConst($const_name, $value) {
		$this->consts[] = new Element\Constant($const_name, $value);
		return $this;
	}

	public function addFunction($function_name) {
		$this->funcs[] = new Element\Func($function_name);
		return $this;
	}

	public function depends(array $extensions) {
		$this->depends = $extensions;
		$this->config->setDepends($extensions);
		return $this;
	}

	public function __toString() {
		return "Extension $this->name";
	}

	public function dump() {
		$total = "$this:";
		if($this->consts) {
			$total .= "\nConstants:";
			foreach($this->consts as $const) {
				$total .= "\n\t".strval($const);
			}
		}
		if($this->funcs) {
			$total .= "\nFunctions:";
			foreach($this->funcs as $func) {
				$total .= "\n\t".strval($func);
			}
		}
		if($this->classes) {
			$total .= "\nClasses:";
			foreach($this->classes as $class) {
				/* @var \Genex\Element\Cls $class */
				$total .= "\n\t".strval($class);
				if($class->constants) {
					foreach($class->constants as $const) {
						$total .= "\n\t\t".strval($const);
					}
				}
				if($class->props) {
					foreach($class->props as $prop) {
						$total .= "\n\t\t".strval($prop);
					}
				}
				if($class->methods) {
					foreach($class->methods as $method) {
						$total .= "\n\t\t".strval($method);
					}
				}
			}
		}
		return $total.\n;
	}
}
