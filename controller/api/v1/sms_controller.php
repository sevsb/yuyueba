<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");
include_once(dirname(__FILE__) . "/app/TCmessage.class.php");
class sms_controller extends v1_base {
    public function preaction($action) {
    }
   public function send_action() {
	   $nationCode = get_request('nationCode');
        $phoneNumber = get_request('phoneNumber');
        $yuyue_session = get_request('yuyue_session');
        $token = get_request('token');
		
		logging::d("sendsms", "nationCode is:" .$nationCode);
		logging::d("sendsms", "phoneNumber is:" .$phoneNumber);
		
		$verification_code = rand(1000,9999)
		$msg = utf8_encode("123456");
		
		$sender = new SmsSingleSender( WX_APPID,WX_SECRET);
		$data = $sender->send(0, $nationCode, $phoneNumber, $msg, $extend = "", $ext = "");
	
		echo $msg;
		echo '<br/>'
	return array("data" =>  $data);
   }
   
  public function verification_action() {
	   $nationCode = get_request('nationCode');
        $phoneNumber = get_request('phoneNumber');
        $yuyue_session = get_request('yuyue_session');
        $token = get_request('token');
		$verification_code = rand(1000,9999)
		$msg = "【预约一下】您的验证码是：".$verification_code;
		$sender = new SmsSingleSender( WX_APPID,WX_SECRET);
		$data = $sender->send(0, $nationCode, $phoneNumber, $msg, $extend = "", $ext = "");
		logging::d("sendsms", "sms msg is:" . $msg);
  return array("data" =>  $data);
   }


}












