<?php

namespace Toxen;


class Extension {
    public $alias = 'exto';
    public $includes = [];
    public $sources = [];

    public function __construct(\Toxen $toxen) {

    }

    /**
     * Generate config.m4 file content
     * @return string
     */
    public function configM4() {
        $ALIAS = strtoupper($this->alias);
        ob_start();
        ?>
dnl Toxen compiler, <?=date("Y-m-d H:i:s")?>.

PHP_ARG_WITH(<?=$this->alias?>, for <?=$this->alias?> support,
[  --with-<?=$this->alias?>             Include <?=$this->alias?> support])

if test "$PHP_<?=$ALIAS?>" != "no"; then
	PHP_ADD_INCLUDE(.)
<? foreach($this->includes as $include): ?>
    PHP_ADD_INCLUDE(<?=$include?>)
<? endforeach ?>

    PHP_NEW_EXTENSION(<?=$this->alias?>, "<?=implode(" ", $this->sources)?>", $ext_shared)
fi
<?php
        return ob_get_clean();
    }

    /**
     * @return string
     */
    public function headerFile() {
        $ALIAS = strtoupper($this->alias);
        ob_start();
        ?>
#ifndef PHP_<?=$ALIAS?>_H
#define PHP_<?=$ALIAS?>_H

extern zend_module_entry <?=$this->alias?>_module_entry;
#define phpext_<?=$this->alias?>_ptr &<?=$this->alias?>_module_entry

#define PHP_<?=$ALIAS?>_VERSION <?=$this->version?>

#ifdef ZTS
#  include "TSRM.h"
#endif

<? if($this->funcs): ?>
/* User functions */
<?   foreach($this->funcs as $func): ?>
PHP_FUNCTION(php_<?=$func->name?>)
<?   endforeach ?>
<? endif ?>

/* Std module functions */
PHP_MINIT_FUNCTION(<?=$this->alias?>);
PHP_RINIT_FUNCTION(<?=$this->alias?>);
PHP_MSHUTDOWN_FUNCTION(<?=$this->alias?>);
PHP_RSHUTDOWN_FUNCTION(<?=$this->alias?>);

#endif	/* PHP_<?=$ALIAS?>_H */\n
<?php
        return ob_get_clean();
    }

    public function mainFile() {

    }
}