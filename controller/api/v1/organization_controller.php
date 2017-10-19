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

        return array('op' => 'org_create', "data" =>  $data);
    }

    public function remove_action() {
    }

    public function invite_action() {
    }

    public function accept_action() {
    }

    public function addvip_action() {
    }

    public function activities_action() {
    }



}













