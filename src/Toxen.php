<?php

class Toxen {
    public $config = [];
    public $root = __DIR__;

    public $classes   = [];
    public $methods   = [];
    public $props     = [];
    public $constants = [];

    /**
     * @param string $config_path
     * @throws LogicException
     */
    public function __construct($config_path = null) {
        $this->root = getcwd();
        if(!file_exists($this->root.'/composer.json')) {
            throw new LogicException("composer.json not found");
        }
        $this->config = json_encode($this->root.'/composer.json', true);
    }

    /**
     *
     */
    public function dispatch() {
        $options = getopt('h', array(
            "help::",
            "convert::",
            "tmp_dir:",
        )) + ['tmp_dir' => '/tmp'];
        if(isset($options['help']) || isset($options['help'])) {
            $file = basename($_SERVER['PHP_SELF']);
            echo "
Usage: $file --convert [--tmp_dir=/tmp]
Help:  $file -h|--help\n";
            exit;
        }

        if($options['convert']) {
            $this->convert($options['tmp_dir']);
        }

    }

    /**
     * @param string $tmp
     */
    public function convert($tmp) {
        // Get classmap from composer
        $classes = $this->parseClasses();

        $extension = new \Toxen\Extension($this);

        foreach($classes as $class => $path) {
            $this->classes[$class] = new TxClass($class, $path);
            foreach($this->classes[$class]->uses as $class) {
                if(isset($classes[ $class ])) {
                    continue;
                } elseif(class_exists($class, false)) {
                    $uses = new ReflectionClass($class);
                    if($ext = $uses->getExtension()) {
                        $this->classes[$class]->addDepends($ext);
                    }
                }

                throw new LogicException("Unknown class $class");
            }
            $extension->addClass($class);

            $this->put($class->basename.".h", $class->headerFile());
        }
        $this->put("comfig.m4", $extension->configM4());
        $this->put("php_{$this->config->alias}.h", $extension->headerFile());
        $this->put("php_{$this->config->alias}.c", $extension->cFile());
    }

    public function parseClasses() {
        $classes = array();
        foreach($this->config['autoload'] as $rule) {
            // ... some code ... load from autoload of projects and packages
            $classes += \Toxen\ClassMapGenerator::createMap($path);
        }

        return $classes;
    }
}