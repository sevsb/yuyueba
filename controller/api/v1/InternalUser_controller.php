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
		$user = InternalUser::oneByTelephone($phoneNumber);//ͨ���ֻ��� ��ȡ��Ӧ���ڲ��û�
		$type = 0;
		$reason="ϵͳ����";
		$data = array();
		if(empty($nationCode)||empty($phoneNumber)||empty($yuyue_session)||empty($verify_code)){
			logging::d("yuyue_session", "111111 is:"  );
			$reason ="��Ϣ��ȫ";
			$type = 0;
		
		}else if(empty($tempuser)) {//���û�ж�Ӧ��user��ϵͳ����
			logging::d("yuyue_session", "1222222 is:"  );
			$reason ="ϵͳ����������С����";
			$type = 0;
		}else if (empty($user)) {//���û�ж�Ӧ��user��ϵͳ����
			logging::d("yuyue_session", "33333 is:"  );
			$reason ="��֤����������»�ȡ";
			$type = 0;
			
		}else{ 
			if(!$user->verify($verify_code)){
				logging::d("yuyue_session", "44444 is:"  );
				$reason ="��֤�����";
				$type = 0;
			
			}else{
			
				$tempId = $tempuser->id();//��ȡ��Ӧtempid			
				if($user->tempid()== $tempId){//������
			
					if($user->verify_status()=="true"){//��ע��ɹ�����½
			
						$type = 2;
					}else {//δע��,ע��
						$tempuser->setUId($user->id());
						$user->setStatus("true");
						$user->setCode("00000");
						$type = 1;
					}
			
				}else {//���Ƕ�Ӧ΢�ţ�
					if($user->verify_status()=="true"){//��ע��ɹ�����½
						$tempuser = TempUser::oneById($user->tempid());//��ȡ��Ӧ�û���Ϣ
						if (empty($tempuser)) {//���û�ж�Ӧ��user��ϵͳ����
							$reason ="ϵͳ�����˺���Ч������ϵ����Ա";
							$type = 0;
					
						}else{
							
						$yuyue_session =$tempuser->yuyue_session();//��ȡyuyue_session
						$user->setStatus("true");
						$user->setCode("00000");
						$type = 3;
						}
					}else{//��Ӧ���������
						$tempuser->setUId($user->id());
						$user->setTempId($tempuser->id());
						$user->setStatus("true");
						$user->setCode("00000");
						$type = 1;
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
}