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
/**
{
	id //唯一序号
	type //
	username // 用户名
	password //密码
	nickname //昵称
	tid //临时id
	telephone // 电话号
	email //邮箱
	avatar // 头像地址
	comments //
	create_time //创建时间
	active_time // 活跃时间
	last_login // 最后一次登录
	verify_code // 验证码
	verify_status // 验证状态
	token //
	status //
	groups //
	yuyue_session//
	session_key
}
*/
    public function add($type, $username, $password, $nickname,$tid, $telephone,$email, $avatar,$comments$create_time, $active_time, $last_login, $verify_code, $verify_status,$token,$status,$groups,$yuyue_session,$session_key) {
        return $this->insert(array("type"=>$type, "username" => $username, "password" => $password,"tid"=>$tid, "telephone" => $telephone,"email"=> $email,"nickname" => $nickname, "avatar" => $avatar,"comments"=>$comments, "create_time" => time(), "active_time" => $active_time, "last_login" => time(),"verify_code" => $verify_code, "verify_status" => $verify_status,"token"=>$token,"status"=>$status,"groups"=>$groups,"yuyue_session"=>$yuyue_session,"session_key"=>$session_key));
    }

    public function modify($id,$type, $username,$tid, $password, $nickname, $telephone,$email, $avatar,$comments$create_time, $active_time, $last_login, $verify_code, $verify_status,$token,$status,$groups,$yuyue_session,$session_key) {
        $id = (int)$id;
        return $this->update(array("type"=>$type, "username" => $username, "password" => $password,"tid"=>$tid, "telephone" => $telephone,"email"=> $email,"nickname" => $nickname, "avatar" => $avatar,"comments"=>$comments, "create_time" => time(), "active_time" => $active_time, "last_login" => time(),"verify_code" => $verify_code, "verify_status" => $verify_status,"token"=>$token,"status"=>$status,"groups"=>$groups,"yuyue_session"=>$yuyue_session,"session_key"=>$session_key), "id = $id");
    }
	public function get($id) {
        $id = (int)$id;
        return $this->get_one("id = $id");
    }
	public function all() {
        return $this->get_all();
    }
    public function remove($id) {
        $id = (int)$id;
        return $this->update(array("status" => self::STATUS_DELETED), "id = $id");
    }


};


