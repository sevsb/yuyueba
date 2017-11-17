<?php

include_once(dirname(__FILE__) . "/../config.php");

class db_invite extends database_table {
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_invite();
        return self::$instance;
    }

    private function init_db_invite() {
        parent::__construct(MYSQL_DATABASE, MYSQL_PREFIX . "invite");
    }

    public function get($id) {
        $id = (int)$id;
        return $this->get_one("id = $id");
    }
    
    public function one($org_id, $userid) {
        $org_id = (int)$org_id;
        $userid = (int)$userid;
        return $this->get_one("organization = $org_id and user = $userid");
    }

    public function all() {
        return $this->get_all();
    }
    

    public function add($org_id, $userid) {
        return $this->insert(array("organization" => $org_id, "user" => $userid, "type" => 0));
    }

    public function modify($org_id, $userid, $type) {
        $org_id = (int)$org_id;
        $userid = (int)$userid;
        return $this->update(array("type" => $type), "organization = $org_id and user = $userid");
    }

    public function remove($id) {
        $id = (int)$id;
        return $this->update(array("status" => self::STATUS_DELETED), "id = $id");
    }


};


