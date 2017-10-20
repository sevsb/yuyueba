<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");

class user_controller extends v1_base {
    public function preaction($action) {
    }

    public function login_action() {
        $from = get_request('from');
        logging::d("LOGIN", "FROM:" . $from);
        if ($from == 'weapp') { //具体的来源，现在只有微信小程序，也就是WXAPP
            $yuyue_session = get_request('yuyue_session', "");  //yuyue_session用作传递的userid
            $user = TempUser::oneBySession($yuyue_session); //拿到具体的tempuser信息,tempuser是wx小程序的user,
            if (empty($user)) {                             //如果没有对应的user，就创建一个。    
                $code = get_request('code', '');
                $wx_auth_ret = Wxapi::wx_auth($code);   //获取openid
                if (!empty($wx_auth_ret->errcode)){
                    return array('op' => 'fail', 'code' => $wx_auth_ret->errcode, 'reason' => $wx_auth_ret->errmsg);
                }
                $openid = $wx_auth_ret->openid;
                $session_key = $wx_auth_ret->session_key;
                $yuyue_session = md5(time() . $openid . $session_key);
                $token = md5(time());
                
                //$user = new TempUser();
                $user = TempUser::createByOpenid($openid);  //创建TempUser,修改属性，保存
                
                $user->setOpenId($openid);
                $user->setSessionKey($session_key);
                $user->setToken($token);
                $user->setYuyueSession($yuyue_session);
                logging::d("LOGIN", "yuyue_session now is :" . $yuyue_session);
                $user->save();
            }
            //logging::d("LOGIN", "now user is :" . $user);
            $data = new stdClass();
            $data->timeout = time() + 7200;
            $data->token = $user->token();
            $data->yuyue_session = $user->yuyue_session();
            //logging::d("LOGIN", "now data is :" . $data);
            return array("op" => "login", 'data' => $data);
        }
    }

    public function refreshtoken_action() { //刷新token
        $yuyue_session = get_request('yuyue_session', "");
        $user = TempUser::oneBySession($yuyue_session);
        
        $token = md5(time());
        $user->setToken($token);
        $user->save();
        
        $data = new stdClass();
        $data->timeout = time() + 7200;
        $data->token = $user->token();
        
        return array("op" => "refreshtoken", 'data' => $data);
    }

    public function bind_action() {
    }

    public function register_action() {
    }

    public function organizations_action() {    //返回用户相关的org信息，一个是创建的，一个是参加的
        $yuyue_session = get_request('yuyue_session', "");
        
        $user = TempUser::oneBySession($yuyue_session);
        $userid = $user->id();
        
        $own_organizations = array();
        
        $all_orgs = Organization::all();
        foreach ($all_orgs as $org) {
            if ($org->owner() == $userid) {
                $own_organizations[$org->id()] = $org->packInfo();
            }
        }

        $data = array("own_orgs" =>$own_organizations, 'join_orgs' => array());
        return array("op" => "organizations", 'data' => $data);
        
    }

    public function join_list_action(){
        $yuyue_session = get_request('yuyue_session', "");
        $user = TempUser::oneBySession($yuyue_session);
        if (!$user) {
            return array('op' => 'fail', "code" => '232323', "reason" => '无此用户');
        }
        $userid = $user->id();
        
        $all_invite = db_invite::inst()->all();
        $all_organization = db_organization::inst()->all();
        
        $ret = array();
        foreach ($all_invite as $invite) {
            $org_id = $invite["organization"];
            if ($invite['user'] == $userid) {
                $invite['organization'] = Organization::oneById($org_id)->packInfo();
                $ret[$invite['id']] = $invite;
            }
        }
        //var_dump($ret);
        return array("op" => "join_list", 'data' => $ret);
    }

}





