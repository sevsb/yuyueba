<?php
// Works well with php5.3 and php5.6.
//namespace Qcloud\Sms;
require_once('SmsTools.php');

class SmsSingleSender {
    var $url;
    var $appid;
    var $appkey;
    var $util;
    function __construct($appid, $appkey) {
        $this->url = "https://yun.tim.qq.com/v5/tlssmssvr/sendsms";
        $this->appid =  $appid;
        $this->appkey = $appkey;
        $this->util = new SmsSenderUtil();
    }
    /**
     * ��ͨ��������ȷָ�����ݣ�����ж��ǩ���������������ԡ����ķ�ʽ��ӵ���Ϣ�����У�����ϵͳ��ʹ��Ĭ��ǩ��
     * @param int $type �������ͣ�0 Ϊ��ͨ���ţ�1 Ӫ������
     * @param string $nationCode �����룬�� 86 Ϊ�й�
     * @param string $phoneNumber ������������ֻ���
     * @param string $msg ��Ϣ���ݣ������������ģ���ʽһ�£����򽫷��ش���
     * @param string $extend ��չ�룬����մ�
     * @param string $ext �����ԭ�����صĲ���������մ�
     * @return string json string { "result": xxxxx, "errmsg": "xxxxxx" ... }����ʡ�Ե����ݲμ�Э���ĵ�
     */
    function send($type, $nationCode, $phoneNumber, $msg, $extend = "", $ext = "") {
/*
�������
{
    "tel": {
        "nationcode": "86",
        "mobile": "13788888888"
    },
    "type": 0,
    "msg": "�����֤����1234",
    "sig": "fdba654e05bc0d15796713a1a1a2318c",
    "time": 1479888540,
    "extend": "",
    "ext": ""
}
Ӧ�����
{
    "result": 0,
    "errmsg": "OK",
    "ext": "",
    "sid": "xxxxxxx",
    "fee": 1
}
*/
        $random = $this->util->getRandom();
        $curTime = time();
        $wholeUrl = $this->url . "?sdkappid=" . $this->appid . "&random=" . $random;
        // ����Э����֯ post ����
        $data = new \stdClass();
        $tel = new \stdClass();
        $tel->nationcode = "".$nationCode;
        $tel->mobile = "".$phoneNumber;
        $data->tel = $tel;
        $data->type = (int)$type;
        $data->msg = $msg;
        $data->sig = hash("sha256",
            "appkey=".$this->appkey."&random=".$random."&time=".$curTime."&mobile=".$phoneNumber, FALSE);
        $data->time = $curTime;
        $data->extend = $extend;
        $data->ext = $ext;
        return $this->util->sendCurlPost($wholeUrl, $data);
    }
    /**
     * ָ��ģ�嵥��
     * @param string $nationCode �����룬�� 86 Ϊ�й�
     * @param string $phoneNumber ������������ֻ���
     * @param int $templId ģ�� id
     * @param array $params ģ������б���ģ�� {1}...{2}...{3}����ô��Ҫ����������
     * @param string $sign ǩ���������մ���ϵͳ��ʹ��Ĭ��ǩ��
     * @param string $extend ��չ�룬����մ�
     * @param string $ext �����ԭ�����صĲ���������մ�
     * @return string json string { "result": xxxxx, "errmsg": "xxxxxx"  ... }����ʡ�Ե����ݲμ�Э���ĵ�
     */
    function sendWithParam($nationCode, $phoneNumber, $templId, $params, $sign = "С���ʿƼ�", $extend = "", $ext = "") {
/*
�������
{
    "tel": {
        "nationcode": "86",
        "mobile": "13788888888"
    },
    "sign": "��Ѷ��",
    "tpl_id": 19,
    "params": [
        "��֤��",
        "1234",
        "4"
    ],
    "sig": "fdba654e05bc0d15796713a1a1a2318c",
    "time": 1479888540,
    "extend": "",
    "ext": ""
}
Ӧ�����
{
    "result": 0,
    "errmsg": "OK",
    "ext": "",
    "sid": "xxxxxxx",
    "fee": 1
}
*/
        $random = $this->util->getRandom();
        $curTime = time();
        $wholeUrl = $this->url . "?sdkappid=" . $this->appid . "&random=" . $random;
        // ����Э����֯ post ����
        $data = new \stdClass();
        $tel = new \stdClass();
        $tel->nationcode = "".$nationCode;
        $tel->mobile = "".$phoneNumber;
        $data->tel = $tel;
        $data->sig = $this->util->calculateSigForTempl($this->appkey, $random, $curTime, $phoneNumber);
        $data->tpl_id = $templId;
        $data->params = $params;
        $data->sign = $sign;
        $data->time = $curTime;
        $data->extend = $extend;
        $data->ext = $ext;
        return $this->util->sendCurlPost($wholeUrl, $data);
    }
}
