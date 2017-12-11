<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");

class calendar_controller extends v1_base {
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

/*
    public function my_created_list_action() {
        $yuyue_session = get_request("yuyue_session");
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        $userid = $user->id();
        $organizations = $user->organizations();
        logging::d("organizations", $organizations);
        
        $my_created_list = [];
        
        $all_calendar = Calendar::all();
        foreach ($all_calendar as $calendar) {
            $type = $calendar->type();
            if ($type == 1) {
                if($calendar->owner() == $userid){
                    $my_created_list[$calendar->id()] = $calendar->packInfo();
                }
            }else if ($type == 2) {
                if (in_array($calendar->owner(), $organizations["my_orgs"])) {
                    $my_created_list[$calendar->id()] = $calendar->packInfo();
                }
            }
        }
        return $this->op("my_created_list", $my_created_list);
    }
*/
    public function my_calendar_list_action() {
        
        $yuyue_session = get_request("yuyue_session");
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        
        $userid = $user->id();
        $organizations = $user->organizations();
        logging::d("organizations", $organizations);
        
        $my_created_list = [];
        $my_org_created_list = [];
        $my_focus_list = [];
        $my_joined_list = [];
        $my_subscribe_list = [];
        
        $all_calendar = Calendar::all();
        $all_my_subscribe_calendar_list = Subscribe::load_subscribe_calendar_list($user->id());
        foreach ($all_calendar as $calendar) {
            $type = $calendar->type();
            if ($type == 1) {
                if($calendar->owner() == $userid){
                    $my_created_list[$calendar->id()] = $calendar->packInfo();
                }
            }else if ($type == 2) {
                if (in_array($calendar->owner(), $organizations["my_orgs"])) {
                    $my_org_created_list[$calendar->id()] = $calendar->packInfo();
                }
            }
            
            foreach ($all_my_subscribe_calendar_list as $sub) {
                if ($sub['calendar'] == $calendar->id()) {
                    $my_subscribe_list[$calendar->id()] = $calendar->packInfo();
                }
            }
        }
        
        $data = array(
            "my_created_list" => $my_created_list,
            "my_org_created_list" => $my_org_created_list,
            "my_joined_list" => $my_joined_list,
            "my_subscribe_list" => $my_subscribe_list,
        );
        
        return $this->op("my_calendar_list", $data);
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
        $id = get_request_assert("id");
        $yuyue_session = get_request("yuyue_session");
        

        $calendar = Calendar::oneById($id);
        logging::d("CAL VIEW", $id);
        logging::d("ACT yuyue_session", $yuyue_session);
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        
        $editable = false;
        if ($calendar->type() == 1) {
            if ($calendar->owner() == $user->id()) {
                $editable = true;
            }
        }else if ($calendar->type() == 2) {
            $user_orgs = $user->organizations();
            if (in_array($calendar->owner(), $user_orgs['my_orgs'])) {
                $editable = true;
            }
        }

        $data = array(
            "info" => $calendar->packInfo(true),
            "editable" => $editable,
        );
        
        return $this->op("calendar_view", $data);
    }

    public function mine_action() {
    }

    public function reply_action() {
    }

    /*
    public function organize_action() {

        $type = get_request("type");    //1: 个人 2：组织
        $owner = get_request("owner");  // 个人yuyue_session or 组织ID;
        
        $yuyue_session = get_request("yuyue_session");  // yuyue_session 

        $joinable = get_request("joinable");
        $participants = get_request("participants", 0);
        
        $title = get_request("title");
        $info = get_request("info", "缺省");
        $images = get_request("images");
        $content = get_request("content");
        
        $begintime = get_request("begintime");
        $endtime = get_request("endtime");
        $deadline = get_request("deadline");
        
        $address = get_request("address");
        
        $repeattype = get_request("repeattype", "once");
        $repeatcount = get_request("repeatcount", 0);
        
        $joinsheet = get_request("joinsheet");
        
        //logging::d("ACT type", ($type));
        //logging::d("ACT owner", ($owner));
        if (($type != 1 && $type != 2) || empty($owner)) {
            return array('op' => 'fail', "code" => 000001, "reason" => '活动类型或创建者信息不完整');
        }
        
        $user = TempUser::oneBySession($yuyue_session);
        if (!$user) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        if ($type == 1) {
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
        
        $activity = new Activity();
        
        $activity->set_Type($type);
        $activity->setOwner($owner);
        
        $activity->setJoinable($joinable);
        $activity->setParticipants($participants);
        
        $activity->setTitle($title);
        $activity->setInfo($info);
        $activity->setContent($content);
        $activity->setImages($images);
        
        $activity->setBegintime($begintime);
        $activity->setEndtime($endtime);
        $activity->setDeadline($deadline);
        
        $activity->setAddress($address);
        
        $activity->setRepeattype($repeattype);
        $activity->setRepeatcount($repeatcount);
        $activity->setJoinsheet($joinsheet);
        
        $activity->setStatus(0);
        
        //logging::d("ACT CREATE", dump_var($activity));
        //return;
        $ret = $activity->save();
        
        $ret ? $record = Event::record(0, $calendar_id, "20001", $user->id()) : 0;
        logging::d("ACT record", dump_var($record));
        return $ret ?  array('op' => 'activity_organize', "data" => $activity->packInfo()) : array('op' => 'fail', "code" => 100002, "reason" => '活动发起失败');
        
    }
    */

    public function edit_action() {
        $calendar_id = get_request("id");
        $title = get_request("title");
        $content = get_request("content");
        $type = get_request("type");
        $owner = get_request("owner");
        $yuyue_session = get_request("yuyue_session");

        //logging::d("ACT owner", dump_var($owner));
        
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '0010002', "reason" => '无此用户');
        }
        if ($type == 1) {
            $owner = $user->id();
        }

        if (!empty($calendar_id)) {
            $calendar = Calendar::oneById($calendar_id);
            if (empty($calendar)) {
                return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
            }
            if ($calendar->type() == 1) {
                if ($calendar->owner() != $user->id()) {
                    return array('op' => 'fail', "code" => 0022201, "reason" => '无权利编辑此日历活动1');
                }
            }else if ($calendar->type() == 2) {
                $organizations = $user->organizations();
                $my_orgs = $organizations["my_orgs"];
                if ( !in_array($calendar->owner(), $my_orgs)) {
                    return array('op' => 'fail', "code" => 0022222, "reason" => '无权利编辑此日历活动2');
                }
            }
            
        }else {
            $calendar = new Calendar();
        }
        $calendar->setContent($content);
        $calendar->setTitle($title);
        $calendar->setOwner($owner);
        $calendar->set_Type($type);
        
        $ret = $calendar->save();
        $calendar_id ? $event_code = 20002 : $event_code = 20001;
        $ret ? $record = Event::record(0, $calendar->id(), $event_code, $user->id()) : 0;
        return $ret ?  array('op' => 'calendar_edit', "data" => $calendar->packInfo()) : array('op' => 'fail', "code" => 100742, "reason" => '日历活动编辑失败');
        
        return;
        
    }

    public function viewmember_action() {
    }
    
    /*
    public function cancel_action() {
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
        $ret = Activity::cancel($activity_id);
        
        return $ret ?  array('op' => 'activity_cancel', "data" => $activity->packInfo()) : array('op' => 'fail', "code" => 55042, "reason" => '活动撤消失败');
        
    }
    */

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
    
    public function check_subscribe_action(){
        $calendar_id = get_request_assert("calendar");
        $yuyue_session = get_request("yuyue_session");

        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }

        $subscribe = Subscribe::load(0, $calendar_id, $user->id());
        return array('op' => 'calendar_check_subscribe', "data" => $subscribe);
    }
    
    public function subscribe_action() {
        $calendar_id = get_request("calendar");
        $yuyue_session = get_request("yuyue_session");
        logging::d("aid", $calendar_id);
        $calendar = Calendar::oneById($calendar_id);
        if (empty($calendar)) {
            return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
        }
        
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }

        $userid = $user->id();
        $type = $calendar->type();
        $owner = $calendar->owner();
        logging::d("userid", $userid);
        $ret = $calendar->subscribe($userid);
        $subscribe = Subscribe::load(0, $calendar_id, $user->id());
        $ret ? $record = Event::record(0, $calendar_id, "20010", $userid) : 0;
        return $ret ?  array('op' => 'calendar_subsrcibe', "data" => $subscribe) : array('op' => 'fail', "code" => 566642, "reason" => '日历活动关注失败');
        
    }

    public function unsubscribe_action() {
        $calendar_id = get_request("calendar");
        $yuyue_session = get_request("yuyue_session");

        $calendar = Calendar::oneById($calendar_id);
        if (empty($calendar)) {
            return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
        }
        
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }

        $userid = $user->id();
        $type = $calendar->type();
        $owner = $calendar->owner();
        
        $ret = $calendar->unsubscribe($userid);
        $subscribe = Subscribe::load(0, $calendar_id, $user->id());
        //$ret ? $record = Event::record($activity->id(), $activity->calendar_id(), "10011", $userid) : 0;
        return $ret ?  array('op' => 'calendar_unsubscribe', "data" => $subscribe) : array('op' => 'fail', "code" => 5666742, "reason" => '日历活动取消关注失败');
        
    }
    
    
    
    
    
}













