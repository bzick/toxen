<?php
namespace Genex;

class State {
	public static function badArgs($message) {
		return new \Exception(call_user_func_array("sprintf", func_get_args()), 1);
	}
}
