<?php

include_once(dirname(__FILE__) . "/../config.php");

class db_base extends database {

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_base();
        return self::$instance;
    }

    public function __construct($db = MYSQL_DATABASE) {
        $this->dbname = $db;
        try {
            $this->init($db);
        } catch (PDOException $e) {
            logging::e("PDO.Exception", $e, false);
            die($e);
        }
    }
    
    public function do_query($sql){
        return $this->get_all($sql);
    }

};


