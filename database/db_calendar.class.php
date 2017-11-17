<?php

include_once(dirname(__FILE__) . "/../config.php");

class db_calendar extends database_table {
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_calendar();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(MYSQL_DATABASE, MYSQL_PREFIX . "calendar");
    }

    public function get($id) {
        $id = (int)$id;
        return $this->get_one("id = $id");
    }

    public function all() {
        return $this->get_all();
    }

    public function add($title, $content, $type, $owner) {
        return $this->insert(array("title" => $title, "content" => $content, "type" => $type, "owner" => $owner, "activity_list" => '', "create_time" => time(), "modify_time" => time(), "status" => 0));
    }

    public function modify($id, $title, $content, $type, $owner) {
        $id = (int)$id;
        return $this->update(array("title" => $title, "content" => $content, "type" => $type, "owner" => $owner,"modify_time" => time()), "id = $id");
    }

    public function remove($id) {
        $id = (int)$id;
        return $this->update(array("status" => self::STATUS_DELETED), "id = $id");
    }

    public function cancel($id) {
        $id = (int)$id;
        return $this->update(array("status" => self::STATUS_DELETED), "id = $id");
    }


};


