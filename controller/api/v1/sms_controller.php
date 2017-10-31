<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");
include_once(dirname(__FILE__) . "/../../../app/SmsSingleSender.class.php");

class sms_controller extends v1_base {

   public function send_action() {
	   $nationCode = get_request('nationCode');
        $phoneNumber = get_request('phoneNumber');
        $yuyue_session = get_request('yuyue_session');
        $token = get_request('token');
		$templId = 50285;

		logging::d("sendsms", "nationCode is:" .$nationCode);
		logging::d("sendsms", "phoneNumber is:" .$phoneNumber);
		
		$verification_code = rand(1000,9999);
	$params =[""+$verification_code];
		
		$sender = new SmsSingleSender( WX_SMS_SDKID,WX_SMS_SECRET);
		$data = $sender->sendWithParam($nationCode, $phoneNumber, $templId, $params);
		$data=json_decode($data);
		return array("data" =>  $data);
   }
}