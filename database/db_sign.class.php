<?php

include_once(dirname(__FILE__) . "/../config.php");

class db_sign extends database_table {
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_sign();
        return self::$instance;
    }

    private function db_sign() {
        parent::__construct(MYSQL_DATABASE, MYSQL_PREFIX . "sign");
    }

    public function get($id) {
        $id = (int)$id;
        return $this->get_one("id = $id");
    }
    public function one($activity_id, $userid) {
        $activity_id = (int)$activity_id;
        $userid = (int)$userid;
        return $this->get_one("activity = $activity_id and user = $userid");
    }

    public function all() {
        return $this->get_all();
    }

    public function add($activity_id, $userid, $sheet) {
        return $this->insert(array("activity" => $activity_id, "user" => $userid, "sheet" => $sheet));
    }

    public function modify($id, $name) {
        $id = (int)$id;
        return $this->update(array("name" => $name), "id = $id");
    }

    public function remove($id) {
        $id = (int)$id;
        return $this->update(array("status" => self::STATUS_DELETED), "id = $id");
    }

    public function del($activity_id, $userid) {
        $activity_id = (int)$activity_id;
        $userid = (int)$userid;
        return $this->delete("activity = $activity_id and user = $userid");
    }


};


