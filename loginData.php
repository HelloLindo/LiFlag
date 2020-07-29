<?php
/**
 * Created by Sublimetext3.
 * User: ckjim
 * Date: 2018-02-22
 * Time: 06:09
 */
require "./config/config.php";
require "./func/token.php";
require "./func/http.php";
require './Medoo/src/Medoo.php';
if (!isset($_SESSION)) {
    session_start();
}
try {
    if (!isset($_SESSION['stoken']) || empty($_SERVER['stoken'])) {
        //无stoken 未登录
        if (!isset($_GET['code']) || empty($_GET['code'])) {
            //不是去code回来的 是第一次进入 去拿code
            $uri = REDIRECT_URI;
            $redirect_uri = urlencode($uri);
            $codeUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . APPID . "&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            jump2UrlInTime($codeUrl);
            //终止页面加载
            throw new \Exception(null, 200);
        } else {
            //有code 获取code回来的 需要用code换openid 用openid创建token
            unset($code);
            $code = $_GET['code'];
            $userinfo_arr = getUserInfo($code);
            $openid = $userinfo_arr['openid'];
            $stoken = getTokenByOpenid($openid);
            $userid = $userinfo_arr['nickname'];
            $headimgurl = $userinfo_arr['headimgurl'];
            //生成用户识别码
            $rev_openid = strrev($openid);
            $t_code = getTokenByOpenid($rev_openid);
            $_SESSION["t_code"] = $t_code;

            // ************************************************************************************************

            //测试的stoken数据
            //    $_SESSION['stoken'] = 'dadad' ;
            //到此处应该已有token 避免死循环 加入报错
            if (!isset($stoken) || empty($stoken) || !isset($t_code) || empty($t_code)) {
                throw new \Exception("token error", 500);
            }
            if (!isset($userid) || empty($userid)) {
                throw new \Exception("userid error", 500);
            }
            if (!isset($headimgurl) || empty($headimgurl)) {
                throw new \Exception("headimgurl error", 500);
            }
            //用session储存用户数据
            $_SESSION['stoken'] = $stoken;
            $_SESSION['openid'] = $openid;
            $_SESSION['userid'] = $userid;
            $_SESSION['headimgurl'] = $headimgurl;

            $before_page = './HTML/before_index.php';
            $after_page = './HTML/index.php';
            $database = new Medoo\Medoo([
                'database_type' => 'mysql',
                'database_name' => '######',
                'server' => '######',
                'username' => 'zh',
                'password' => '######',
                'charset' => 'utf8',
                'port' => 0,
            ]);
            //根据openid判断用户是否立过flag

            //有了token 进入
            $data_token = $database->select("userinfo", "t_code", ["openid" => $openid]);
            //展示页面

            //活动开始
            //如果openid已存入数据库，说明已经立flag
            if ($data_token) {
                $database->update("userinfo", ["headimgurl" => $_SESSION['headimgurl']], ["openid" => $openid]);
                foreach ($data_token as $data) {
                    $_SESSION["t_code"] = $data;
                }

                jump2UrlInTime($after_page);
            } else {
                jump2UrlInTime($before_page);
            }

            //活动结束

        }
    }
} catch (Exception $e) {
    http_code($e->getCode());
    echo $e->getMessage();
}
