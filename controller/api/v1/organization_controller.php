<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");

class organization_controller extends v1_base {
    public function preaction($action) {
    }

    public function create_action() {
        $org_name = urldecode(get_request('org_name'));
        $org_intro = urldecode(get_request('org_intro'));
        $yuyue_session = get_request('yuyue_session');
        $token = get_request('token');
        
        logging::d('create_action','org_name' . $org_name);
        logging::d('create_action','org_intro' . $org_intro);
        $org_avatar = Upload::upload_image();   //先存图片
        if (!$org_avatar) {
            return array('op' => 'fail', "code" => '???', "reason" => '上传图片失败');
        }
    
        $user = TempUser::oneBySession($yuyue_session);
        if (!$user) {
            return array('op' => 'fail', "code" => '???', "reason" => '无此用户');
        }
        $own_id = $user->id();
        
        $organization = new Organization();
        
        $organization->setName($org_name);
        $organization->setIntro($org_intro);
        $organization->setOwner($own_id);
        $organization->setAvatar($org_avatar);
        
        logging::d('org_create', 'org_name:' . $organization->name());
        logging::d('org_create', 'org_intro:' . $organization->intro());
        logging::d('org_create', 'own_id:' . $organization->owner());
        logging::d('org_create', 'org_avatar:' . $organization->avatar());
        
        $organization->save();
        $data = $organization->packInfo();

        return array('op' => 'org_create', "data" =>  $data);
    }
	public function setPassword_action() {
		$org_id = get_request('org_id');
		$password = get_request('password');
		$yuyue_session = get_request('yuyue_session');
		logging::d('setPassword','org_id' . $org_id);
		$user = TempUser::oneBySession($yuyue_session);
        if (!$user) {
            return array('op' => 'fail', "code" => '30002', "reason" => '无此用户');
        }
        $userid = $user->id();
		$organization = Organization::oneById($org_id);

        $owner_id = $organization->owner();
        if ($owner_id != $userid) {
            return array('op' => 'fail', "code" => '30003', "reason" => '此用户不是组织的发起者，无法修改');
        }
        if ($organization->type() == 1) {
            return array('op' => 'fail', "code" => '555', "reason" => '此组织已经被解散');
        }
		logging::d('setPassword','password' . $password);
		if (!$password) {
            return array('op' => 'fail', "code" => '3333', "reason" => '空口令');
        }
		$organization->setPassword($password);
		logging::d('setPassword','password' . $password);
		$organization->save();
		$data = $organization->packInfo();

        return array('op' => 'setPassword', "data" => $data);
	}
	
	public function setName_action() {
		$org_id = get_request('org_id');
		$org_name = get_request('org_name');
		$yuyue_session = get_request('yuyue_session');
		logging::d('setName','org_id' . $org_id);
		$user = TempUser::oneBySession($yuyue_session);
        if (!$user) {
            return array('op' => 'fail', "code" => '30002', "reason" => '无此用户');
        }
        $userid = $user->id();
		$organization = Organization::oneById($org_id);

        $owner_id = $organization->owner();
        if ($owner_id != $userid) {
            return array('op' => 'fail', "code" => '30003', "reason" => '此用户不是组织的发起者，无法修改');
        }
        if ($organization->type() == 1) {
            return array('op' => 'fail', "code" => '555', "reason" => '此组织已经被解散');
        }
		logging::d('setName','org_name' . $org_name);
		if (!$org_name) {
            return array('op' => 'fail', "code" => '3333', "reason" => '空口令');
        }
		$organization->setName($org_name);
		logging::d('setName','org_name' . $org_name);
		$organization->save();
		$data = $organization->packInfo();

        return array('op' => 'setName', "data" => $data);
	}
	
    public function edit_action() {
        $org_id = get_request('org_id');
        $org_name = urldecode(get_request('org_name'));
        $org_intro = urldecode(get_request('org_intro'));
        $yuyue_session = get_request('yuyue_session');
        $token = get_request('token');
        
        logging::d('create_action','org_name' . $org_name);
        logging::d('create_action','org_intro' . $org_intro);
        
        if($_FILES['file']){
            $org_avatar = Upload::upload_image();   //先存图片
            if (!$org_avatar) {
                return array('op' => 'fail', "code" => '30001', "reason" => '上传图片失败');
            }
        }
        
        $user = TempUser::oneBySession($yuyue_session);
        if (!$user) {
            return array('op' => 'fail', "code" => '30002', "reason" => '无此用户');
        }
        $userid = $user->id();
        
        
        $organization = Organization::oneById($org_id);

        $owner_id = $organization->owner();
        if ($owner_id != $userid) {
            return array('op' => 'fail', "code" => '30003', "reason" => '此用户不是组织的发起者，无法修改');
        }
        if ($organization->type() == 1) {
            return array('op' => 'fail', "code" => '555', "reason" => '此组织已经被解散');
        }
        $organization->setName($org_name);
        $organization->setIntro($org_intro);
        
        if ($_FILES['file']) {
            $organization->setAvatar($org_avatar);
        }
        
        logging::d('org_create', 'org_name:' . $organization->name());
        logging::d('org_create', 'org_intro:' . $organization->intro());
        logging::d('org_create', 'own_id:' . $organization->owner());
        logging::d('org_create', 'org_avatar:' . $organization->avatar());
        
        $organization->save();
        $data = $organization->packInfo();

        return array('op' => 'org_edit', "data" =>  $data);
    }

    public function remove_action() {
    }

    public function invite_action() {
    }
    
    public function join_action() {
        $org_id = get_request('org_id');
        $yuyue_session = get_request("yuyue_session");
        
        $organization = Organization::oneById($org_id);
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($organization)) {
            return array("op" => "fail" , "code" => "222" , "reason" => "未找到此组织");
        }
        if (!$user) {
            return array('op' => 'fail', "code" => '333', "reason" => '无此用户');
        }
        if ($organization->type() == 1) {
            return array('op' => 'fail', "code" => '2323', "reason" => '此组织已经被解散');
        }
        if($yuyue_session == $organization->owner_yuyue_session()) {
            return array('op' => 'fail', "code" => '444', "reason" => '申请加入的user是组织创建者');
        }
        $userid = $user->id();
        if(db_organization_member::inst()->one($org_id, $userid)) {
            return array('op' => 'fail', "code" => '4443', "reason" => '申请的用户已经是此组织会员');
        }
        
        $ret = Organization::receive_join($org_id, $userid);
        return $ret ? array("op" => "org_join" , "data" => $ret) : array('op' => 'fail', "code" => '666', "reason" => '申请失败');
    }
    
    public function disband_action() {
        $org_id = get_request('org_id');
        $yuyue_session = get_request("yuyue_session");
        
        $organization = Organization::oneById($org_id);
        $user = TempUser::oneBySession($yuyue_session);
        //var_dump($organization);
        if (empty($organization)) {
            return array("op" => "fail" , "code" => "222" , "reason" => "未找到此组织");
        }
        if (!$user) {
            return array('op' => 'fail', "code" => '333', "reason" => '无此用户');
        }
        if($yuyue_session != $organization->owner_yuyue_session()) {
            return array('op' => 'fail', "code" => '444', "reason" => 'user并非是组织创建者，无权解散组织');
        }
        if ($organization->type() == 1) {
            return array('op' => 'fail', "code" => '555', "reason" => '此组织已经被解散');
        }
        $userid = $user->id();

        $ret = Organization::disband($org_id);
        return $ret ? array("op" => "org_disband" , "data" => $ret) : array('op' => 'fail', "code" => '666', "reason" => '组织解散失败');
    }

    public function audit_action() {
        $org_id = get_request('org_id');
        $yuyue_session = get_request("yuyue_session");
        $audit = get_request("audit");
        
        $organization = Organization::oneById($org_id);
        $user = TempUser::oneBySession($yuyue_session);
        if (empty($organization)) {
            return array("op" => "fail" , "code" => "222" , "reason" => "未找到此组织");
        }
        if ($organization->type() == 1) {
            return array('op' => 'fail', "code" => '555', "reason" => '此组织已经被解散');
        }
        if (!$user) {
            return array('op' => 'fail', "code" => '333', "reason" => '无此用户');
        }
        if($yuyue_session == $organization->owner_yuyue_session()) {
            return array('op' => 'fail', "code" => '444', "reason" => '申请加入的user是组织创建者');
        }
        $userid = $user->id();
        
        $ret = Organization::audit_join($org_id, $userid, $audit);
        return $ret ? array("op" => "org_audit" , "data" => $ret) : array('op' => 'fail', "code" => '555', "reason" => '失败操作');
        
    }

    public function addvip_action() {
        
        
        
        
        
        
        
    }

    public function member_list_action() {
        $org_id = get_request('org_id');
        $organization = Organization::oneById($org_id);
        if (empty($organization)) {
            return array("op" => "fail" , "code" => "222" , "reason" => "未找到此组织");
        }
        if ($organization->type() == 1) {
            return array('op' => 'fail', "code" => '555', "reason" => '此组织已经被解散');
        }
        $member_list = Organization::member_list($org_id);
        
        return array("op" => "member_list" , "data" => $member_list);
        
    }

    public function kick_member_action() {
        $org_id = get_request('org_id');
        $yuyue_session = get_request("yuyue_session");
        
        $organization = Organization::oneById($org_id);
        $user = TempUser::oneBySession($yuyue_session);
        $member_list = Organization::member_list($org_id);
        if (empty($organization)) {
            return array("op" => "fail" , "code" => "222" , "reason" => "未找到此组织");
        }
        if ($organization->type() == 1) {
            return array('op' => 'fail', "code" => '555', "reason" => '此组织已经被解散');
        }
        if (!$user) {
            return array('op' => 'fail', "code" => '333', "reason" => '无此用户');
        }
        $userid = $user->id();
        if($yuyue_session == $organization->owner_yuyue_session()) {
            return array('op' => 'fail', "code" => '444', "reason" => '踢出的user是组织创建者');
        }
        if (empty($member_list)) {
            return array('op' => 'fail', "code" => '555', "reason" => '此组织内没有会员');
        }
        foreach ($member_list as $member){
            if ($member['id'] == $userid) {
                $ret = db_organization_member::inst()->kick($org_id, $userid);
                return $ret ? array('op' => 'kick_member', "data" => '') : array('op' => 'fail', "code" => '777', "reason" => '删除成员失败');
            }
        }
        return array('op' => 'fail', "code" => '666', "reason" => '此用户非此组织成员');
        
    }

    public function activities_action() {
    }

    public function search_action() {
        $org_id = get_request('org_id');
        $organization = Organization::oneById($org_id);
        if (empty($organization)) {
            return array("op" => "fail" , "code" => "222" , "reason" => "未找到此组织");
        }
        if ($organization->type() == 1) {
            return array('op' => 'fail', "code" => '555', "reason" => '此组织已经被解散');
        }
        return array("op" => "org_search" , "data" => $organization->packInfo());
    }

    public function receive_list_action() {
        $org_id = get_request('org_id');
        $organization = Organization::oneById($org_id);
        if (empty($organization)) {
            return array("op" => "fail" , "code" => "444" , "reason" => "未找到此组织");
        }
        if ($organization->type() == 1) {
            return array('op' => 'fail', "code" => '555', "reason" => '此组织已经被解散');
        }
        
        $all_invite = db_invite::inst()->all();
        $all_tempuser = db_tempuser::inst()->all();
        
        $ret = array();
        foreach ($all_invite as $invite) {
            $invite_id = $invite["id"];
            $invite_userid = $invite["user"];
            $invite_orgid = $invite["organization"];
            if ($invite_orgid == $org_id) {
                $join_user = new TempUser($all_tempuser[$invite_userid]);
                $ret[$invite_id] = $invite;
                $ret[$invite_id]["user"] = $join_user->packInfo();
            }
        }
        return array("op" => "receive_list" , "data" => $ret);
    }


}













