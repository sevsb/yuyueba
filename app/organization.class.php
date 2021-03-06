<?php
include_once(dirname(__FILE__) . "/../config.php");

class Organization {
    private $mSummary = null;

    public function __construct($summary = array()) {
        if (empty($summary)) {
            $summary = array(
                "id" => 0,
                "name" => "",
                "avatar" => "",
                "intro" => "",
                "owner " => "",
                "type" => 0,
				"password" => "",
				"joinable"=>""
            );
        }
        $this->mSummary = $summary;
    }

    public function id() {
        return $this->mSummary["id"];
    }

    public function name() {
        return $this->mSummary["name"];
    }
	
	public function password(){
		return $this->mSummary["password"];
	}
	
    public function avatar() {
        return $this->mSummary["avatar"];
    }
    public function avatar_url() {
        return rtrim(UPLOAD_URL, "/") . "/" . $this->mSummary["avatar"];
    }
    public function avatar_thumbnail_url() {
        return Upload::mkUploadThumbnail($this->mSummary["avatar"], 200, 200);
    }

    public function intro() {
        return $this->mSummary["intro"];
    }

    public function owner() {
        return $this->mSummary["owner"];
    }

    public function type() {
        return $this->mSummary["type"];
    }
    public function joinable() {
        return $this->mSummary["joinable"];
    }
    public function owner_yuyue_session() {
        $owner = $this->mSummary['owner'];
        $user = TempUser::oneById($owner);
        return $user->yuyue_session();
    }
    public function owner_detail() {
        $owner = $this->mSummary['owner'];
        $user = TempUser::oneById($owner);
        return $user->packInfo();
    }

    public function setName($n) {
        $this->mSummary["name"] = $n;
    }
    public function setAvatar($n) {
        $this->mSummary["avatar"] = $n;
    }
    public function setIntro($n) {
        $this->mSummary["intro"] = $n;
    }
    public function setOwner($n) {
        $this->mSummary["owner"] = $n;
    }
    public function set_Type($n) {
        $this->mSummary["type"] = $n;
    }
	 public function setPassword($n) {
        $this->mSummary["password"] = $n;
    }
	 public function setJoinable($n) {
        $this->mSummary["joinable"] = $n;
    }


    public function save() {
        $id = $this->id();
        if ($id == 0) {
            db_organization::inst()->begin_transaction();
            db_organization_member::inst()->begin_transaction();
            $id = db_organization::inst()->add($this->name(), $this->avatar(), $this->intro(), $this->owner(), $this->password(), $this->joinable());
            $member = db_organization_member::inst()->add($id, $this->owner(), 1);
            if ($id && $member) {
                db_organization::inst()->commit();
                db_organization_member::inst()->commit();
            }else {
                db_organization::inst()->rollback();
                db_organization_member::inst()->rollback();
                return false;
            }
            if ($id !== false) {
               $this->mSummary["id"] = $id;
            }
        } else {
            $id = db_organization::inst()->modify($this->id(), $this->name(), $this->avatar(), $this->intro(), $this->password(), $this->joinable());
        }
        return $id;
    }

    public function packInfo() {
       return array(
            "id" => $this->id(),
            "name" => $this->name(), 
            "intro" => $this->intro(), 
            "avatar" => $this->avatar_url(), 
            "owner_yuyue_session" => $this->owner_yuyue_session(), 
            "avatar_thumbnail_url" => $this->avatar_thumbnail_url(), 
            "owner" => $this->owner_detail(), 
            "type" => $this->type(), 
			"password"=>$this->password(),
			"joinable"=>$this->joinable(),
        );
    }

    public static function create($id) {
        $summary = db_organization::inst()->get($id);
        return new Organization($summary);
    }

    public static function all() {
        $items = db_organization::inst()->all();
        $arr = array();
        foreach ($items as $id => $summary) {
            $arr[$id] = new Organization($summary);
        }
        return $arr;
    }
    
    public static function oneById($id) {
        $users = self::cachedAll();
        foreach ($users as $user) {
            if ($user->id() == $id) {
                return $user;
            }
        }
        return null;
    }

    public static function &cachedAll() {
        $cache = cache::instance();
        $all = $cache->load("class.template.all", null);
        if ($all === null) {
            $all = Organization::all();
            $cache->save("class.template.all", $all);
        }
        return $all;
    }

    public static function remove($id) {
        return db_organization::inst()->remove($id);
    }

    public static function disband($id) {
        return db_organization::inst()->disband($id);
    }
    
    public static function receive_join($org_id, $userid) {
        $ret = db_invite::inst()->one($org_id, $userid);
        if (!$ret) {
            return db_invite::inst()->add($org_id, $userid);
        }else {
            return db_invite::inst()->modify($org_id, $userid, 0);
        }
    }
    
    public static function audit_join($org_id, $userid, $audit) {
        $ret = db_invite::inst()->modify($org_id, $userid, $audit);
        if (!$ret) {
            return false;
        }
        if ($audit == 2) {
            return $ret;
        }
        $ret2 = db_organization_member::inst()->one($org_id, $userid);
        if ($ret2) {
            return $ret;
        }
        return db_organization_member::inst()->add($org_id, $userid);
    }

    public static function member_list($org_id) {
        $organization_member = db_organization_member::inst()->all();
        $tempuser_list = db_tempuser::inst()->all();
        //var_dump($organization_member);
        //var_dump($tempuser_list);
        
        $ret_arr = [];
        foreach ($organization_member as $member) {
            $member_org_id = $member['organization'];
            if ($member_org_id != $org_id) {
                continue;
            }
            $userid = $member['user'];
            $ret_arr[$userid] = new TempUser($tempuser_list[$userid]);
            $ret_arr[$userid] = $ret_arr[$userid]->packInfo();
        }
        return $ret_arr;
    }
    
    public static function has_member($org_id, $userid) {
        $member_list = self::member_list($org_id);
        logging::d("222", json_encode($member_list));
        foreach ($member_list as $member) {
            if ($member->id() == $userid) {
                return true;
            }
        }
        return false;
    }
    
    
};

