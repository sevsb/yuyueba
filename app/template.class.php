<?php
include_once(dirname(__FILE__) . "/../config.php");

class Template {
    private $mSummary = null;

    public function Template($summary = array()) {
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
        //     $id = db_template::inst()->add();
        //     if ($id !== false) {
        //         $this->mSummary["id"] = $id;
        //     }
        // } else {
        //     $id = db_template::inst()->modify($id);
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
        $summary = db_template::inst()->get($id);
        return new Template($summary);
    }

    public static function all() {
        $items = db_template::inst()->all();
        $arr = array();
        foreach ($items as $id => $summary) {
            $arr[$id] = new Template($summary);
        }
        return $arr;
    }

    public static function &cachedAll() {
        $cache = cache::instance();
        $all = $cache->load("class.template.all", null);
        if ($all === null) {
            $all = Template::all();
            $cache->save("class.template.all", $all);
        }
        return $all;
    }

    public static function remove($id) {
        return db_template::inst()->remove($id);
    }
};

