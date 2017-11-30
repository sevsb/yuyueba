<?php

include_once(dirname(__FILE__) . "/../config.php");

class db_activity extends database_table {
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_activity();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(MYSQL_DATABASE, MYSQL_PREFIX . "activity");
    }

    public function get($id) {
        $id = (int)$id;
        return $this->get_one("id = $id");
    }

    public function all() {
        return $this->get_all();
    }

    public function add($owner, $title, $info, $images, $begintime, $endtime, $repeattype, $repeatcount, $repeatend, $address, $content, $participants, $joinsheet, $type, $joinable, $calendar_id) {
        return $this->insert(array("owner" => $owner, "title" => $title, "info" => $info, "images" => $images, "begintime" => $begintime, "endtime" => $endtime, "repeattype" => $repeattype, "repeatcount" => $repeatcount, "repeatend" => $repeatend, "address" => $address, "content" => $content, "participants" => $participants, "sheet" => $joinsheet, "type" => $type, "joinable" => $joinable, "status" => 0, "createtime" => time(),  "modifytime" => time(), "clickcount" => 0, "calendar_id" => $calendar_id));
    }

    public function modify($id, $title, $info, $images, $begintime, $endtime, $repeattype, $repeatcount, $repeatend, $address, $content, $participants, $joinsheet, $joinable, $calendar_id, $status) {

        return $this->update(array("title" => $title, "info" => $info, "images" => $images, "begintime" => $begintime, "endtime" => $endtime, "repeattype" => $repeattype, "repeatcount" => $repeatcount, "repeatend" => $repeatend, "address" => $address, "content" => $content, "participants" => $participants, "sheet" => json_encode($joinsheet), "joinable" => $joinable, "modifytime" => (int)time(), "calendar_id" => $calendar_id, "status" => $status), "id = $id");
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


