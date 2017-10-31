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
		logging::d("sendsms", "verification_code is:" .$verification_code);
	$params =array("".$verification_code);
		
		$sender = new SmsSingleSender( WX_SMS_SDKID,WX_SMS_SECRET);
		$msg ="【小柠檬科技】您的验证码是（".$verification_code."），如非本人操作，请忽略本短信。"
		$data = $sender->send(0, $nationCode, $phoneNumber, $msg, $extend = "", $ext = "")
		//$data = $sender->sendWithParam($nationCode, $phoneNumber, $templId, $params);
		$data=json_decode($data);
		return array("data" =>  $data);
   }
}