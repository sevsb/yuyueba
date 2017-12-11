<?php
include_once(dirname(__FILE__) . "/../config.php");

class Subscribe {
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

    public function name() {
        return $this->mSummary["name"];
    }

    public function setName($n) {
        $this->mSummary["name"] = $n;
    }


    public function save() {
        // $id = $this->id();
        // if ($id == 0) {
        //     $id = db_Subscribe::inst()->add();
        //     if ($id !== false) {
        //         $this->mSummary["id"] = $id;
        //     }
        // } else {
        //     $id = db_Subscribe::inst()->modify($id);
        // }
        // return $id;
    }

    public function packInfo() {
       return array(
            "id" => $this->id(),
            "name" => $this->name(), 
        );
    }

    public static function create($id) {
        $summary = db_subscribe::inst()->get($id);
        return new Subscribe($summary);
    }

    public static function all() {
        $items = db_subscribe::inst()->all();
        $arr = array();
        foreach ($items as $id => $summary) {
            $arr[$id] = new Subscribe($summary);
        }
        return $arr;
    }

    public static function &cachedAll() {
        $cache = cache::instance();
        $all = $cache->load("class.Subscribe.all", null);
        if ($all === null) {
            $all = Subscribe::all();
            $cache->save("class.Subscribe.all", $all);
        }
        return $all;
    }

    public static function remove($id) {
        return db_Subscribe::inst()->remove($id);
    }
    
    public static function load($aid, $cid, $userid){
        return db_subscribe::inst()->get($aid, $cid, $userid);
    }
    
    public static function load_subscribe_activity_list($userid){
        return db_subscribe::inst()->get_activity_by_user($userid);
    }    
    
    public static function load_subscribe_calendar_list($userid){
        return db_subscribe::inst()->get_calendar_by_user($userid);
    }
    
    public static function subscribe_it($aid, $cid, $userid){
        return db_subscribe::inst()->add($aid, $cid, $userid);
    }
    
    public static function unsubscribe_it($aid, $cid, $userid){
        return db_subscribe::inst()->remove($aid, $cid, $userid);
    }
};

