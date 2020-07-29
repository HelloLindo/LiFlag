<?php
require "../config/config.php";
/*
引用自方倍工作室 http://www.fangbei.org/
CopyRight 2014 All Rights Reserved
 */
class class_weixin
{
    public $appid = APPID;
    public $appsecret = APPSECRET;

    //构造函数，获取Access Token
    public function __construct($appid = null, $appsecret = null)
    {
        if ($appid && $appsecret) {
            $this->appid = $appid;
            $this->appsecret = $appsecret;
        }
        $res = file_get_contents('access_token.json');
        $result = json_decode($res, true);
        $this->expires_time = $result["expires_time"];
        $this->access_token = $result["access_token"];

        if (time() > ($this->expires_time + 3600)) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appid . "&secret=" . $this->appsecret;
            $res = $this->http_request($url);
            $result = json_decode($res, true);
            $this->access_token = $result["access_token"];
            $this->expires_time = time();
            file_put_contents('access_token.json', '{"access_token": "' . $this->access_token . '", "expires_time": ' . $this->expires_time . '}');
        }
    }

    //生成长度16的随机字符串
    public function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    //获得微信卡券api_ticket
    public function getCardApiTicket()
    {
        $res = file_get_contents('cardapi_ticket.json');
        $result = json_decode($res, true);
        $this->cardapi_ticket = $result["cardapi_ticket"];
        $this->cardapi_expire = $result["cardapi_expire"];
        if (time() > ($this->cardapi_expire + 3600)) {
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card&access_token=" . $this->access_token;
            $res = $this->http_request($url);
            $result = json_decode($res, true);
            $this->cardapi_ticket = $result["ticket"];
            $this->cardapi_expire = time();
            file_put_contents('cardapi_ticket.json', '{"cardapi_ticket": "' . $this->cardapi_ticket . '", "cardapi_expire": ' . $this->cardapi_expire . '}');
        }
        return $this->cardapi_ticket;
    }

    //cardSign卡券签名
    public function get_cardsign($bizObj)
    {
        //字典序排序
        asort($bizObj);
        //URL键值对拼成字符串
        $buff = "";
        foreach ($bizObj as $k => $v) {
            $buff .= $v;
        }
        //sha1签名
        return sha1($buff);
    }

    //获得JS API的ticket
    private function getJsApiTicket()
    {
        $res = file_get_contents('jsapi_ticket.json');
        $result = json_decode($res, true);
        $this->jsapi_ticket = $result["jsapi_ticket"];
        $this->jsapi_expire = $result["jsapi_expire"];

        if (time() > ($this->jsapi_expire + 3600)) {
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=" . $this->access_token;
            $res = $this->http_request($url);
            $result = json_decode($res, true);
            $this->jsapi_ticket = $result["ticket"];
            $this->jsapi_expire = time();
            file_put_contents('jsapi_ticket.json', '{"jsapi_ticket": "' . $this->jsapi_ticket . '", "jsapi_expire": ' . $this->jsapi_expire . '}');
        }
        return $this->jsapi_ticket;
    }

    //获得签名包
    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        // $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url = $_GET["current_url"];
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId" => $this->appid,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string,
        );
        return $signPackage;
    }

    //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    protected function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
