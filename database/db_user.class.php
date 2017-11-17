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

    private function init_db_user() {
        parent::__construct(MYSQL_DATABASE, MYSQL_PREFIX . "user");
    }

    public function get($id) {
        $id = (int)$id;
        return $this->get_one("id = $id");
    }
public function getByTelephone($telephone) {
        $telephone = (String)$telephone;
        return $this->get_one("telephone = $telephone");
    }
    public function all() {
        return $this->get_all();
    }
/**
{
	id //唯一序号
	tempid//对应临时用户id
	telephone // 电话号
	email //邮箱	
	verify_code // 验证码
	verify_status // 验证状态
	status // 账户状态

}
*/
    public function add($tempid, $telephone,$email, $verify_code, $verify_status, $status) {
        return $this->insert(array("tempid"=>$tempid, "telephone" => $telephone,"email"=> $email,"verify_code" => $verify_code, "verify_status" => $verify_status, "status" => $status));
    }

    public function modify($id,$tempid, $telephone,$email, $verify_code, $verify_status, $status) {
        $id = (int)$id;
        return $this->update(array("tempid"=>$tempid, "telephone" => $telephone,"email"=> $email,"verify_code" => $verify_code, "verify_status" => $verify_status, "status" => $status), "id = $id");
    }
	
    public function remove($id) {
        $id = (int)$id;
        return $this->update(array("status" => self::STATUS_DELETED), "id = $id");
    }


};


