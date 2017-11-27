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

    protected function __construct() {
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

    public function add($activity_id, $calendar_id, $userid, $sheet) {
        return $this->insert(array("activity" => $activity_id, "calendar" => $calendar_id, "user" => $userid, "sheet" => $sheet, "modify_time" => time()));
    }

    public function modify($id, $activity, $calendar, $user, $sheet) {
        $id = (int)$id;
        return $this->update(array("sheet" => $sheet, "activity" => $activity, "user" => $user, "calendar" => $calendar, "modify_time" => time()), "id = $id");
    }

    public function remove($id) {
        $id = (int)$id;
        return $this->update(array("status" => self::STATUS_DELETED), "id = $id");
    }

    public function cancel($id) {
        $id = (int)$id;
        return $this->delete("id = $id");
    }


};


