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
  
    $tempuser = TempUser::oneBySession($yuyue_session);//获取用户信息
			if(empty($nationCode)||empty($phoneNumber)||empty($yuyue_session))
			return array("data" =>array("status"=>0,"reason"=>"信息不全") ,"op" =>"verify" );
		$templId = 50285;
		$tempuser = TempUser::oneBySession($yuyue_session);//获取用户信息
		if (empty($tempuser)) {//如果没有对应的user，就创建一个。
			$tempuser = new TempUser();
		}
		$user = InternalUser::oneByTelephone($phoneNumber);//通过手机号 获取对应的内部用户
		if (empty($user)) {//如果没有对应的user，就创建一个。
			$user = new InternalUser();
		}

	logging::d("Id", "Id is:" .$user->id());
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
					$yuyue_session =$tempuser->yuyue_session();//获取yuyue_session
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
		logging::d("yuyue_session", "yuyue_session is:" .$yuyue_session );
		$verify_code = get_request('verify_code');
		$tempuser = TempUser::oneBySession($yuyue_session);//获取用户信息
		if(empty($nationCode)||empty($phoneNumber)||empty($yuyue_session)||empty($verify_code)){
			logging::d("yuyue_session", "111111 is:"  );
			$data = array("status"=>0,"reason"=>"信息不全");
		return array("data" => $data,"op" =>"verify" );
		}
		  //获取用户信息
		
		  
		if (empty($tempuser)) {//如果没有对应的user，系统错误。
		logging::d("yuyue_session", "1222222 is:"  );
		$data =array("status"=>0,"reason"=>"系统错误，请重启小程序");
			return array("data" =>$data ,"op" =>"verify" );
		}
		$user = InternalUser::oneByTelephone($phoneNumber);//通过手机号 获取对应的内部用户
		if (empty($user)) {//如果没有对应的user，系统错误。
			logging::d("yuyue_session", "33333 is:"  );
			$data =array("status"=>0,"reason"=>"验证码错误，请重新获取");
			return array("data" =>$data  ,"op" =>"verify" );
		}
		  if(!$user->verify($verify_code)){
			  logging::d("yuyue_session", "44444 is:"  );
			$data =  array("status"=>0,"reason"=>"验证码错误");
			  	return array("data" => $data,"op" =>"verify" );
		  }
		  

	logging::d("Id", "Id is:" .$user->id());

	logging::d("verify_code", "send verify_code is:" .$verify_code);
	logging::d("verify_status", "verify_status is:" .$user->verify_status());
	logging::d("tempuser", "Id is:" .$tempuser->id());
		
		$tempId = $tempuser->id();//获取对应tempid
		$type = 0;
		if($user->tempid()== $tempId){//绑定无误
			
			if($user->verify_status()=="true"){//已注册成功，登陆
			
				$type = 2;
			}else {//未注册,注册
				$user->setStatus("true");
				$type = 1;
			}
			
		}else {//不是对应微信，
			$tempuser = TempUser::oneById($user->tempid());//获取对应用户信息
			if (empty($tempuser)) {//如果没有对应的user，系统错误。
				$data = array("status"=>0,"reason"=>"系统错误，账号无效，请联系管理员");
				return array("data" => $data ,"op" =>"verify" );
			}
			
			$yuyue_session =$tempuser->yuyue_session();//获取yuyue_session
			$user->setStatus("true");
			$type = 3;
		}
			

		$data = array("status"=>$type,"info"=>array( "id"=>$user->id,"yuyue_session"=>$yuyue_session));
		return array("data" =>$data ,"op" =>"verify" );
   }
}