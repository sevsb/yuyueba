<?php

include_once(dirname(__FILE__) . "/../config.php");

class db_organization extends database_table {
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_organization();
        return self::$instance;
    }

    protected function __construct() {
        parent::__construct(MYSQL_DATABASE, MYSQL_PREFIX . "organization");
    }

    public function get($id) {
        $id = (int)$id;
        return $this->get_one("id = $id");
    }

    public function all() {
        return $this->get_all();
    }

    public function add($name, $avatar, $intro, $owner, $password) {
        return $this->insert(array("name" => $name, "avatar" => $avatar, "intro" => $intro, "owner" => $owner, "type" => 0, "password"=>$password));
    }

    //$id, $this->type(), $this->openid(), $this->uid(), $this->nickname(), $this->avatar(), $this->create_time(), $this->active_time(), $this->last_login(), $this->token(),  $this->status(), $this->mSummary["groups"]
    
    public function modify($id, $name, $avatar, $intro, $password) {
        $id = (int)$id;
        return $this->update(array("name" => $name, "avatar" => $avatar, "intro" => $intro, "password"=>$password), "id = $id");
    }

    public function remove($id) {
        $id = (int)$id;
        return $this->update(array("status" => self::STATUS_DELETED), "id = $id");
    }

    public function disband($id) {
        $id = (int)$id;
        return $this->update(array("type" => self::STATUS_DELETED), "id = $id");
    }


};


