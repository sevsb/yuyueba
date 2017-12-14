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
        
        $my_activity_event_list = [];
        $my_calendar_event_list = [];
        
        $my_activity_event_list = Event::get_activity_event_list($userid);
        
        $event_code = Event::EVENT_CODES;
        
        $res_my_activity_event_list = array();
        
        foreach ($my_activity_event_list as $id => $event) {
            $res_my_activity_event_list[$event['time']] = $event;
            $res_my_activity_event_list[$event['time']]['event_content'] = $event_code[$event['event_code']];
        }
        
        $data = array(
            "my_activity_event_list" => $res_my_activity_event_list,
            "my_calendar_event_list" => $my_calendar_event_list
        );
        
        return $this->op("event_list", $data);
        
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

