<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");

class event_controller extends v1_base {
    private $mToken = null;
    private $mUser = null;

    public function preaction($action) {
        //$token = get_request_assert("token");
    }

    public function list_action() {
        $sort = get_request("sort", 0);
        $display = get_request("display", "0,20");

        $act1 = new Activity();
        $act2 = new Activity();
        $data = array(
            "activities" => array(
                $act1->packInfo(),
                $act2->packInfo(),
            ),
        );
        return $this->op("activities", $data);
    }
    
    public function my_test_action(){
        $owner = get_request("owner");
        logging::d("ORGED_LIST owner:", $owner);
        activity_controller::organized_all_my_list_action($owner);
    }
    
    public function get_all_my_list_action(){
        $owner = get_request("owner");
        logging::d("ORGED_LIST owner:", $owner);
        
        $user = TempUser::oneBySession($owner);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        $owner = $user->id();
        $my_organizetions = $user->organizations();
        $my_organizetions = $my_organizetions["my_orgs"];
        
        $my_create_activity_list = array();
        $my_org_create_activity_list = array();
        $my_join_activity_list = array();

        $all_activities = Activity::all();
        $all_sign = db_sign::inst()->all();

        foreach ($all_activities as $act) {
            $type = $act->type();
            logging::d("actpe", $type);
            if ($type == 1) {
                if ($act->owner() == $owner) {
                    $my_create_activity_list[$act->id()] = $act->packInfo();
                }
            }else if($type == 2) {
                if (in_array($act->owner(), $my_organizetions)) {
                    $my_org_create_activity_list[$act->id()] = $act->packInfo();
                }
            }
            foreach ($all_sign as $sign) {
                if ($sign['user'] == $user->id() && $sign['activity'] == $act->id()) {
                    $my_join_activity_list[$act->id()] = $act->packInfo();
                }
            }
        }
        $data = array("my_create_activity_list" => $my_create_activity_list, "my_org_create_activity_list" => $my_org_create_activity_list, "my_join_activity_list" => $my_join_activity_list);
        return $this->op("organized_all_my_list", $data);
    }

    public function organized_list_action() {
        $type = get_request("type");
        $owner = get_request("owner");
        logging::d("ORGED_LIST type:", $type);
        logging::d("ORGED_LIST owner:", $owner);
        if($type == 1){
            $user = TempUser::oneBySession($owner);
            if (empty($user)) {
                return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
            }
            $owner = $user->id();
        }else if ($type == 2){
            $organization = Organization::oneById($owner);
            if (empty($organization)) {
                return array('op' => 'fail', "code" => '000003', "reason" => '组织不存在');
            }
        }else {
            return array('op' => 'fail', "code" => '000004', "reason" => 'type错误');
        }

        $all_activities = Activity::all();
        $ret = [];
        foreach ($all_activities as $act) {
            if ($act->owner() == $owner) {
                $ret[$act->id()] = $act->packInfo();
            }
        }
        return $this->op("organized_list", $ret);
    }
    
    public function organized_all_my_list_action() {    //列出此人所有相关的activity，包括自身创建，创建组织创建，所加入组织创建
        $owner = get_request("owner");
        logging::d("ORGED_LIST owner:", $owner);
        
        $user = TempUser::oneBySession($owner);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        $owner = $user->id();
        $my_organizetions = $user->organizations();
        $my_organizetions = $my_organizetions["my_orgs"];
        
        $my_create_activity_list = array();
        $my_org_create_activity_list = array();
        $my_join_activity_list = array();

        $all_activities = Activity::all();
        $all_sign = db_sign::inst()->all();

        foreach ($all_activities as $act) {
            $type = $act->type();
            logging::d("actpe", $type);
            if ($type == 1) {
                if ($act->owner() == $owner) {
                    $my_create_activity_list[$act->id()] = $act->packInfo();
                }
            }else if($type == 2) {
                if (in_array($act->owner(), $my_organizetions)) {
                    $my_org_create_activity_list[$act->id()] = $act->packInfo();
                }
            }
            foreach ($all_sign as $sign) {
                if ($sign['user'] == $user->id() && $sign['activity'] == $act->id()) {
                    $my_join_activity_list[$act->id()] = $act->packInfo();
                }
            }
        }
        $data = array("my_create_activity_list" => $my_create_activity_list, "my_org_create_activity_list" => $my_org_create_activity_list, "my_join_activity_list" => $my_join_activity_list);
        return $this->op("organized_all_my_list", $data);
    }

    public function search_action() {
        $s = get_request_assert("s");

        $act1 = new Activity();
        $act2 = new Activity();
        $data = array(
            "activities" => array(
                $act1->packInfo(),
                $act2->packInfo(),
            ),
        );
        return $this->op("activities", $data);
    }
    
    public function view_action() {
        $aid = get_request_assert("activity");
        $yuyue_session = get_request("yuyue_session");
        logging::d("ACT aid", $aid);
        logging::d("ACT yuyue_session", $yuyue_session);
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        
        $editable = false;
        $joined = false;
        $join_sheet = array();
        $act = Activity::oneById($aid);
        
        if ($act->type() == 1) {
            if ($act->owner() == $user->id()) {
                $editable = true;
            }
        }else if ($act->type() == 2) {
            $user_orgs = $user->organizations();
            if (in_array($act->owner(), $user_orgs['my_orgs'])) {
                $editable = true;
            }
        }
        
        $all_sign = Sign::all();
        //logging::d("all_sign", json_encode($all_sign));
        foreach ($all_sign as $sign) {
            if ($sign->calendar() == 0) {
                if ($sign->user() == $user->id() && $sign->activity() == $aid) {
                    $joined = true;
                    $join_sheet = $sign->sheet();
                }
            }
        }
        
        $data = array(
            "info" => $act->packInfo(true),
            "editable" => $editable,
            "joined" => $joined,
            "join_sheet" => $join_sheet
        );
        return $this->op("activity_view", $data);
    }

    public function sign_action() {
        $activity_id = get_request("activity_id");
        $yuyue_session = get_request("yuyue_session");
        $sheet = get_request("sheet");

        logging::d("sheet", $sheet);
        $activity = Activity::oneById($activity_id);
        if (empty($activity)) {
            return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
        }
        
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        
        $joinable = $activity->joinable();
        if ($joinable == 0) {
            return array('op' => 'fail', "code" => '20302', "reason" => '此活动无法报名');
        }
        if ($activity->status() == 1) {
            return array('op' => 'fail', "code" => '203023', "reason" => '此活动已暂停');
        }
        $now_participants = $activity->now_participants();
        $max_participants = $activity->max_participants();
        if ($now_participants >= $max_participants && $max_participants != 0) {
            return array('op' => 'fail', "code" => '203402', "reason" => '此活动报名额度已经满额');
        }
        
        $userid = $user->id();
        
        $sign = Sign::oneByAidUser($activity_id, $userid);
        //$sign = db_sign::inst()->one($activity_id, $userid);
        if ($sign) {
            $sign->set_sheet($sheet);
            $ret = $sign->save();

            return $ret ?  array('op' => 'activity_sign', "data" => $sign->packInfo()) : array('op' => 'fail', "code" => 1033022, "reason" => '活动报名修改失败');
        }else {
            $sign = new Sign();
            $sign->set_activity($activity_id);
            $sign->set_calendar(0);
            $sign->set_user($userid);
            $sign->set_sheet($sheet);
            $ret = $sign->save();
            
            return $ret ?  array('op' => 'activity_sign', "data" => $sign->packInfo()) : array('op' => 'fail', "code" => 1033002, "reason" => '活动报名失败');
        }

    }
    
    public function unsign_action() {
        $activity_id = get_request("activity_id");
        $yuyue_session = get_request("yuyue_session");

        $activity = Activity::oneById($activity_id);
        if (empty($activity)) {
            return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
        }
        
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        $userid = $user->id();
        
        $sign = Sign::oneByAidUser($activity_id, $userid);
        if (!$sign) {
            return array('op' => 'fail', "code" => 1033002, "reason" => '用户尚未报名过此活动');
        }
        $ret = $sign->cancel();
        return $ret ?  array('op' => 'activity_unsign', "data" => $ret) : array('op' => 'fail', "code" => 1033002, "reason" => '退出活动/取消报名失败');
    }

    public function mine_action() {
    }

    public function reply_action() {
    }

    public function organize_action() {

        $type = get_request("type");    //1: 个人 2：组织
        $owner = get_request("owner");  // 个人yuyue_session or 组织ID;

        $joinable = get_request("joinable");
        $participants = get_request("participants", 0);
        
        $title = get_request("title");
        $info = get_request("info", "缺省");
        $images = get_request("images");
        $content = get_request("content");
        
        $begintime = get_request("begintime");
        $endtime = get_request("endtime");
        
        $address = get_request("address");
        
        $repeattype = get_request("repeattype", "once");
        $repeatcount = get_request("repeatcount", 0);
        $repeatend = get_request("repeatend");
        
        $joinsheet = get_request("joinsheet");
        
        $calendar_id = get_request("calendar_id");
        
        logging::d("ACT calendar_id", ($calendar_id));
        //logging::d("ACT owner", ($owner));
        if (($type != 1 && $type != 2) || empty($owner)) {
            return array('op' => 'fail', "code" => 000001, "reason" => '活动类型或创建者信息不完整');
        }
        if ($type == 1) {
            $yuyue_session = $owner;
            $user = TempUser::oneBySession($yuyue_session);
            if (!$user) {
                return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
            }
            $owner = $user->id();
        }
        if (empty($title) || empty($info) || empty($content) ) {
            return array('op' => 'fail', "code" => 000002, "reason" => '活动标题，简介，详情不完整');
        }
        if (empty($begintime) || empty($endtime)) {
            return array('op' => 'fail', "code" => 000003, "reason" => '活动开始时间，结束时间不完整');
        }
        if (empty($address)) {
            return array('op' => 'fail', "code" => 000004, "reason" => '活动地址不完整');
        }
        
        if ($calendar_id == 0) {
            $result = Activity::build_one($type, $owner, $joinable, $participants, $title, $info, $content, $images, $begintime, $endtime, $repeatend, $address, $repeattype, $repeatcount, $joinsheet, $calendar_id);
            
            $ret = $result['ret'];
            $activity = $result['activity'];
            
            return $ret ?  array('op' => 'activity_organize', "data" => $activity->packInfo()) : array('op' => 'fail', "code" => 100002, "reason" => '活动发起失败');
        
        }else {
        
            logging::d("ACT begintime", ($begintime));
            logging::d("ACT endtime", ($endtime));
            
            logging::d("ACT repeattype", ($repeattype));
            logging::d("ACT repeatcount", ($repeatcount));
            logging::d("ACT repeatend", ($repeatend));
            
            //repeattypes: ["仅一次", "每天", "每周", "隔周", "每月"],
            //repeatcounts: ["once", "daily", "weekly", "fortnightly", "monthly"],
            $duration = $endtime - $begintime;
            $timestamp_array = [];
            switch ($repeattype) {
                case 'once': 
                    $timestamp_start = $begintime;
                    array_push($timestamp_array, $timestamp_start);
                    break;
                case 'daily': 
                    $timestamp_start = $begintime;
                    while ($timestamp_start <= $repeatend) {
                        logging::d("ACT timestamp_start", ($timestamp_start));
                        array_push($timestamp_array, $timestamp_start);
                        $timestamp_start += 60 * 60 * 24;
                    }
                    break;
                    
                case 'weekly': 
                    $timestamp_start = $begintime;
                    while ($timestamp_start <= $repeatend) {
                        array_push($timestamp_array, $timestamp_start);
                        $timestamp_start += 60 * 60 * 24 * 7;
                    }
                    break;

                case 'fortnightly': 
                    $timestamp_start = $begintime;
                    while ($timestamp_start <= $repeatend) {
                        array_push($timestamp_array, $timestamp_start);
                        $timestamp_start += 60 * 60 * 24 * 7 * 2;
                    }
                    break;

                case 'monthly': 
                    $timestamp_array = [];
                    $timestamp_start = $begintime;
                    while ($timestamp_start <= $repeatend) {
                        array_push($timestamp_array, $timestamp_start);
                        $timestamp_start = add_month($timestamp_start);

                    }
                    break;    

                default: 
                    break;
            }
            logging::d("timestamp_array", json_encode($timestamp_array));  
            $ret = true;
            
            $calendar = Calendar::oneById($calendar_id);
            
            $activity_list = $calendar->activity_list();
            empty($activity_list) ? $activity_list = [] : $activity_list = $activity_list;
            $db_activity = db_activity::inst();
            $db_activity->begin_transaction();
            foreach ($timestamp_array as $begintime) {
                $result = Activity::build_one($type, $owner, $joinable, $participants, $title, $info, $content, $images, $begintime, $begintime + $duration, $repeatend, $address, $repeattype, $repeatcount, $joinsheet, $calendar_id);
                
                $add_ret = $result['ret'];
                $activity_id = $result['activity']->id();
                logging::d("add_ret", $add_ret);
                logging::d("activity_id", $activity_id);
                array_push($activity_list, $activity_id);
                $ret = $ret && $add_ret;
                logging::d("ret", $ret);
            }
            if (!$ret) {
                $db_activity->rollback();
                return array('op' => 'fail', "code" => 100002, "reason" => '日历活动activity发起失败');
            }
            logging::d("activity_list", json_encode($activity_list));
            
            $calendar->set_activity_list($activity_list);
            
            $ret = $calendar->save();
            if ($ret) {
                $db_activity->commit();
            }else {
                $db_activity->rollback();
            }
            return $ret ?  array('op' => 'activity_organize', "data" => "") : array('op' => 'fail', "code" => 100044, "reason" => '修改日历活动list失败');

        }
        
    }

    public function edit_action() {
        $activity_id = get_request("activity_id");
        $yuyue_session = get_request("yuyue_session");
        
        //$type = get_request("type");    //1: 个人 2：组织
        //$owner = get_request("owner");  // 个人yuyue_session or 组织ID;
        $activity = Activity::oneById($activity_id);
        if (empty($activity)) {
            return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
        }
        
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        
        $userid = $user->id();
        $type = $activity->type();
        $owner = $activity->owner();
        
        if ($type == 1) {
            if ($owner != $userid){
                return array('op' => 'fail', "code" => '0023002', "reason" => '用户无权限编辑此活动');
            }
        }else if($type == 2) {
            if (!(Organization::has_member($owner, $userid))) {
                return array('op' => 'fail', "code" => '0023032', "reason" => '用户无权限编辑此活动');
            }
        }

        $title = get_request("title");
        $content = get_request("content");
        $address = get_request("address");
        $images = get_request("images");
        
        $batch = get_request("batch");
        
        if (empty($title) || empty($content)) {
            return array('op' => 'fail', "code" => 000002, "reason" => '活动标题，简介，详情不完整');
        }
        if (empty($address)) {
            return array('op' => 'fail', "code" => 000004, "reason" => '活动地址不完整');
        }
        
        if ($batch == 0) {
            $result = Activity::edit_one($activity_id, $title,  $content,  $address,  $images);
            logging::d('edit', json_encode($activity));
            $record = Event::record($activity->id(), $activity->calendar_id(), 10002, $userid);
            logging::d('edit', json_encode($result));
            logging::d('record', json_encode($record));
            $ret = $result['ret'];
            $activity = $result['activity'];
            return $ret ?  array('op' => 'activity_edit', "data" => $activity->packInfo(true)) : array('op' => 'fail', "code" => 104042, "reason" => '活动修改失败');
        }else if ($batch == 1) {
            logging::d('edit', json_encode($batch));
        }
    
        
        //return $ret ?  array('op' => 'activity_edit', "data" => $activity->packInfo()) : array('op' => 'fail', "code" => 1000042, "reason" => '活动编辑失败');
        
        
    }

    public function view_join_list_action() {
        $activity_id = get_request("activity_id");
        $yuyue_session = get_request("yuyue_session");

        $activity = Activity::oneById($activity_id);
        if (empty($activity)) {
            return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
        }
        
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        
        $all_sign = Sign::all();
        //logging::d("all_sign", json_encode($all_sign));
        $join_list = array();
        foreach ($all_sign as $sign) {
            if ($sign->calendar() == 0) {
                if ($sign->activity() == $activity_id) {
                    $join_list[$sign->id()] = $sign->packInfo();
                }
            }
        }
        return array('op' => 'view_join_list', "data" => $join_list);
        
        
    }
    
    public function cancel_action() {
        $activity_id = get_request("activity_id");
        $yuyue_session = get_request("yuyue_session");
        $batch = get_request("batch");

        $activity = Activity::oneById($activity_id);
        if (empty($activity)) {
            return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
        }
        
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        $userid = $user->id();
        $type = $activity->type();
        $owner = $activity->owner();
        if ($type == 1) {
            if ($owner != $userid){
                return array('op' => 'fail', "code" => '0023002', "reason" => '用户无权限编辑此活动');
            }
        }else if($type == 2) {
            if (!(Organization::has_member($owner, $userid))) {
                return array('op' => 'fail', "code" => '0023032', "reason" => '用户无权限编辑此活动');
            }
        }
        if ($activity->status() == 1) {
            return array('op' => 'fail', "code" => '00244032', "reason" => '此活动已被撤消');
        }
        $ret = $activity->cancel();
        
        return $ret ?  array('op' => 'activity_cancel', "data" => $activity->packInfo()) : array('op' => 'fail', "code" => 55042, "reason" => '活动撤消失败');
        
    }
    
    public function start_action() {
        $activity_id = get_request("activity_id");
        $yuyue_session = get_request("yuyue_session");
        $batch = get_request("batch");

        $activity = Activity::oneById($activity_id);
        if (empty($activity)) {
            return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
        }
        
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        $userid = $user->id();
        $type = $activity->type();
        $owner = $activity->owner();
        if ($type == 1) {
            if ($owner != $userid){
                return array('op' => 'fail', "code" => '0023002', "reason" => '用户无权限编辑此活动');
            }
        }else if($type == 2) {
            if (!(Organization::has_member($owner, $userid))) {
                return array('op' => 'fail', "code" => '0023032', "reason" => '用户无权限编辑此活动');
            }
        }
        if ($activity->status() == 0) {
            return array('op' => 'fail', "code" => '00244032', "reason" => '此活动已启动');
        }
        $ret = $activity->start();
        
        return $ret ?  array('op' => 'activity_start', "data" => $activity->packInfo()) : array('op' => 'fail', "code" => 525042, "reason" => '活动启动失败');
        
    }

    public function tipoff_action() {
    }
    
    public function signed_user_list_action() {
        $activity_id = get_request("activity_id");
        
        $activity = Activity::oneById($activity_id);
        if (empty($activity)) {
            return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
        }
        
        $signed_user_list = $activity->sign_user_list();
        return array('op' => 'signed_user_list', "data" => $signed_user_list);
    }
    
    public function upload_image_action() {
        $image = Upload::upload_image();   //先存图片
        $thumbnail = Upload::mkUploadThumbnail($image, 200, 200);
        if (!$image || !$thumbnail) {
            return array('op' => 'fail', "code" => 111, "reason" => '上传图片失败');
        }
        return array('op' => 'upload_image', "data" => $image);
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

