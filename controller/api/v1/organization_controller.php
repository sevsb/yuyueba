<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");

class organization_controller extends v1_base {
    public function preaction($action) {
    }

    public function create_action() {
        $org_name = get_request('org_name');
        $org_intro = get_request('org_intro');
        $yuyue_session = get_request('yuyue_session');
        $token = get_request('token');
        
        
        $org_avatar = Upload::upload_image();
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













