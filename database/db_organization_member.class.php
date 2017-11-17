<?php

include_once(dirname(__FILE__) . "/../config.php");

class db_organization_member extends database_table {
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_organization_member();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(MYSQL_DATABASE, MYSQL_PREFIX . "organization_member");
    }

    public function get($id) {
        $id = (int)$id;
        return $this->get_one("id = $id");
    }
    
    public function one($org_id, $userid) {
        $id = (int)$id;
        return $this->get_one("user = $userid and organization = $org_id");
    }

    public function all() {
        return $this->get_all();
    }

    public function add($org_id, $userid) {
        return $this->insert(array("user" => $userid, "organization" => $org_id, "type" => 0 ));
    }

    public function modify($id, $name) {
        $id = (int)$id;
        return $this->update(array("name" => $name), "id = $id");
    }

    public function remove($id) {
        $id = (int)$id;
        return $this->update(array("status" => self::STATUS_DELETED), "id = $id");
    }

    public function kick($org_id, $userid) {
        $org_id = (int)$org_id;
        return $this->delete("organization = $org_id and user = $userid");
    }


};


