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
		$status = 10;
		$id = -1 ;
		$reason="ϵͳ����";
		$data = array("123"=>12316);
		$dataaaa = array("123"=>12316);
		if(empty($nationCode)||empty($phoneNumber)||empty($yuyue_session)||empty($verify_code)){
			logging::d("yuyue_session", "111111 is:"  );
			$reason ="��Ϣ��ȫ";
			$status = 0;
		
		}else if(empty($tempuser)) {//���û�ж�Ӧ��user��ϵͳ����
			logging::d("yuyue_session", "1222222 is:"  );
			$reason ="ϵͳ����������С����";
			$status = 0;
		}else if (empty($user)) {//���û�ж�Ӧ��user��ϵͳ����
			logging::d("yuyue_session", "33333 is:"  );
			$reason ="�ֻ���������������";
			$status = 0;
			
		}else{ 
			if(!$user->verify($verify_code)){
				logging::d("yuyue_session", "44444 is:"  );
				$reason ="��֤�����";
				$status = 0;
			
			}else{
			
				$tempId = $tempuser->id();//��ȡ��Ӧtempid			
				if($user->tempid()== $tempId){//����������
			
					if($user->verify_status()=="true"){//��ע��ɹ�����½
			logging::d("yuyue_session", "status 1 $status " . $status);
						$status = 2;
					}else if($tempuser->id()==0){//δע��,ע��
					logging::d("yuyue_session", "status 2 $status " . $status);
						$tempuser->setUId($user->id());
						$user->setStatus("true");
						$user->setCode("00000");
						$status = 1;
					}else{//һ��΢��ע�����������һ���ֻ���ע��
					
						$reason ="�޴��û�";
						$status = 0;
						logging::d("yuyue_session", "status 3 $status " . $status);
					}
			
				}else {//���Ƕ�Ӧ΢�ţ�
				
					if($user->verify_status()=="true"){//��ע��ɹ�����½
						$tempuser = TempUser::oneById($user->tempid());//��ȡ��Ӧ�û���Ϣ
						if (empty($tempuser)) {//���û�ж�Ӧ��user��ϵͳ����
							$reason ="ϵͳ�����˺���Ч������ϵ����Ա";
							$status = 0;
					
						}else{
							
						$yuyue_session =$tempuser->yuyue_session();//��ȡyuyue_session
						$user->setStatus("true");
						$user->setCode("00000");
						$status = 3;
						}
					}else if($tempuser->id()==0){//��Ӧ���������
						$tempuser->setUId($user->id());
						$user->setTempId($tempuser->id());
						$user->setStatus("true");
						$user->setCode("00000");
						$status = 1;
					}else{//�˺Ű󶨴���
						$reason ="�˺Ŵ���";
						$status = 0;
					}
					logging::d("yuyue_session", "status 4 $status " . $status);
				}
			}
		}	
		if($status!=0){
			logging::d("verify_action", "$status!=0  " .$status );
			$id = $user->save();
			$tempuser->save();
			$data = array("status" => $status , "info"=>array( "id" => $id , "yuyue_session" => $yuyue_session));
		}else{
			logging::d("verify_action", "$status==0  " .$status );
			$data = array("status" => $status , "reason" => $reason);
			
		}
		logging::d("verify_action", " status  " .$status." reason " .$reason." id " .$id);

		return array("data" => $data , "op" => "verify" );
   }
   
    public function getInfo_action() {

		$yuyue_session = get_request('yuyue_session');		
		$uid = get_request('uid');
		$user = InternalUser::oneById($uid);
		$tempuser = TempUser::oneBySession($yuyue_session);//��ȡ�û���Ϣ
		
	
		$reason =".0.0.";
		$status = 0;
		$avatar = "";
		$phoneNumber = "";
		$data = array("1"=>123);
		if(empty($yuyue_session)){
			
			logging::d("yuyue_session", "111111 is:"  );
			$reason ="��Ϣ��ȫ";
			$status = 0;
		
		}else if(empty($tempuser)) {//���û�ж�Ӧ��user��ϵͳ����
			logging::d("yuyue_session", "1222222 is:"  );
			$reason ="ϵͳ����������С����";
			$status = 0;
		}else if (empty($user)) {//���û�ж�Ӧ��user��ϵͳ����
			logging::d("yuyue_session", "33333 is:"  );
			$reason ="�޴��û�";
			$status = 0;
			
		}else if($tempuser->uid() == $user->id()&&$user->tempId()==$tempuser->id()){
			logging::d("yuyue_session", "145611 is:"  );
			$avatar = $tempuser->avatar();
			$phoneNumber =  $user->telephone();
			$status = 1;
		}else{
			logging::d("yuyue_session", "1789789781 is:"  );
			$reason ="δ֪����";
		}
		if($status==1)
		$data= array("phoneNumber"=>$phoneNumber,"avatar"=>$avatar,"status"=>$status);
		else{
		$data= array("reason"=>$reason,"status"=>$status);
		}
		return array( "op" => "getInfo","data" => $data  );
	}
    public function login_action() {
	$yuyue_session = get_request('yuyue_session');		
		$uid = get_request('uid');
		$user = InternalUser::oneById($uid);
		$tempuser = TempUser::oneBySession($yuyue_session);//��ȡ�û���Ϣ
		$data= new stdclass();
		if(empty($yuyue_session)){
			logging::d("yuyue_session", "111111 is:"  );
			$data->reason ="��Ϣ��ȫ";
			$data->status = 0;
		
		}else if(empty($tempuser)) {//���û�ж�Ӧ��user��ϵͳ����
			logging::d("yuyue_session", "1222222 is:"  );
			$data->reason ="ϵͳ����������С����";
			$data->status = 0;
		}else if (empty($user)) {//���û�ж�Ӧ��user��ϵͳ����
			logging::d("yuyue_session", "33333 is:"  );
			$data->reason ="�޴��û�";
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