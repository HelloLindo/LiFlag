<?php
/**
 * Created by Sublimetext3.
 * User: ckjim
 * Date: 2018-02-25
 * Time: 17:30
 */

function getOpenidByCode($code, $appid = APPID, $appsecret = APPSECRET)
{
//appid 和 appsecret在配置文件中
    //根据code获得Access Token 与 openid
    $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
    $access_token_json = https_request($access_token_url);
    $access_token_array = json_decode($access_token_json, true);
//    var_dump($access_token_array);
    if (!isset($access_token_array['openid'])) {
        $errmsg = $access_token_array['errmsg'];
        throw new Exception("openid unauthorized: $errmsg", 401);
    }
    return $access_token_array['openid'];
}

function getUserInfo($code, $appid = APPID, $appsecret = APPSECRET)
{
    $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=$code&grant_type=authorization_code";
    $access_token_json = https_request($access_token_url);
    $access_token_array = json_decode($access_token_json, true);

    if (!isset($access_token_array['openid'])) {
        $errmsg = $access_token_array['errmsg'];
        var_dump($access_token_array);
        throw new Exception("openid unauthorized: $errmsg", 401);
    }
    $access_token = $access_token_array['access_token'];
    $openid = $access_token_array['openid'];
    $get_user_info_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
    $userinfo_json = https_request($get_user_info_url);
    $userinfo_array = json_decode($userinfo_json, true);
    $userinfo_array['openid'] = $openid;
    return $userinfo_array;
}

/** 发请求
 * @param $url
 * @param null $data
 * @return mixed
 */
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * @param $openid
 */
function getTokenByOpenid($openid)
{
    return base64_encode($openid);
}
