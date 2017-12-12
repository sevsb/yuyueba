<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");

class event_controller extends v1_base {
    private $mToken = null;
    private $mUser = null;

    public function preaction($action) {
        //$token = get_request_assert("token");
    }
    
    public function get_all_my_list_action(){
        $yuyue_session = get_request("owner");
        logging::d("ORGED_LIST yuyue_session:", $yuyue_session);
        
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        $userid = $user->id();
        $my_organizetions = $user->organizations();
        $my_organizetions = $my_organizetions["my_orgs"];
        
        $my_activity_no_list = array();
        $my_calendar_no_list = array();
        
        $my_subscribe_activity_list = Subscribe::load_subscribe_activity_list($user->id());
        $my_subscribe_calendar_list = Subscribe::load_subscribe_calendar_list($user->id());

        $all_activities = Activity::all();
        $all_calendars = Calendar::all();
        $all_sign = db_sign::inst()->all();
        
        // 遍历activity_list
        foreach ($all_activities as $act) {
            $type = $act->type();
            logging::d("actpe", $type);
            if ($type == 1) {
                if ($act->owner() == $userid) {
                    array_push( $my_activity_no_list, $act->id());
                }
            }else if($type == 2) {
                if (in_array($act->owner(), $my_organizetions)) {
                    array_push( $my_activity_no_list, $act->id());
                }
            }
            foreach ($all_sign as $sign) {
                if ($sign['user'] == $user->id() && $sign['activity'] == $act->id()) {
                    array_push( $my_activity_no_list, $act->id());
                }
            }
            foreach ($my_subscribe_activity_list as $sub) {
                if ($sub['activity'] == $act->id()) {
                    array_push($my_activity_no_list, $act->id());
                }
            }
        }
        
        
        // 遍历calendar_list
        foreach ($all_calendars as $calendar) {
            $type = $calendar->type();
            logging::d("actpe", $type);
            if ($type == 1) {
                if ($calendar->owner() == $userid) {
                    array_push( $my_calendar_no_list, $calendar->id());
                }
            }else if($type == 2) {
                if (in_array($calendar->owner(), $my_organizetions)) {
                    array_push( $my_calendar_no_list, $calendar->id());
                }
            }
            // calendar没有报名，只有订阅
            foreach ($my_subscribe_calendar_list as $sub) {
                if ($sub['calendar'] == $calendar->id()) {
                    array_push($my_calendar_no_list, $calendar->id());
                }
            }
        }
        
        $events = Event::all();
        logging::d("my_activity_no_list ", json_encode($my_activity_no_list));
        logging::d("my_calendar_no_list ", json_encode($my_calendar_no_list));
        $event_list = [];
        
        foreach ($events as $event){
            if (in_array($event->activity(), $my_activity_no_list) || in_array($event->calendar(), $my_calendar_no_list)) {
                $event_list[$event->id()] = $event->packInfo();
            }
        }
        
        return $this->op("event_list", $event_list);
    }

}



function add_month($stamp){
        
    $y = date("Y",$stamp);
    $m = date("n",$stamp);
    $d = date("d",$stamp);
    $h = date("H",$stamp);
    $m = date("m",$stamp);
    $s = date("s",$stamp);
    
    $r = increate_month($y, $m);
    $y = $r['y'];
    $m = $r['m'];
    while(!check_valid($y, $m, $d)){
        $r = increate_month($y, $m);
        $y = $r['y'];
        $m = $r['m'];
    }
    
    $new_date = "$y-$m-$d $h:$m:$s";

    logging::d("add_month", $new_date);
    logging::d("add_month strtotime", strtotime($new_date));
    return strtotime($new_date);
}


function check_valid($y, $m, $d) {
    switch ($m) {
        case 1:
        case 3:
        case 5:
        case 7:
        case 8:
        case 10:
        case 12:
            if ($d > 31 || $d < 0) {
                return false;
            }
            break;
        case 4:
        case 6:
        case 9:
        case 11:
            if ($d > 30 || $d < 0) {
                return false;
            }
            break;
        case 2:
            if ($y % 4 == 0) {
                if ($d > 29 || $d < 0) {
                    return false;
                }
            }else {
                if ($d > 28 || $d < 0) {
                    return false;
                }
            }
            break;
        default:
            return false;
            break;
    }
    return true;
}

function increate_month($y, $m){
    if ($m != 12) {
        $m++;
    }else {
        $m = 1;
        $y++;
    }
    return array("y" => $y, "m" => $m);
}

