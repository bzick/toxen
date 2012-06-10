<?php
namespace Genex;
/**
 * Desc of log
 * @author Ivan Shalganov <a.combest@gmail.com>
 */
class Log {
	const E_ERROR = 1;
	const E_WARNING = 2;
	const E_INFO = 4;
	const E_NOTICE = 3;
	const E_DEBUG = 5;

	private static $_fd = STDERR;

	private static $_levels = array(
		self::E_ERROR => 'Error',
		self::E_WARNING => 'Warn',
		self::E_INFO => 'Info',
		self::E_NOTICE => 'Note',
		self::E_DEBUG => 'Debug'
	);


	public static function setup() {
		ini_set('display_errors', true);
		error_reporting(-1);

		register_shutdown_function(function () {
			$error = error_get_last();
			if ($error && ($error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR))) {
				if (strpos($error['message'], 'Allowed memory size') === 0) {
					$current_limit = ini_get('memory_limit');
					if ($current_limit != -1) {
						ini_set('memory_limit', (str_replace('M', '', $current_limit) + 1) . 'M');
					}
					$error['message'] = 'Not enough memory';
				}
				self::error("PHP Fatal: ".$error['message']." in ".$error['file'].":".$error['line']);
			}
		});


		set_exception_handler(function ($e) {
			self::error($e);
		});

		set_error_handler(function ($errno, $errstr, $errfile, $errline) {
			$str = $errstr.' in '.$errfile.':'.$errline;
			switch ($errno) {
				case E_NOTICE:
				case E_STRICT:
				case E_DEPRECATED:
				case E_USER_NOTICE:
				case E_USER_DEPRECATED:
					self::notice($str);
					break;
				case E_WARNING:
				case E_USER_WARNING:
					self::warning($str, 'default', true);
					break;
				case E_ERROR:
				case E_USER_ERROR:
				case E_RECOVERABLE_ERROR:
					self::error($str." in ".$errfile.":".$errline);
					break;
				default:
					self::warning($str);
			}
		});
	}

	public static function error($msg) {
		if($msg instanceof \Exception) {
			/* @var \Exception $msg */
			self::_print(self::E_ERROR, $msg->getMessage()." in ".$msg->getFile().":".$msg->getLine().\n.$msg->getTraceAsString());
		} else {
			self::_print(self::E_ERROR, $msg);
		}
	}

	public static function warning($msg) {
		self::_print(self::E_WARNING, $msg);
	}

	public static function info($msg) {
		self::_print(self::E_INFO, $msg);
	}

	public static function notice($msg) {
		self::_print(self::E_NOTICE, $msg);
	}

	public static function debug($msg) {
		self::_print(self::E_DEBUG, $msg);
	}

	private static function _print($level, $message) {
		$mt = microtime(1);
		fwrite(self::$_fd, sprintf("%s%3s [%5s] %s\n", date("H:i:s", $mt), substr(strstr("$mt", "."), 0, 3), self::$_levels[$level], $message));
	}

}
