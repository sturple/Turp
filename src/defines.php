<?php

define('TE_VERSION', '0.0.1');
define('DS', '/');

if (!defined('TE_ROOT')){
    define('TE_ROOT', str_replace(DIRECTORY_SEPARATOR, DS, getcwd()));
}

define('ROOT_DIR', TE_ROOT . '/app/');
define('PROJECT_DIR', ROOT_DIR .'project/');
define('USER_DIR', ROOT_DIR .'user/');
define('PLUGIN_DIR',ROOT_DIR.'plugins/');
define('CONFIG_DIR', ROOT_DIR .'resources/config/');
define('CONFIG_FILE', 'config.yml');
define('ROUTE_FILE', 'routing.yml');
define('LOG_DIR', TE_ROOT .'/logs/');