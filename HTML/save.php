<?php
/**
 * Created by Sublimetext3.
 * User: ckjim
 * Date: 2018-2-27
 * Time: 06:00
 */
require '../Medoo/src/Medoo.php';
require '../func/http.php';
require '../func/safe.php';
require '../func/token.php';
if (!isset($_SESSION)) {
    session_start();
}

try {
    $stoken = isset($_SESSION['stoken']) ? $_SESSION['stoken'] : null;
    $userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : null;
    $headimgurl = isset($_SESSION['headimgurl']) ? $_SESSION['headimgurl'] : null;
    $content = isset($_POST['letter_text']) ? $_POST['letter_text'] : null;
    if (empty($stoken)) {
        // alert2Url("好像出了一点小问题...请重试哦", '../index.php');
        throw new Exception("stoken null", 406);
    }
    if (empty($content)) {
        throw new Exception("params null", 400);
    }
    if (empty($userid)) {
        throw new Exception("params null", 400);
    }
    if (empty($headimgurl)) {
        throw new Exception("params null", 400);
    }
    //参数检查
    $safe = new Safe();
    $stoken = $safe->str_check($stoken);
    //$title = $safe->str_check($title);
    $content = $safe->str_check($content);
    $openid = base64_decode($stoken);
    //生成用户识别码
    $rev_openid = strrev($openid);
    $t_code = getTokenByOpenid($rev_openid);
    // $openid = 122;
    $database = new Medoo\Medoo([
        'database_type' => 'mysql',
        'database_name' => 'zhanhong',
        'server' => '55c5f3742f54d.gz.cdb.myqcloud.com',
        'username' => 'zh',
        'password' => 'lzjsB&H!Z@19',
        'charset' => 'utf8',
        'port' => 13125,
    ]);
//数据插入
    $table = "userinfo";
    $insert = $database->insert($table, [
        "openid" => $openid,
        "userid" => $userid,
        "headimgurl" => $headimgurl,
        "content" => $content,
        "submission_date" => date("Y-m-d", time()),
        "token" => $stoken,
        "t_code" => $t_code, //储存识别码
    ]);

    // 返回识别码
    if ($insert) {
        $re_msg = array("retcode" => 200, "retmsg" => null, "token" => $t_code);
        $_SESSION["t_code"] = $t_code;
        echo json_encode($re_msg);
    } else {
        throw new Exception("Connection error");
    }

} catch (Exception $e) {
    http_code($e->getCode());
    echo $e->getMessage();
}
