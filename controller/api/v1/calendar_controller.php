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
        logging::d("ACT VIEW", $aid);
        $act = Activity::oneById($aid);
        $data = array(
            "info" => array(
                $act->packInfo(true),
            ),
        );
        return $this->op("activity_view", $act->packInfo());
    }

    public function sign_action() {
        $activity_id = get_request("activity_id");
        $yuyue_session = get_request("yuyue_session");
        $sheet = get_request("sheet");

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
        $now_participants = $activity->now_participants();
        $max_participants = $activity->max_participants();
        if ($now_participants >= $max_participants) {
            return array('op' => 'fail', "code" => '203402', "reason" => '此活动报名额度已经满额');
        }
        $deadline = $activity->deadline();
        if (time() >= $deadline) {
            return array('op' => 'fail', "code" => '203407', "reason" => '此活动报名截止时间已过，无法报名');
        }
        
        $userid = $user->id();
        $sign = db_sign::one($activity_id, $userid);
        if ($sign) {
            return array('op' => 'fail', "code" => 1033002, "reason" => '用户已经报名过此活动');
        }
        $ret = db_sign::add($activity_id, $userid, json_encode($sheet));
        return $ret ?  array('op' => 'activity_sign', "data" => $ret) : array('op' => 'fail', "code" => 1033002, "reason" => '活动报名失败');
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
        $sign = db_sign::one($activity_id, $userid);
        if (!$sign) {
            return array('op' => 'fail', "code" => 1033002, "reason" => '用户尚未报名过此活动');
        }
        $ret = db_sign::del($activity_id, $userid);
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
        
        return $ret ?  array('op' => 'activity_organize', "data" => $activity->packInfo()) : array('op' => 'fail', "code" => 100002, "reason" => '活动发起失败');
        
    }

    public function edit_action() {
        $calendar_id = get_request("id");
        $title = get_request("title");
        $content = get_request("content");
        $type = get_request("type");
        $owner = get_request("owner");
        $yuyue_session = get_request("yuyue_session");

        $user = TempUser::oneBySession($yuyue_session);
        if (empty($user)) {
            return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
        }
        if ($type == 1) {
            $yuyue_session = $owner;
            $user = TempUser::oneBySession($yuyue_session);
            if (!$user) {
                return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
            }
            $owner = $user->id();
        }
        
        //$type = get_request("type");    //1: 个人 2：组织
        //$owner = get_request("owner");  // 个人yuyue_session or 组织ID;
        if (!empty($calendar_id)) {
            $calendar = Calendar::oneById($calendar_id);
            if (empty($calendar)) {
                return array('op' => 'fail', "code" => 00022201, "reason" => '活动不存在');
            }
        }else {
            $calendar = new Calendar();
        }
        $calendar->setContent($content);
        $calendar->setTitle($title);
        $calendar->setOwner($owner);
        $calendar->set_Type($type);
        
        //logging::d('calendar', dump_var($calendar));
        //return;
        $ret = $calendar->save();
        
        return $ret ?  array('op' => 'calendar_edit', "data" => $calendar->packInfo()) : array('op' => 'fail', "code" => 100742, "reason" => '日历活动编辑失败');
        
        return;
        
        
        
        
        
        
        
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

        $joinable = get_request("joinable");
        $participants = get_request("participants", 0);
        
        $title = get_request("title");
        $info = get_request("info");
        $images = get_request("images");
        $content = get_request("content");
        
        $begintime = get_request("begintime");
        $endtime = get_request("endtime");
        $deadline = get_request("deadline");
        
        $address = get_request("address");
        
        $repeattype = get_request("repeattype", "once");
        $repeatcount = get_request("repeatcount", 0);
        
        $joinsheet = get_request("joinsheet");
        

        if ($type != 1 || $type != 2 || empty($owner)) {
            return array('op' => 'fail', "code" => 000001, "reason" => '活动类型或创建者信息不完整');
        }

        if (empty($title) ||　empty($info) ||　empty($content) ) {
            return array('op' => 'fail', "code" => 000002, "reason" => '活动标题，简介，详情不完整');
        }
        if (empty($begintime) ||　empty($endtime)) {
            return array('op' => 'fail', "code" => 000003, "reason" => '活动开始时间，结束时间不完整');
        }
        if (empty($address)) {
            return array('op' => 'fail', "code" => 000004, "reason" => '活动地址不完整');
        }
        
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
        
        $ret = $activity->save();
        
        return $ret ?  array('op' => 'activity_edit', "data" => $activity->packInfo()) : array('op' => 'fail', "code" => 1000042, "reason" => '活动编辑失败');
        
        
    }

    public function viewmember_action() {
    }
    
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













