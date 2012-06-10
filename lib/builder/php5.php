<?php
namespace Genex\Builder;

class PHP5 implements \Iterator {
	use \Genex\Stubs;
	public $files = array();
	public $ext;
	public $stub = true;

	public function __construct(\Genex\Extension $ext, $stub = true) {
		$this->ext = $ext;
		$this->stub = $stub;
		$this->files["config.m4"] = array("configFile");
		$this->files["php_{$ext->code}.h"] = array("moduleHeaderFile");
		$this->files["php_{$ext->code}.c"] = array("moduleSourceFile");
		foreach($this->ext->classes as $class) {
			$this->files["class_{$class->code}.h"] = array("classHeaderFile", $class);
			$this->files["class_{$class->code}.c"] = array("classSourceFile", $class);
		}
	}

	public function __toString() {
		return "PHP5+";
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid() {
		return !!current($this->files);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
		reset($this->files);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		next($this->files);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return scalar scalar on success, or null on failure.
	 */
	public function key() {
		return key($this->files);
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current() {
		$args = current($this->files);
		$cb = array_shift($args);
		return call_user_func_array(array($this, $cb), $args);
	}

	public function configFile() {
		$upper = strtoupper($this->ext->code);
		$m4 = "
PHP_ARG_WITH({$this->ext->code}, for {$this->ext->name} support,
[  --with-{$this->ext->code}             Include {$this->ext->name} support])

if test \"\$PHP_{$upper}\" != \"no\"; then
	PHP_ADD_INCLUDE(.)
	";
		foreach($this->ext->config->includes as $include) {
			$m4 .= "PHP_ADD_INCLUDE($include)".\n.\t;
		}

		$src = implode(" ", $this->ext->config->sources);
		$m4 .= "PHP_NEW_EXTENSION({$this->ext->code}, \"{$src}\", \$ext_shared)
fi\n";
		return $m4;
	}

	/**
	 * @return string
	 */
	public function moduleHeaderFile() {
		$EXT = strtoupper($this->ext->code);
		$ext = $this->ext->code;
		$h = <<<HEADER
#ifndef PHP_{$EXT}_H
#define PHP_{$EXT}_H

extern zend_module_entry {$ext}_module_entry;
#define phpext_{$ext}_ptr &{$ext}_module_entry

#define PHP_{$EXT}_VERSION {$this->ext->version_int}

#ifdef ZTS
#  include "TSRM.h"
#endif\n\n
HEADER;
		if($this->ext->funcs) {
			foreach($this->ext->funcs as $func) {
				$h .= "PHP_FUNCTION({$func->name});\n";
			}
		}
		$h .= <<<HEADER
\n/** Std module functions */
PHP_MINIT_FUNCTION({$ext});
PHP_RINIT_FUNCTION({$ext});
PHP_MSHUTDOWN_FUNCTION({$ext});
PHP_RSHUTDOWN_FUNCTION({$ext});

#endif	/* PHP_{$EXT}_H */\n
HEADER;

		return $h;
	}

	/**
	 * @return string
	 */
	public function moduleSourceFile() {
		$EXT = strtoupper($this->ext->code);
		$ext = $this->ext->code;
		$c = <<<SOURCE
#ifdef HAVE_CONFIG_H
#  include "config.h"
#endif

/* PHP */
#include "php.h"

/* Extension */
#include "php_{$ext}.h"

/* Declare module */
zend_module_entry {$ext}_module_entry = {\n
SOURCE;
		$c .= \t.implode(",\n\t", array(
			'STANDARD_MODULE_HEADER_EX',
			'NULL',
			$this->ext->depends ? "{$ext}_deps" : 'NULL',
			'"'.$ext.'"',
			$this->ext->funcs ? "{$ext}_functions" : 'NULL',
			"PHP_MINIT({$ext})",
			"PHP_MSHUTDOWN({$ext})",
			"PHP_RINIT({$ext})",
			"PHP_RSHUTDOWN({$ext})",
			"PHP_MINFO({$ext})",
			"PHP_{$EXT}_VERSION",
			"STANDARD_MODULE_PROPERTIES"
		)).\n;

		$c .= <<<SOURCE
};

#ifdef COMPILE_DL_{$EXT}
ZEND_GET_MODULE({$ext})
#endif
\n
SOURCE;

		if($this->ext->funcs) {
			$c .= "/* Declare function's bodies */\n";

			foreach($this->ext->funcs as $func) {
				$c .= $this->phpFunction($func).\n;
			}

			$c .= "/* Declare function's arguments */\n";

			foreach($this->ext->funcs as $func) {
				$c .= $this->zendArgInfo($func).\n;
			}

			$c .= "/* Registration of functions */\n";
			$c .= "const zend_function_entry {$ext}_functions[] = {\n";
			foreach($this->ext->funcs as $func) {
				$c .= "\tPHP_FE({$func->name},\targinfo_{$func->name})\t\n";
			}
			$c .= "\t{NULL, NULL, NULL}\t\n}";
		}

		$c .= "\n/* Define module init function */\n";

		$c .= $this->minit();

		return $c;
	}

	public function phpFunction(\Genex\Element\Func $function) {
		$f = "PHP_FUNCTION({$function->name}) {\n";
		$parse_str = "";
		foreach($function->args as $arg) {
			$parse_str .= $arg->char;
		}
		if($parse_str) {
			$f .= "\tif(zend_parse_parameters(ZEND_ARGS, \"{$parse_str}\",) == FAILURE) {\n".
			"\t\treturn;\n".
			"}";
		}
		return $f."\n}";
	}

	public function zendArgInfo(\Genex\Element\Func $function) {
		$a = "ZEND_BEGIN_ARG_INFO_EX(arginfo_{$function->name}, 0, {$function->return_reference}, {$function->required})\n";
		foreach($function->args as $arg) {
			$a .= "\tZEND_ARG_INFO({$arg->by_reference}, {$arg->name})\n";
		}
		return $a."ZEND_END_ARG_INFO()";
	}

	/**
	 * Generate module init function (PHP_MINIT_FUNCTION)
	 * @return string
	 */
	public function minit() {
		$m = "PHP_MINIT_FUNCTION({$this->ext->code}) {\n";
		foreach($this->ext->consts as $const) {
			/* @var \Genex\Element\Constant $const */
			$m .= "/* const {$const->name} = ".json_encode($const->value)."; */";
			$m .= $this->makeConstant($const);
		}
		return $m."\n}";
	}

	/**
	 * Generate constant declaration
	 * @param \Genex\Element\Constant $const
	 * @return string C code
	 * @throws \Exception
	 */
	public function makeConstant(\Genex\Element\Constant $const) {
		switch($const->type) {
			case $const::TYPE_BOOLEAN:
			case $const::TYPE_FLOAT:
				return f('REGISTER_DOUBLE_CONSTANT("%s", %f, CONST_CS | CONST_PERSISTENT)', $const->name, $const->value);
			case $const::TYPE_LONG:
				return f('REGISTER_LONG_CONSTANT("%s", %d, CONST_CS | CONST_PERSISTENT);', $const->name, $const->value);
			case $const::TYPE_STRING:
				return f('REGISTER_STRINGL_CONSTANT("%s", "%s", sizeof("%2$s"), CONST_CS | CONST_PERSISTENT);', $const->name, addcslashes($const->value, '"'.\r\n));
			default:
				throw new \Exception("Undefined constant type {$const->type}");
		}
	}

	public function makeVar(\Genex\Element\Variable $var, $value = null) {
		if($var->zval) {
			return "zval *{$var->name};";
		} else {
			switch($var->type) {
				case \Genex\Element\Variable::INT:
					return "long {$var->name}".(is_null($value) ? "" : " = ".intval($value)).";";
				case \Genex\Element\Variable::BOOL:
					return "zend_bool {$var->name}".(is_null($value) ? "" : " = ".intval(!!$value)).";";
				case \Genex\Element\Variable::STR:
					return "STR_INIT({$var->name}, \"".addcslashes(strval($value), "\"\n\r")."\");";
			}
		}
	}

	public function classHeaderFile() {
		return __FUNCTION__.\n;
	}

	public function classSourceFile() {
		return __FUNCTION__.\n;
	}

}
