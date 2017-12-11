<?php

include_once(dirname(__FILE__) . "/../config.php");

class db_subscribe extends database_table {
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_subscribe();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(MYSQL_DATABASE, MYSQL_PREFIX . "subscribe");
    }

    public function get($aid, $cid, $userid) {
        $id = (int)$id;
        return $this->get_one("activity = $aid and calendar = $cid and user = $userid");
    }
    
    public function get_activity_by_user($userid) {
        $userid = (int)$userid;
        return $this->get_all("calendar = 0 and user = $userid");
    }
    
    public function get_calendar_by_user($userid) {
        $userid = (int)$userid;
        return $this->get_all("activity = 0 and user = $userid");
    }

    public function all() {
        return $this->get_all();
    }

    public function add($aid, $cid, $userid) {
        logging::d("cid", $cid);
        return $this->insert(array("activity" => $aid, "calendar" => $cid, "user" => $userid, "time" => time()));
    }

    public function modify($id, $name) {
        $id = (int)$id;
        return $this->update(array("name" => $name), "id = $id");
    }

    public function remove($aid, $cid, $userid) {
        $id = (int)$id;
        return $this->delete("activity = $aid and calendar = $cid and user = $userid");
    }


};


