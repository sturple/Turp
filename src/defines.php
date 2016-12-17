<?php

define('TE_VERSION', '0.0.1');
define('DS', '/');

if (!defined('TE_ROOT')){
    define('TE_ROOT', str_replace(DIRECTORY_SEPARATOR, DS, getcwd()));
}

define('ROOT_DIR', TE_ROOT . '/');
define('PROJECT_DIR', ROOT_DIR .'project/');
define('USER_DIR', ROOT_DIR .'user/');
define('CONFIG_DIR', ROOT_DIR .'config/');
define('CONFIG_FILE', 'config.yml');
define('ROUTE_FILE', 'routing.yml');
define('LOG_DIR', ROOT_DIR .'logs/');