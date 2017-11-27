<?php
include_once(dirname(__FILE__) . "/../config.php");

class Sign {
    private $mSummary = null;

    public function __construct($summary = array()) {
        if (empty($summary)) {
            $summary = array(
                "id" => 0,
            );
        }
        $this->mSummary = $summary;
    }

    public function id() {
        return $this->mSummary["id"];
    }
    public function activity() {
        return $this->mSummary["activity"];
    }
    public function calendar() {
        return $this->mSummary["calendar"];
    }
    public function user() {
        return $this->mSummary["user"];
    }
    
    public function user_detail() {
        $userid = $this->user();
        return Tempuser::oneById($userid)->packInfo();
    }
    public function sheet() {
        return json_decode($this->mSummary["sheet"]);
    }
    public function modify_time() {
        return date("Y-m-d H:i:s",$this->mSummary["modify_time"]);
    }
    public function modify_time_stamp() {
        return $this->mSummary["modify_time"];
    }

    public function set_activity($n) {
        $this->mSummary["activity"] = $n;
    }
    public function set_user($n) {
        $this->mSummary["user"] = $n;
    }
    public function set_calendar($n) {
        $this->mSummary["calendar"] = $n;
    }
    public function set_sheet($n) {
        $this->mSummary["sheet"] = $n;
    }

    public function save() {
        $id = $this->id();
        if ($id == 0) {
            $id = db_sign::inst()->add($this->activity(), $this->calendar(), $this->user(), json_encode($this->sheet()));
            if ($id !== false) {
                $this->mSummary["id"] = $id;
            }
        } else {
            $id = db_sign::inst()->modify($this->id(), $this->activity(), $this->calendar(), $this->user(), json_encode($this->sheet()));
        }
        return $id;
    }

    public function packInfo($detail = false) {
       return array(
            "id" => $this->id(),
            "activity" => $this->activity(),
            "user" => $this->user(),
            "user_detail" => $this->user_detail(),
            "calendar" => $this->calendar(),
            "sheet" => $this->sheet(),
            "modify_time_stamp" => $this->modify_time_stamp(),
            "modify_time" => $this->modify_time(),
        );
    }

    public static function create($id) {
        $summary = db_sign::inst()->get($id);
        return new Sign($summary);
    }
    
    public static function oneById($id) {
        $signs = self::cachedAll();
        foreach ($signs as $sign) {
            if ($sign->id() == $id) {
                return $sign;
            }
        }
        return null;
    }    
    public static function oneByAidUser($activity_id, $userid) {
        $signs = self::cachedAll();
        foreach ($signs as $sign) {
            if ($sign->activity() == $activity_id && $sign->user() == $userid ) {
                return $sign;
            }
        }
        return null;
    }

    public static function all() {
        $items = db_sign::inst()->all();
        $arr = array();
        foreach ($items as $id => $summary) {
            $arr[$id] = new Sign($summary);
        }
        return $arr;
    }

    public static function &cachedAll() {
        $cache = cache::instance();
        $all = $cache->load("class.Sign.all", null);
        if ($all === null) {
            $all = Sign::all();
            $cache->save("class.Sign.all", $all);
        }
        return $all;
    }

    public static function remove($id) {
        return db_sign::inst()->remove($id);
    }
    
    public function cancel() {
        return db_sign::inst()->cancel($this->id());
    }
};

