<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");

class activity_controller extends v1_base {
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

        $act = Activity::oneById($aid);
        $data = array(
            "info" => array(
                $act->packInfo(true),
            ),
        );
        return $this->op("activity_view", $data);
    }

    public function join_action() {
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
        if ($type == 1) {
            $user = TempUser::oneBySession($yuyue_session);
            if (!$user) {
                return array('op' => 'fail', "code" => '000002', "reason" => '无此用户');
            }
            $owner = $user->id();
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
        
        
        $ret = $activity->save();
        
        return $ret ?  array('op' => 'activity_organize', "data" => $activity->packInfo()) : array('op' => 'fail', "code" => 100002, "reason" => '活动发起失败');
        
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
        $type = activity->type();
        $owner = activity->owner();
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

    public function tipoff_action() {
    }
    
    public function upload_image_action() {
        $image = Upload::upload_image();   //先存图片
        if (!$image) {
            return array('op' => 'fail', "code" => 111, "reason" => '上传图片失败');
        }
        return array('op' => 'upload_image', "data" => $image);
    }
}













