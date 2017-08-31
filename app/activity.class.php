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

    public function name() {
        return $this->mSummary["name"];
    }

    public function setName($n) {
        $this->mSummary["name"] = $n;
    }


    public function save() {
        // $id = $this->id();
        // if ($id == 0) {
        //     $id = db_activity::inst()->add();
        //     if ($id !== false) {
        //         $this->mSummary["id"] = $id;
        //     }
        // } else {
        //     $id = db_activity::inst()->modify($id);
        // }
        // return $id;
    }

    public function packInfo($detail = false) {
       return array(
            "id" => $this->id(),
            "owner" => "发起组织名称",
            "title" => "活动标题",
            "info" => "活动概要",
            "begintime" => time(),
            "endtime" => time(),
            "address" => "活动地址",
        );
    }

    public static function create($id) {
        $summary = db_activity::inst()->get($id);
        return new Activity($summary);
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
};

