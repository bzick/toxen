<?php
namespace Genex;

class Builder {
	use Stubs;

	public $ext;
	public $output;

	public static function getBuild($file) {
		if(!file_exists($file)) {
			throw new \Exception("Build file $file not found");
		}
		$ext = require_once($file);
		return new self($ext);
	}

	public function __construct(Extension $ext) {
		$this->ext = $ext;
		print_r($ext->dump());
	}

	public function setOutput($dir) {
		if(!is_dir($dir)) {
			if(!mkdir($dir, 0777, true)) {
				throw new \Exception("Can't create dir $dir");
			}
		}
		$this->output = $dir;
		return $this;
	}

	public function __invoke() {
		//$this->put($this->ext->config->getFilename(), $this->ext->config->compile());
		$php = new Builder\PHP5($this->ext);
		foreach($php as $file => $code) {
			Log::debug("Build file $file");
			$this->put($file, $code);
		}
	}

	public function put($filename, $content) {
		file_put_contents($this->output.DS.$filename, $content);
	}

}
