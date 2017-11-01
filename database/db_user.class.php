<?php

include_once(dirname(__FILE__) . "/../config.php");

class db_user extends database_table {
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    private static $instance = null;
    public static function inst() {
        if (self::$instance == null)
            self::$instance = new db_user();
        return self::$instance;
    }

    private function db_user() {
        parent::__construct(MYSQL_DATABASE, MYSQL_PREFIX . "user");
    }

    public function get($id) {
        $id = (int)$id;
        return $this->get_one("id = $id");
    }

    public function all() {
        return $this->get_all();
    }

    public function add($id, $username, $password, $nickname, $phone, $nickname, $avatar, $verify_code, $verify_status) {
        return $this->insert(array("id" => $id, "username" => $username, "password" => $password, "phone" => $phone, "nickname" => $nickname, "avatar" => $avatar, "verify_code" => $verify_code, "verify_status" => $verify_status));
    }

    //$id, $this->type(), $this->openid(), $this->uid(), $this->nickname(), $this->avatar(), $this->create_time(), $this->active_time(), $this->last_login(), $this->token(),  $this->status(), $this->mSummary["groups"]
    
    public function modify($id, $type, $openid, $uid, $nickname, $avatar, $create_time, $active_time, $last_login, $token, $status, $groups, $yuyue_session) {
        $id = (int)$id;
        return $this->update(array("type" => $type, "openid" => $openid, "uid" => $uid, "nickname" => $nickname, "avatar" => $avatar, "create_time" => $create_time, "active_time" => $active_time, "last_login" => time(), "token" => $token, "status" => $status, "groups" => $groups, "yuyue_session" => $yuyue_session), "id = $id");
    }

    public function remove($id) {
        $id = (int)$id;
        return $this->update(array("status" => self::STATUS_DELETED), "id = $id");
    }


};

