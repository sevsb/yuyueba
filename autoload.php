<?php
spl_autoload_register(function ($class) {
    $file = null;
    if (substr($class, 0, 3) == "db_") {
        $file = dirname(__FILE__) . "/database/$class.class.php";
    } else {
        $clz = strtolower($class);
        $file = dirname(__FILE__) . "/app/$clz.class.php";
    }
    if (is_file($file)) {
        include($file);
    } else {
        die("No such class.");
    }
});

