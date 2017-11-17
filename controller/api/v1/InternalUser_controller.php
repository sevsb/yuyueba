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
  
		$templId = 50285;
		$tempuser = TempUser::oneBySession($yuyue_session);//获取用户信息
		$user = InternalUser::oneByTelephone($phoneNumber);//通过手机号 获取对应的内部用户
		if (empty($user)) {//如果没有对应的user，就创建一个。
			$user = new InternalUser();
		}

logging::d("Id", "Id is:" .$user->id());
logging::d("verify_code", "verify_code is:" .$user->verify_code());
logging::d("verify_status", "verify_status is:" .$user->verify_status());


		logging::d("tempuser", "Id is:" .$tempuser->id());
		$tempId = $tempuser->id();//获取对应tempid
		$type = 0;
		if($user->id()==0){//未注册
				
			$user->setTempId($tempId);
			logging::d("tempId", "tempId is:" .$tempId);
			$user->setTelephone($phoneNumber);
			logging::d("sendsms", "nationCode is:" .$nationCode);
			logging::d("sendsms", "phoneNumber is:" .$phoneNumber);
	
		}
		else if($user->id()!=0){//已注册
			if($user->verify_status()=="true"){//已注册成功，登陆
	
				if($tempId==$user->tempid()){//对应微信登陆 不做处理
					$type = 1;
				}else{//不是对应微信 获取session
					$tempuser = TempUser::oneById($user->tempid());//获取对应用户信息
					$yuyue_session =$tempuser->tempuser();//获取yuyue_session
					$type = 2;
				}
			}else{//未注册成功
				if($tempId==$user->tempid()){//对应微信注册 不做处理
				
				}else{//绑定有问题
					$user->setTempId($tempId);
			logging::d("tempId", "tempId is:" .$tempId);
				}
		
			}
			
		}	
			
			$verification_code = rand(1000,9999);//随机验证码
			$user->setCode($verification_code);
			logging::d("sendsms", "verification_code is:" .$verification_code);
			
			$params =array("".$verification_code);

			$sender = new SmsSingleSender( WX_SMS_SDKID,WX_SMS_SECRET);
			$result = $sender->sendWithParam($nationCode, $phoneNumber, $templId, $params);
			$result=json_decode($result);

			if($result->result == 0){
				$id = $user->save();
		//		logging::d("id", "id is:" .$id);
		
			}
			$data = array("type"=>$type,"info"=>array( "id"=>$id,"yuyue_session"=>$yuyue_session),"result" =>$result);
		return array("data" =>$data ,"op" =>"send" );
   }

   public function verify_action() {
 
	   $nationCode = get_request('nationCode');
        $phoneNumber = get_request('phoneNumber');
        $yuyue_session = get_request('yuyue_session');
		  $verify_code = get_request('verify_code');
		


		$user = InternalUser::oneByTelephone($phoneNumber);//通过手机号 获取对应的内部用户
		if (empty($user)) {//如果没有对应的user，就创建一个。
			$user = new InternalUser();
		}
	
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