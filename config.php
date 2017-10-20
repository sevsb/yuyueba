<?php

if (file_exists(dirname(__FILE__) . "/../PATH.php")) {
    include_once(dirname(__FILE__) . "/../PATH.php");
}

include_once(dirname(__FILE__) . "/../framework/config.php");
include_once(dirname(__FILE__) . "/autoload.php");

include_once(FRAMEWORK_PATH . "/helper.php");
include_once(FRAMEWORK_PATH . "/logging.php");
include_once(FRAMEWORK_PATH . "/tpl.php");
include_once(FRAMEWORK_PATH . "/database.php");
include_once(FRAMEWORK_PATH . "/cache.php");

//UPLOAD
defined('UPLOAD_DIR') or define('UPLOAD_DIR', ROOT_PATH . '/upload/images');
defined('UPLOAD_URL') or define('UPLOAD_URL', rtrim(DOMAIN_URL, "/") . '/sevsb/upload/images');
defined('THUMBNAIL_DIR') or define('THUMBNAIL_DIR', ROOT_PATH . '/upload/thumbnails');
defined('THUMBNAIL_URL') or define('THUMBNAIL_URL', rtrim(DOMAIN_URL, "/") . '/sevsb/upload/thumbnails');


// database
defined('MYSQL_SERVER') or define('MYSQL_SERVER', '118.89.226.27');
defined('MYSQL_USERNAME') or define('MYSQL_USERNAME', 'yyba');
defined('MYSQL_PASSWORD') or define('MYSQL_PASSWORD', 'yyba');
defined('MYSQL_DATABASE') or define('MYSQL_DATABASE', 'yyba');
defined('MYSQL_PREFIX') or define('MYSQL_PREFIX', 'yyba_');


//WX
defined('WX_APPID') or define('WX_APPID', 'wx48789b1ee3a4fb7d');
defined('WX_SECRET') or define('WX_SECRET', 'eb2d636b74d8da415455b368e817edd2');

defined('WX_SMS_SDKID') or define('WX_SMS_SDKID', '1400045428');
defined('WX_SMS_SECRET') or define('WX_SMS_SECRET', 'edbb6bf077fbadac41c40275783a2d2b');

