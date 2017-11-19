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
  
    $tempuser = TempUser::oneBySession($yuyue_session);//��ȡ�û���Ϣ
			if(empty($nationCode)||empty($phoneNumber)||empty($yuyue_session))
			return array("data" =>array("status"=>0,"reason"=>"��Ϣ��ȫ") ,"op" =>"verify" );
		$templId = 50285;
		$tempuser = TempUser::oneBySession($yuyue_session);//��ȡ�û���Ϣ
		if (empty($tempuser)) {//���û�ж�Ӧ��user���ʹ���һ����
			$tempuser = new TempUser();
		}
		$user = InternalUser::oneByTelephone($phoneNumber);//ͨ���ֻ��� ��ȡ��Ӧ���ڲ��û�
		if (empty($user)) {//���û�ж�Ӧ��user���ʹ���һ����
			$user = new InternalUser();
		}

	logging::d("Id", "Id is:" .$user->id());
	logging::d("verify_status", "verify_status is:" .$user->verify_status());


		logging::d("tempuser", "Id is:" .$tempuser->id());
		$tempId = $tempuser->id();//��ȡ��Ӧtempid
		$type = 0;
		if($user->id()==0){//δע��
				
			$user->setTempId($tempId);
			logging::d("tempId", "tempId is:" .$tempId);
			$user->setTelephone($phoneNumber);
			logging::d("sendsms", "nationCode is:" .$nationCode);
			logging::d("sendsms", "phoneNumber is:" .$phoneNumber);
	
		}
		else if($user->id()!=0){//��ע��
			if($user->verify_status()=="true"){//��ע��ɹ�����½
	
				if($tempId==$user->tempid()){//��Ӧ΢�ŵ�½ ��������
					$type = 1;
				}else{//���Ƕ�Ӧ΢�� ��ȡsession
					$tempuser = TempUser::oneById($user->tempid());//��ȡ��Ӧ�û���Ϣ
					$yuyue_session =$tempuser->yuyue_session();//��ȡyuyue_session
					$type = 2;
				}
			}else{//δע��ɹ�
				if($tempId==$user->tempid()){//��Ӧ΢��ע�� ��������
				
				}else{//��������
					$user->setTempId($tempId);
			logging::d("tempId", "tempId is:" .$tempId);
				}
		
			}
			
		}	
			
			$verification_code = rand(1000,9999);//�����֤��
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
		$tempuser = TempUser::oneBySession($yuyue_session);//��ȡ�û���Ϣ
		if(empty($nationCode)||empty($phoneNumber)||empty($yuyue_session)||empty($verify_code)){
			logging::d("yuyue_session", "111111 is:"  );
			$data = array("status"=>0,"reason"=>"��Ϣ��ȫ");
		return array("data" => $data,"op" =>"verify" );
		}
		  //��ȡ�û���Ϣ
		
		  
		if (empty($tempuser)) {//���û�ж�Ӧ��user��ϵͳ����
		logging::d("yuyue_session", "1222222 is:"  );
		$data =array("status"=>0,"reason"=>"ϵͳ����������С����");
			return array("data" =>$data ,"op" =>"verify" );
		}
		$user = InternalUser::oneByTelephone($phoneNumber);//ͨ���ֻ��� ��ȡ��Ӧ���ڲ��û�
		if (empty($user)) {//���û�ж�Ӧ��user��ϵͳ����
			logging::d("yuyue_session", "33333 is:"  );
			$data =array("status"=>0,"reason"=>"��֤����������»�ȡ");
			return array("data" =>$data  ,"op" =>"verify" );
		}
		  if(!$user->verify($verify_code)){
			  logging::d("yuyue_session", "44444 is:"  );
			$data =  array("status"=>0,"reason"=>"��֤�����");
			  	return array("data" => $data,"op" =>"verify" );
		  }
		  

	logging::d("Id", "Id is:" .$user->id());

	logging::d("verify_code", "send verify_code is:" .$verify_code);
	logging::d("verify_status", "verify_status is:" .$user->verify_status());
	logging::d("tempuser", "Id is:" .$tempuser->id());
		
		$tempId = $tempuser->id();//��ȡ��Ӧtempid
		$type = 0;
		if($user->tempid()== $tempId){//������
			
			if($user->verify_status()=="true"){//��ע��ɹ�����½
			
				$type = 2;
			}else {//δע��,ע��
				$user->setStatus("true");
				$type = 1;
			}
			
		}else {//���Ƕ�Ӧ΢�ţ�
			$tempuser = TempUser::oneById($user->tempid());//��ȡ��Ӧ�û���Ϣ
			if (empty($tempuser)) {//���û�ж�Ӧ��user��ϵͳ����
				$data = array("status"=>0,"reason"=>"ϵͳ�����˺���Ч������ϵ����Ա");
				return array("data" => $data ,"op" =>"verify" );
			}
			
			$yuyue_session =$tempuser->yuyue_session();//��ȡyuyue_session
			$user->setStatus("true");
			$type = 3;
		}
			

		$data = array("status"=>$type,"info"=>array( "id"=>$user->id,"yuyue_session"=>$yuyue_session));
		return array("data" =>$data ,"op" =>"verify" );
   }
}