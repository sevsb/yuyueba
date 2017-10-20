<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");
include_once(dirname(__FILE__) . "/app/TCmessage.php");
class sms_controller extends v1_base {

   public function send_action() {
	   $nationCode = get_request('nationCode');
        $phoneNumber = get_request('phoneNumber');
        $yuyue_session = get_request('yuyue_session');
        $token = get_request('token');
		
		logging::d("sendsms", "nationCode is:" .$nationCode);
		logging::d("sendsms", "phoneNumber is:" .$phoneNumber);
		
		$verification_code = rand(1000,9999)
	
		
		$sender = new SmsSingleSender( WX_APPID,WX_SECRET);
		$data = $sender->send(0, $nationCode, $phoneNumber, "123456", "", "");
	
	return array("data" =>  $data);
   }
   
  

}













