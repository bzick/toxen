<?php

require_once __DIR__.'/lib/init.php';

$build = Genex\Builder::getBuild(__DIR__."/tests/basic/build.php");
$build->setOutput("/tmp/genex");
$build();