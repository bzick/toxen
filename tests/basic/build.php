<?php

require_once __DIR__."/stub.php";

return
	Genex\Extension::factory("genexstub")
	->addClass('Tests\Basic\Stub')
	->addFunction('stub_funct');