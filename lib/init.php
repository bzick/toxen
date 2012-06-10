<?php
use Genex\Log;

define('n', "\n"); // $var.\n;
define('r', "\r"); // $var.\r;
define('r\n', "\r\n"); // $var.\r\n;
define('t', "\t"); // $var.\t;
define('n\t', "\n\t"); // $var.\n\t;
define('DS', DIRECTORY_SEPARATOR);
define('NS', '\\');
define('GENEX_DIR', dirname(__DIR__));
define('GENEX_LIB', GENEX_DIR.DS.'lib');

require_once GENEX_LIB.DS.'log.php';

Log::setup();

spl_autoload_register(function($class_name) {
	$_class = strtolower($class_name);
	$_class = str_replace(NS, DS, $_class);
	$_class = ltrim($_class, DS);
	$first = strstr($_class, "/", true);
	if($first == "genex") {
		$file = GENEX_LIB.strstr($_class, "/").".php";
		if(file_exists($file)) {
			require_once $file;
		}
	}
});

/**
 * Alias of sprintf
 */
function f($format) {
	return call_user_func_array("sprintf", func_get_args());
}

/**
 * Аналогично var_dump($data); exit; за исключением того, что перед выводом пишет где была вызвана
 * @param ...
 */
function drop() {
	echo "Drop in ".\Genex\Dev::getCallerPath().\n;
	if(func_num_args()) {
		foreach(func_get_args() as $d) {
			var_dump($d);
		}
	}
	exit;
}

/**
 * Аналогично var_dump() за исключением того, что перед выводом пишет где была вызвана
 * @param mixed $data данные для вывода
 * @param ...
 */
function dump($data) {
	echo "Dump in ".\Genex\Dev::getCallerPath().\n;
	if(func_num_args()) {
		foreach(func_get_args() as $d) {
			var_dump($d);
		}
	}
}
