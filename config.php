<?php

if (file_exists(dirname(__FILE__) . "/../PATH.php")) {
    include_once(dirname(__FILE__) . "/../PATH.php");
}

include_once(dirname(__FILE__) . "/../framework/config.php");

include_once(FRAMEWORK_PATH . "/helper.php");
include_once(FRAMEWORK_PATH . "/logging.php");
include_once(FRAMEWORK_PATH . "/tpl.php");
include_once(FRAMEWORK_PATH . "/database.php");
include_once(FRAMEWORK_PATH . "/cache.php");


// database
defined('MYSQL_SERVER') or define('MYSQL_SERVER', 'localhost');
defined('MYSQL_USERNAME') or define('MYSQL_USERNAME', 'yyba');
defined('MYSQL_PASSWORD') or define('MYSQL_PASSWORD', 'yyba');
defined('MYSQL_DATABASE') or define('MYSQL_DATABASE', 'yyba');
defined('MYSQL_PREFIX') or define('MYSQL_PREFIX', 'yyba_');



