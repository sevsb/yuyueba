<?php
include_once(dirname(__FILE__) . "/../config.php");

class Wxapi {
    
    public static function wx_auth($code){
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $postString = array(
            "appid" => WX_APPID,
            "secret" => WX_SECRET,
            "js_code" => $code,
            "grant_type" => "authorization_code");
        $wx_auth_ret = comm_curl_request($url, $postString);
        return json_decode($wx_auth_ret);
    }
            
}







function comm_curl_request($url,$postString='',$httpHeader='')  { 
    $ch = curl_init();  
    curl_setopt($ch,CURLOPT_URL,$url);  
    curl_setopt($ch,CURLOPT_POSTFIELDS,$postString);  
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //这个是重点。不加这curl报错
    curl_setopt($ch,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);  

    if(!empty($httpHeader) && is_array($httpHeader))  
    {  
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);  
    }  
    $data = curl_exec($ch);  
    $info = curl_getinfo($ch);  
    //var_dump(curl_error($ch)); 
    //var_dump($info);  
    curl_close($ch);  
    return $data;  
}  




?>