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
		$user = InternalUser::oneByTelephone($phoneNumber);//通过手机号 获取对应的内部用户
		$type = 0;
		$reason="系统错误";
		$data = array();
		if(empty($nationCode)||empty($phoneNumber)||empty($yuyue_session)||empty($verify_code)){
			logging::d("yuyue_session", "111111 is:"  );
			$reason ="信息不全";
			$type = 0;
		
		}else if(empty($tempuser)) {//如果没有对应的user，系统错误。
			logging::d("yuyue_session", "1222222 is:"  );
			$reason ="系统错误，请重启小程序";
			$type = 0;
		}else if (empty($user)) {//如果没有对应的user，系统错误。
			logging::d("yuyue_session", "33333 is:"  );
			$reason ="手机错误，请重新输入";
			$type = 0;
			
		}else{ 
			if(!$user->verify($verify_code)){
				logging::d("yuyue_session", "44444 is:"  );
				$reason ="验证码错误";
				$type = 0;
			
			}else{
			
				$tempId = $tempuser->id();//获取对应tempid			
				if($user->tempid()== $tempId){//单方绑定无误
			
					if($user->verify_status()=="true"){//已注册成功，登陆
			
						$type = 2;
					}else if($tempuser->id()==0){//未注册,注册
						$tempuser->setUId($user->id());
						$user->setStatus("true");
						$user->setCode("00000");
						$type = 1;
					}else{//一个微信注册过，又用另一个手机号注册
						$reason ="无此用户";
						$type = 0;
					}
			
				}else {//不是对应微信，
					if($user->verify_status()=="true"){//已注册成功，登陆
						$tempuser = TempUser::oneById($user->tempid());//获取对应用户信息
						if (empty($tempuser)) {//如果没有对应的user，系统错误。
							$reason ="系统错误，账号无效，请联系管理员";
							$type = 0;
					
						}else{
							
						$yuyue_session =$tempuser->yuyue_session();//获取yuyue_session
						$user->setStatus("true");
						$user->setCode("00000");
						$type = 3;
						}
					}else if($tempuser->id()==0){//不应有这种情况
						$tempuser->setUId($user->id());
						$user->setTempId($tempuser->id());
						$user->setStatus("true");
						$user->setCode("00000");
						$type = 1;
					}else{//账号绑定错误
						$reason ="账号错误";
						$type = 0;
					}
				}
			}
		}	
		if($type!=0){
			$id = $user->save();
			$tempuser->save();
			$data = array("status" => $type , "info"=>array( "id" => $id , "yuyue_session" => $yuyue_session));
		}else{
			$data = array("status" => $type , "reason" => $reason);
		}
		return array("data" => $data , "op" => "verify" );
   }
   
    public function getInfo_action() {
		logging::d("yuyue_session", "45678 is:"  );
		$yuyue_session = get_request('yuyue_session');		
		$uid = get_request('uid');
		$user = InternalUser::oneById($uid);
		$tempuser = TempUser::oneBySession($yuyue_session);//获取用户信息
		$data= new stdclass();
		logging::d("yuyue_session", "14879789611 is:"  );
		/*
		if(empty($yuyue_session)){
			
			logging::d("yuyue_session", "111111 is:"  );
			$data->reason ="信息不全";
			$data->status = 0;
		
		}else if(empty($tempuser)) {//如果没有对应的user，系统错误。
			logging::d("yuyue_session", "1222222 is:"  );
			$data->reason ="系统错误，请重启小程序";
			$data->status = 0;
		}else if (empty($user)) {//如果没有对应的user，系统错误。
			logging::d("yuyue_session", "33333 is:"  );
			$data->reason ="无此用户";
			$data->status = 0;
			
		}else if($tempuser->uid() == $user->id()&&$user->tempId()==$tempuser->id()){
			logging::d("yuyue_session", "145611 is:"  );
			$data->avatar = tempuser->avatar();
			$data->phoneNumber =  $user->telephone();
			$data->status = 1;
		}else{
			logging::d("yuyue_session", "1789789781 is:"  );
			$data->error ="未知错误";
		}
		*/
		return array( "op" => "getInfo","data" => $data  );
	}
    public function login_action() {
	$yuyue_session = get_request('yuyue_session');		
		$uid = get_request('uid');
		$user = InternalUser::oneById($uid);
		$tempuser = TempUser::oneBySession($yuyue_session);//获取用户信息
		$data= new stdclass();
		if(empty($yuyue_session)){
			logging::d("yuyue_session", "111111 is:"  );
			$data->reason ="信息不全";
			$data->status = 0;
		
		}else if(empty($tempuser)) {//如果没有对应的user，系统错误。
			logging::d("yuyue_session", "1222222 is:"  );
			$data->reason ="系统错误，请重启小程序";
			$data->status = 0;
		}else if (empty($user)) {//如果没有对应的user，系统错误。
			logging::d("yuyue_session", "33333 is:"  );
			$data->reason ="无此用户";
			$data->status = 0;
			
		}else if($tempuser->uid() == $user->id()&&$user->tempId()==$tempuser->id()){
			$tempuser->setSessionKey =  md5(time() . $tempuser->yuyue_session());
			$tempuser->save();
			$data->phoneNumber =  $user->telephone();
			$data->yuyue_session = $tempuser->yuyue_session();
			$data->status = 1;
		}
		
		return array( "op" => "login","data" => $data);
   }
}