<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");
include_once(dirname(__FILE__) . "/../../../app/SmsSingleSender.class.php");
include_once(dirname(__FILE__) . "/../../../app/InternalUser.class.php");
class InternalUser_controller extends v1_base {

public function send_action(){
 
	   $nationCode = get_request('nationCode');
        $phoneNumber = get_request('phoneNumber');
        $yuyue_session = get_request('yuyue_session');
        $token = get_request('token');
		$tempuser = TempUser::oneBySession($yuyue_session);//获取用户信息
		$user = new InternalUser();

		$templId = $tempuser->id();//获取对应tempid
		$user->setTempid($templId);
		logging::d("templId", "templId is:" .$templId);
		$user->setTelephone($telephone);
		logging::d("sendsms", "nationCode is:" .$nationCode);
		logging::d("sendsms", "phoneNumber is:" .$phoneNumber);
		
		$verification_code = rand(1000,9999);//随机验证码
		$user->setCode($verification_code);
		logging::d("sendsms", "verification_code is:" .$verification_code);
		
		
		
		
		$params =array("".$verification_code);
		
		$sender = new SmsSingleSender( WX_SMS_SDKID,WX_SMS_SECRET);

		$data = $sender->sendWithParam($nationCode, $phoneNumber, $templId, $params);
		$data=json_decode($data);
		if(data.result == 0){
		$id = $user->save();
		logging::d("id", "id is:" .$id);
		
		}
		return array("data" =>  $data);
   }

   public function verify_action() {
	   /**
	    $from = get_request('from');
        logging::d("LOGIN", "FROM:" . $from);
        if ($from == 'weapp') { //具体的来源，现在只有微信小程序，也就是WXAPP

		
            $yuyue_session = get_request('yuyue_session', "");  //yuyue_session用作传递的userid
            $avatar = get_request('avatar', "");  //
            $nick = get_request('nick', "");  //
            logging::d("LOGIN", "nick:" . $nick);
            logging::d("LOGIN", "avatar:" . $avatar);
            $user = InternalUser::oneBySession($yuyue_session); //拿到具体的tempuser信息,tempuser是wx小程序的user,
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

                $user = InternalUser::createByOpenid($openid);  //创建TempUser,修改属性，保存
                $user->setOpenId($openid);
                $user->setSessionKey($session_key);
                $user->setToken($token);
                $user->setYuyueSession($yuyue_session);
                logging::d("LOGIN", "yuyue_session now is :" . $yuyue_session);
            }
            $user->setAvatar($avatar);
            $user->setNickname($nick);
            $user->save();
            //logging::d("LOGIN", "now user is :" . $user);
            $data = new stdClass();
            $data->timeout = time() + 7200;
            $data->token = $user->token();
            $data->yuyue_session = $user->yuyue_session();
            //logging::d("LOGIN", "now data is :" . $data);
            return array("op" => "login", 'data' => $data);
        }*/
	   
	   
	   
	   $nationCode = get_request('nationCode');
        $phoneNumber = get_request('phoneNumber');
        $yuyue_session = get_request('yuyue_session');
        $token = get_request('token');
		$templId = 50285;

		$user = new InternalUser();
		$user->setTelephone($phoneNumber);
		$user->setYuyueSession(yuyue_session);
		logging::d("sendsms", "nationCode is:" .$nationCode);
		logging::d("sendsms", "phoneNumber is:" .$phoneNumber);
		
		$verification_code = rand(1000,9999);
		logging::d("sendsms", "verification_code is:" .$verification_code);
	$params =array("".$verification_code);
		
		$sender = new SmsSingleSender( WX_SMS_SDKID,WX_SMS_SECRET);

		$data = $sender->sendWithParam($nationCode, $phoneNumber, $templId, $params);
		$data=json_decode($data);
		return array("data" =>  $data);
   }
}