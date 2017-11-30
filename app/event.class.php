<?php
include_once(dirname(__FILE__) . "/../config.php");

class Event {
    private $mSummary = null;

    const EVENT_CODES = array(
        10001 => "创建单体活动",
        10002 => "修改单体活动",
        10003 => "暂停单体活动",
        10004 => "启动单体活动",
        10005 => "报名单体活动",
        10006 => "取消报名单体活动",
    );
    
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
    public function activity_detail() {
        $activity = Activity::oneById($this->mSummary["activity"]);
        return $activity ? $activity->packInfo() : $activity;
    }
    public function calendar() {
        return $this->mSummary["calendar"];
    }
    public function calendar_detal() {
        $calendar = Calendar::oneById($this->mSummary["calendar"]);
        return $calendar ? $calendar->packInfo() : $calendar;
    }
    public function event_code() {
        return $this->mSummary["event_code"];
    }
    public function event_content() {
        return self::EVENT_CODES[$this->mSummary["event_code"]];
    }
    public function operator() {
        return $this->mSummary["operator"];
    }
    public function time_stamp() {
        return $this->mSummary["time"];
    }
    public function modify_time() {
        return date("Y-m-d H:i:s",$this->time_stamp());
    }
    public function status() {
        return $this->mSummary["status"];
    }
    public function operator_detail() {
        $operator_id = $this->operator();
        $user = TempUser::oneById($operator_id);
        return $user ? $user->packInfo() : null;
    }


    public function set_activity($n) {
        $this->mSummary["activity"] = $n;
    }
    public function set_calendar($n) {
        $this->mSummary["calendar"] = $n;
    }
    public function set_event_code($n) {
        $this->mSummary["event_code"] = $n;
    }
    public function set_operator($n) {
        $this->mSummary["operator"] = $n;
    }
    public function set_time($n) {
        $this->mSummary["time"] = $n;
    }
    public function set_status($n) {
        $this->mSummary["status"] = $n;
    }

    public function save() {
        $id = $this->id();
        if ($id == 0) {
            $id = db_event::inst()->add($this->activity(), $this->calendar(), $this->event_code(), $this->operator());
            if ($id !== false) {
                $this->mSummary["id"] = $id;
            }
        } else {
            $id = db_event::inst()->modify($this->id(), $this->activity(), $this->calendar(), $this->event_code(), $this->operator(), $this->status());
        }
        return $id;
    }

    public function packInfo($detail = false) {
       return array(
            "id" => $this->id(),
            "activity" => $this->activity(),
            "activity_detail" => $this->activity_detail(),
            "calendar" => $this->calendar(),
            "calendar_detal" => $this->calendar_detal(),
            "event_code" => $this->event_code(),
            "event_content" => $this->event_content(),
            "operator" => $this->operator(),
            "operator_detail" => $this->operator_detail(),
            "time" => $this->time_stamp(),
            "modify_time" => $this->modify_time(),
            "status" => $this->status()
        );
    }

    public static function create($id) {
        $summary = db_event::inst()->get($id);
        return new Event($summary);
    }
    
    public static function record($activity, $calendar, $event_code, $operator) {
        $event = new Event();
            
        $event->set_activity($activity);
        $event->set_calendar($calendar);
        $event->set_event_code($event_code);
        $event->set_operator($operator);

        $ret = $event->save();
        
        return array("ret" => $ret, "event" => $event->packInfo());
    }
    
    public static function edit_one($event_id, $activity,  $calendar,  $event_code,  $operator) {
        
        $event = Event::oneById($event_id);
            
        $event->set_activity($activity);
        $event->set_calendar($calendar);
        $event->set_event_code($event_code);
        $event->set_operator($operator);

        $ret = $event->save();
        
        return array("ret" => $ret, "event" => $event);
    }
    
    public static function oneById($id) {
        $events = self::cachedAll();
        foreach ($events as $event) {
            if ($event->id() == $id) {
                return $event;
            }
        }
        return null;
    }

    public static function all() {
        $items = db_event::inst()->all();
        $arr = array();
        foreach ($items as $id => $summary) {
            $arr[$id] = new Event($summary);
        }
        return $arr;
    }

    public static function &cachedAll() {
        $cache = cache::instance();
        $all = $cache->load("class.event.all", null);
        if ($all === null) {
            $all = Event::all();
            $cache->save("class.event.all", $all);
        }
        return $all;
    }

    public static function remove($id) {
        return db_event::inst()->remove($id);
    }

};


