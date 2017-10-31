<?php
include_once(dirname(__FILE__) . "/../config.php");

class Activity {
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

    public function owner() {
        return $this->mSummary["owner"];
    }
    public function owner_detail() {
        $type = $this->type();
        $owner_id = $this->owner();
        if ($type == 1) {
            $user = TempUser::oneById($owner_id);
            return $user ? $user->packInfo() : null;
        }else if ($type == 2) {
            $organization = Organization::oneById($owner_id);
            return $organization ? $organization->packInfo() : null;
        }else {
            return false;
        }
    }
    public function title() {
        return $this->mSummary["title"];
    }
    public function info() {
        return $this->mSummary["info"];
    }
    public function images() {
        return json_decode($this->mSummary["images"]);
    }
    public function createtime() {
        return $this->mSummary["createtime"];
    }
    public function modifytime() {
        return $this->mSummary["modifytime"];
    }
    public function begintime() {
        return $this->mSummary["begintime"];
    }
    public function endtime() {
        return $this->mSummary["endtime"];
    }
    public function repeattype() {
        return $this->mSummary["repeattype"];
    }
    public function repeatcount() {
        return $this->mSummary["repeatcount"];
    }
    public function deadline() {
        return $this->mSummary["deadline"];
    }
    public function address() {
        return $this->mSummary["address"];
    }
    public function content() {
        return $this->mSummary["content"];
    }
    public function max_participants() {
        return $this->mSummary["participants"];
    }
    public function participants() {
        return $this->mSummary["participants"];
    }
    
    public function now_participants() {
        return count($this->signed_user_list());
    }
    public function type() {
        return $this->mSummary["type"];
    }
    public function joinable() {
        return $this->mSummary["joinable"];
    }
    public function joinsheet() {
        return json_decode($this->mSummary["sheet"]);
    }
    public function clickcount() {
        return $this->mSummary["clickcount"];
    }
    public function status() {
        return $this->mSummary["status"];
    }
   
    
    public function signed_user_list() {   
        $activity_id = $this->id();
        
        $all_sign = db_sign::all();
        $all_user = TempUser::all();
        
        $ret = [];
        if (empty($all_sign)) {
            return [];
        }
        foreach ($all_sign as $sign) {
            if ($sign["activity"] ==  $activity_id) {
                $s["id"] = $sign['id'];
                $s["sheet"] = json_decode($sign['sheet']);
                $s["user"] = $all_user[$sign['user']]->packInfo();
                $ret[$sign["id"]] = $s;
            }
        }
        return $ret;
    }
    /*
        $activity->set_Type();
        $activity->setOwner();
        
        $activity->setJoinable();
        $activity->setParticipants();
        
        $activity->setTitle();
        $activity->setInfo();
        $activity->setContent();
        $activity->setImages();
        
        $activity->setBegintime();
        $activity->setEndtime();
        $activity->setDeadline();
        
        $activity->setAddress();
        
        $activity->setRepeattype();
        $activity->setRepeatcount();
        $activity->setJoinsheet();
    */
    public function setTitle($n) {
        $this->mSummary["title"] = $n;
    }
    public function set_Type($n) {
        $this->mSummary["type"] = $n;
    }
    public function setOwner($n) {
        $this->mSummary["owner"] = $n;
    }
    public function setJoinable($n) {
        $this->mSummary["joinable"] = $n;
    }
    public function setParticipants($n) {
        $this->mSummary["participants"] = $n;
    }
    public function setInfo($n) {
        $this->mSummary["info"] = $n;
    }
    public function setContent($n) {
        $this->mSummary["content"] = $n;
    }
    public function setImages($n) {
        $this->mSummary["images"] = json_encode($n);
    }
    public function setBegintime($n) {
        $this->mSummary["begintime"] = $n;
    }
    public function setEndtime($n) {
        $this->mSummary["endtime"] = $n;
    }
    public function setDeadline($n) {
        $this->mSummary["deadline"] = $n;
    }
    public function setAddress($n) {
        $this->mSummary["address"] = $n;
    }
    public function setRepeattype($n) {
        $this->mSummary["repeattype"] = $n;
    }
    public function setRepeatcount($n) {
        $this->mSummary["repeatcount"] = $n;
    }
    public function setJoinsheet($n) {
        $this->mSummary["sheet"] = json_encode($n);
    }
    public function setStatus($n) {
        $this->mSummary["status"] = $n;
    }


    public function save() {
        $id = $this->id();
        if ($id == 0) {
            $id = db_activity::inst()->add($this->owner(), $this->title(), $this->info(), $this->images(), $this->begintime(), $this->endtime(), $this->repeattype(), $this->repeatcount(), $this->deadline(), $this->address(), $this->content(), $this->participants(), $this->joinsheet(), $this->type(), $this->joinable());
            if ($id !== false) {
                $this->mSummary["id"] = $id;
            }
        } else {
            $id = db_activity::inst()->modify($this->id(), $this->title(), $this->info(), $this->images(), $this->begintime(), $this->endtime(), $this->repeattype(), $this->repeatcount(), $this->deadline(), $this->address(), $this->content(), $this->participants(), $this->joinsheet(), $this->joinable());
        }
        return $id;
    }

    public function packInfo($detail = false) {
       return array(
            "id" => $this->id(),
            "type" => $this->type(),
            "owner" => $this->owner_detail(),
            "title" => $this->title(),
            "info" => $this->info(),
            "images" => $this->images(),
            "content" => $this->content(),
            "begintime" => $this->begintime(),
            "endtime" => $this->endtime(),
            "deadline" => $this->deadline(),
            "address" => $this->address(),
            "repeattype" => $this->repeattype(),
            "repeatcount" => $this->repeatcount(),
            "joinsheet" => $this->joinsheet(),
            "status" => $this->status(),
            "joinable" => $this->joinable(),
            "max_participants" => $this->max_participants(),
        );
    }

    public static function create($id) {
        $summary = db_activity::inst()->get($id);
        return new Activity($summary);
    }
    
    public static function oneById($id) {
        $activities = self::cachedAll();
        foreach ($activities as $activity) {
            if ($activity->id() == $id) {
                return $activity;
            }
        }
        return null;
    }

    public static function all() {
        $items = db_activity::inst()->all();
        $arr = array();
        foreach ($items as $id => $summary) {
            $arr[$id] = new Activity($summary);
        }
        return $arr;
    }

    public static function &cachedAll() {
        $cache = cache::instance();
        $all = $cache->load("class.activity.all", null);
        if ($all === null) {
            $all = Activity::all();
            $cache->save("class.activity.all", $all);
        }
        return $all;
    }

    public static function remove($id) {
        return db_activity::inst()->remove($id);
    }
    
    public static function cancel($id) {
        return db_activity::inst()->cancel($id);
    }
};

