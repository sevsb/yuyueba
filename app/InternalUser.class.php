<?php
include_once(dirname(__FILE__) . "/../config.php");

class InternalUser extends User{
    private $mSummary = null;
    private $mGroups = null;
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
    public function InternalUser($summary = array()) {
        if (empty($summary)) {
			
            $summary = array(
                "id" => 0,
                "username" => "",
                "password" => "",
				"type" => "",
				"tid" => "",
                "telephone" => "",
                "email" => "",
                "groups" => "",
                "comments" => "",
                "create_time" => "",
                "active_time" => "",
                "last_login" => "",
                "verify_code" => "",
                "verify_status" => "",
                "token" => "",
                "status" => 0,
				 "yuyue_session" => "",
				 
				 "session_key" => "",
				
            );
        }
        $this->mSummary = $summary;
    }

    public function id() {
        return $this->mSummary["id"];
    }

    public function type() {
        return $this->mSummary["type"];
    }
public function username() {
        return $this->mSummary["username"];
    }
    public function password() {
        return $this->mSummary["password"];
    }

    public function nickname() {
        return $this->mSummary["nickname"];
    }

    public function telephone() {
        return $this->mSummary["telephone"];
    }

    public function email() {
        return $this->mSummary["email"];
    }

    public function comments() {
        return $this->mSummary["comments"];
    }



   
    public function yuyue_session() {
        return $this->mSummary["yuyue_session"];
    }

    public function session_key () {
        return $this->mSummary["session_key "];
    }

    public function tid() {
        return $this->mSummary["tid"];
    }
    
    

    public function avatar() {
        return $this->mSummary["avatar"];
    }

    public function create_time() {
        return $this->mSummary["create_time"];
    }
    
    public function active_time() {
        return $this->mSummary["active_time"];
    }
    
    public function last_login() {
        return $this->mSummary["last_login"];
    }
    
    public function token() {
        return $this->mSummary["token"];
    }
    
    public function status() {
        return $this->mSummary["status"];
    }

    //修改参数函数
    public function setNickname($n) {
        $this->mSummary["nickname"] = $n;
    }
    public function setAvatar($n) {
        $this->mSummary["avatar"] = $n;
    }
    public function setYuyueSession($n) {
        $this->mSummary["yuyue_session"] = $n;
    }
    public function setToken($n) {
        $this->mSummary["token"] = $n;
    }
    public function setSessionKey($n) {
        $this->mSummary["session_key"] = $n;
    }
    public function setTelephone($t) {
        $this->mSummary["telephone"] = $t;
    }

    public function setEmail($mail) {
        $this->mSummary["email"] = $mail;
    }

    public function setEmail($mail) {
        $this->mSummary["email"] = $mail;
    }
    public function setComments($c) {
        $this->mSummary["comments"] = $c;
    }
	public function verify($verify_code){
		if(!empty( $verify_code)){
			if($this->mSummary["verify_code"]==$verify_code){
				this->mSummary["verify_code"]=="00000";
				this->mSummary["verify_status"]=="true";
				return true;
			}
			return false;
		}
		return null;
	}
    public function gids() {
        $gids = $this->mSummary["groups"];
        if (empty($gids)) {
            $gids = array();
        } else {
            $gids = explode(",", $gids);
        }
        return $gids;
    }

    public function groups() {
        if ($this->mGroups === null) {
            $this->mGroups = array();
            $groups = self::cachedAllGroups();
            $gids = $this->gids();
            foreach ($gids as $gid) {
                if (isset($groups[$gid])) {
                    $this->mGroups []= $groups[$gid];
                }
            }
        }
        return $this->mGroups;
    }

    public function hasPerm($pkey) {
        $groups = $this->groups();
        foreach ($groups as $group) {
            if ($group->hasPerm($pkey)) {
                return true;
            }
        }
        return false;
    }

    public function joinGroup($gid) {
        $gids = $this->gids();
        if (!in_array($gid, $gids)) {
            $gids []= $gid;
        }
        $this->mSummary["groups"] = implode(",", $gids);
    }

    public function leaveGroup($gid) {
        $gids = $this->gids();
        foreach ($gids as $k =>  $g) {
            if ($g == $gid) {
                unset($gids[$k]);
            }
        }
        $this->mSummary["groups"] = implode(",", $gids);
    }
//       public function add($type, $username, $password, $nickname,$tid, $telephone,$email, $avatar,$comments$create_time, $active_time, $last_login,  $verify_code, $verify_status,$token,$status,$groups,$yuyue_session) {
    public function save() {
        $id = $this->id();
        if ($id == 0) {
            $id = db_user::inst()->add($this->type(),$this->username(), $this->password(), $this->nickname(),$this->tid(), $this->telephone(), $this->email(),$this->avatar(),$this->comments(),$this->create_time(),$this->active_time(),$this->last_login(),$this->verify_code(),$this->verify_status(),$this->token(),$this->status(), $this->mSummary["groups"],$this->yuyue_session(), );
            if ($id !== false) {
                $this->mSummary["id"] = $id;
            }
        } else {
            $id = db_user::inst()->modify($this->id(),$this->type(),$this->username(), $this->password(), $this->nickname(),$this->tid(), $this->telephone(), $this->email(),$this->avatar(),$this->comments(),$this->create_time(),$this->active_time(),$this->last_login(),$this->verify_code(),$this->verify_status(),$this->token(),$this->status(), $this->mSummary["groups"],$this->yuyue_session(),);
        }
        return $id;
    }

    private static function cachedAllGroups() {
        $cache = cache::instance();
        $groups = $cache->load("class.user.allgroups", null);
        if ($groups === null) {
            $groups = Group::all();
            $cache->save("class.user.allgroups", $groups);
        }
        return $groups;
    }

    public function packInfo($pack_all_groups = true) {
        $groupInfo = array();
        if ($pack_all_groups) {
            $groups = self::cachedAllGroups();
            $gids = $this->gids();
            foreach ($groups as $gid => $group) {
                $groupInfo[$gid] = $group->packInfo(false);
                $groupInfo[$gid]["join"] = 0;
            }
            foreach ($gids as $gid) {
                if (isset($groups[$gid])) {
                    $groupInfo[$gid]["join"] = 1;
                }
            }
        } else {
            $groups = $this->groups();
            $groupInfo = array();
            foreach ($groups as $group) {
                $groupInfo []= $group->packInfo(false);
            }
        }

        return array(
            "id" => $this->id(),
            "username" => $this->username(), 
            "password" => $this->password(), 
            "nickname" => $this->nickname(), 
            "telephone" => $this->telephone(), 
            "email" => $this->email(), 
            "comments" => $this->comments(), 
            "groups" => $groupInfo
        );
    }

    public static function create($uid) {
        $user = db_user::inst()->get($uid);
        return new User($user);
    }

    public static function all($include_deleted = false) {
        $users = db_user::inst()->all();
        $arr = array();
        foreach ($users as $uid => $user) {
            if (!$include_deleted) {
                if ($user["status"] == db_user::STATUS_DELETED) {
                    continue;
                }
            }
            $arr[$uid] = new User($user);
        }
        return $arr;
    }

    public static function &cachedAll() {
        $cache = cache::instance();
        $all = $cache->load("class.user.all", null);
        if ($all === null) {
            $all = User::all();
            $cache->save("class.user.all", $all);
        }
        return $all;
    }

    public static function oneByName($username) {
        $users = self::cachedAll();
        foreach ($users as $user) {
            if ($user->username() == $username) {
                return $user;
            }
        }
        return null;
    }
    public static function oneBySession($yuyue_session) {
        $users = self::cachedAll();
        foreach ($users as $user) {
            if ($user->yuyue_session() == $yuyue_session) {
                return $user;
            }
        }
        return null;
    }

    public static function remove($uid) {
        return db_user::inst()->remove($uid);
    }
};

