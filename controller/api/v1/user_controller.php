<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");

class user_controller extends v1_base {
    public function preaction($action) {
    }

    public function login_action() {
        $from = get_request('from');
        
        if ($from == 'weapp') {
            $yuyue_session = get_request('yuyue_session', "");  //yuyue_session用作传递的userid
            $user = TempUser::oneBySession($yuyue_session);
            if (empty($user)) {
                $code = get_request('code', '');
                $wx_auth_ret = WxApi::wx_auth($code);
                if (!empty($wx_auth_ret->errcode)){
                    return array('op' => 'fail', 'code' => $wx_auth_ret->errcode, 'reason' => $wx_auth_ret->errmsg);
                }
                $openid = $wx_auth_ret->openid;
                $session_key = $wx_auth_ret->session_key;
                $yuyue_session = md5(time() . $openid . $session_key);
                $token = md5(time());
                
                $user = new TempUser();
                $user->setOpenId($openid);
                $user->setSessionKey($session_key);
                $user->setToken($token);
                $user->setYuyueSession($yuyue_session);
                
                $user->save();
            }
            
            $data = new stdClass();
            $data->timeout = time() + 7200;
            $data->token = $user->token();
            $data->yuyue_session = $user->yuyue_session();

            return array("op" => "login", 'data' => $data);
        }
    }

    public function refreshtoken_action() {
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

    public function organizations_action() {
    }


}





