<?php
namespace Genex;

class Config {
	public $name = "config.m4";
	public $ext;
	public $includes = array();
	public $sources = array();

	public function __construct(\Genex\Extension $ext) {
		$this->ext = $ext;
		$low = strtolower($this->name);
	}

	public function addInclude($path) {
		$this->includes[] = $path;
	}

	public function addSources($filename) {
		$this->sources[] = $filename;
	}

	public function getFilename() {
		return "config.m4";
	}
}
