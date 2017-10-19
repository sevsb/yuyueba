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
                "type" => 0
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

    public function avatar() {
        return $this->mSummary["avatar"];
    }
    public function avatar_url() {
        return rtrim(UPLOAD_URL, "/") . "/" . $this->mSummary["avatar"];
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
    
    public function owner_yuyue_session() {
        $owner = $this->mSummary['owner'];
        $user = TempUser::oneById($owner);
        return $user->yuyue_session();
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


    public function save() {
        $id = $this->id();
        if ($id == 0) {
            $id = db_organization::inst()->add($this->name(), $this->avatar(), $this->intro(), $this->owner());
            if ($id !== false) {
               $this->mSummary["id"] = $id;
            }
        } else {
            $id = db_organization::inst()->modify($id);
        }
        return $id;
    }

    public function packInfo() {
       return array(
            "id" => $this->id(),
            "name" => $this->name(), 
            "intro" => $this->intro(), 
            "avatar_url" => $this->avatar_url(), 
            "owner_yuyue_session" => $this->owner_yuyue_session(), 
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
};

