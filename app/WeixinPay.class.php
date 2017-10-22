<?php
include_once(dirname(__FILE__) . "/../config.php");
class WeixinPay {
	 protected $appid;  
    protected $mch_id;  
    protected $pay_key;  
    protected $openid;  
    protected $out_trade_no;  
    protected $body;  
    protected $total_fee;  
 function __construct($appid, $openid, $mch_id, $pay_key,$out_trade_no,$body,$total_fee ) {  
        $this->appid = $appid;  
        $this->openid = $openid;  
        $this->mch_id = $mch_id;  
        $this->pay_key = $pay_key;  
        $this->out_trade_no = $out_trade_no;  
        $this->body = $body;  
        $this->total_fee = $total_fee;  
		
    }  
		public function pay(){
			//ͳһ�µ��ӿ� 
			$return=$this->weixinapp();
			return $return; 
			}
			
			//΢��С����ӿ� 
		 private function unifiedorder() {  
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';  
        $parameters = array(  
            'appid' => $this->appid, //С����ID  
             'mch_id'=>  $this->mch_id, //�̻����
            'nonce_str' => $this->createNoncestr(), //����ַ���  
//            'body' => 'test', //��Ʒ����  
            'body' => $this->body,  
//            'out_trade_no' => '2015450806125348', //�̻�������  
            'out_trade_no'=> $this->out_trade_no,  
//            'total_fee' => floatval(0.01 * 100), //�ܽ�� ��λ ��  
            'total_fee' => $this->total_fee,  
//            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], //�ն�IP  
            'spbill_create_ip' => '118.89.226.27', //�ն�IP  
            'notify_url' => 'https://www.bloodtear.cn/rendajinrong_server/app/wxpay.php', //֪ͨ��ַ  ȷ����������������  
            'openid' => $this->openid, //�û�id  
            'trade_type' => 'JSAPI'//��������  
        );  
        //ͳһ�µ�ǩ��  
		
        $parameters['sign'] = $this->getSign($parameters);  
        $xmlData = $this->arrayToXml($parameters);  
        $return = $this->xmlToArray($this->postXmlCurl($xmlData, $url, 60));  

        return $return;  
    }  
  
  
    private static function postXmlCurl($xml, $url, $second = 30)   
    {  
        $ch = curl_init();  
        //���ó�ʱ  
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //�ϸ�У��  
        //����header  
        curl_setopt($ch, CURLOPT_HEADER, FALSE);  
        //Ҫ����Ϊ�ַ������������Ļ��  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
        //post�ύ��ʽ  
        curl_setopt($ch, CURLOPT_POST, TRUE);  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);  
  
  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);  
        set_time_limit(0);  
  
  
        //����curl  
        $data = curl_exec($ch);  
        //���ؽ��  
        if ($data) {  

            curl_close($ch);  
            return $data;  
        } else {  
            $error = curl_errno($ch);  
            curl_close($ch);  
            throw new WxPayException("curl����������:$error");  
        }  
    }  
      
      
      
    //����ת����xml  
    private function arrayToXml($arr) {  
        $xml = "<root>";  
        foreach ($arr as $key => $val) {  
            if (is_array($val)) {  
                $xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";  
            } else {  
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";  
            }  
        }  
        $xml .= "</root>";  
        return $xml;  
    }  
  
  
    //xmlת��������  
    private function xmlToArray($xml) {  
  
  
        //��ֹ�����ⲿxmlʵ��   
  
  
        libxml_disable_entity_loader(true);  
  
  
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);  

        $val = json_decode(json_encode($xmlstring), true);  
 
        return $val;  
    }  
  
  
    //΢��С����ӿ�  
    private function weixinapp() {  
        //ͳһ�µ��ӿ�  
        $unifiedorder = $this->unifiedorder();  
 
        $parameters = array(  
            'appId' => $this->appid,//С����ID   
            'timeStamp' => '' . time() . '', //ʱ���  
            'nonceStr' => $this->createNoncestr(), //�����  
            'package' => 'prepay_id=' . $unifiedorder['prepay_id'], //���ݰ�  
            'signType' => 'MD5'//ǩ����ʽ  
        );  
		        
        //ǩ��  
        $parameters['paySign'] = $this->getSign($parameters);  
 return $parameters; 
    }  
  
  
    //���ã���������ַ�����������32λ  
    private function createNoncestr($length = 32) {  
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str = "";  
        for ($i = 0; $i < $length; $i++) {  
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);  
        }  
        return $str;  
    }  
  
  
    //���ã�����ǩ��  
    private function getSign($Obj) {  
        foreach ($Obj as $k => $v) {  
            $Parameters[$k] = $v;  
        }  
        //ǩ������һ�����ֵ����������  
        ksort($Parameters);  
        $String = $this->formatBizQueryParaMap($Parameters, false);  
        //ǩ�����������string�����KEY  
        $String = $String . "&key=" . $this->pay_key;  
        //ǩ����������MD5����  
        $String = md5($String);  
        //ǩ�������ģ������ַ�תΪ��д  
        $result_ = strtoupper($String);  
        return $result_;  
    }  
  
  
    ///���ã���ʽ��������ǩ��������Ҫʹ��  
    private function formatBizQueryParaMap($paraMap, $urlencode) {  
        $buff = "";  
        ksort($paraMap);  
        foreach ($paraMap as $k => $v) {  
            if ($urlencode) {  
                $v = urlencode($v);  
            }  
            $buff .= $k . "=" . $v . "&";  
        }  
        $reqPar;  
        if (strlen($buff) > 0) {  
            $reqPar = substr($buff, 0, strlen($buff) - 1);  
        }  
        return $reqPar;  
    }  
  
  
}