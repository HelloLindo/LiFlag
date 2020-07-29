<?php
session_start();
function echoJSON($withStatus, $andMessage)
{
    $data = array('status' => $withStatus, 'message' => $andMessage);
    $jsonstring = json_encode($data);
    header('Content-Type: application/json');
    echo $jsonstring;
}
//配置数据库
$user = '######';
$password = '######';
$db = '######';
$host = '######';
$link = mysqli_init();
$port = 0;
if (!$link) {
    die("mysqli_init failed");
}
$success = mysqli_real_connect(
    $link,
    $host,
    $user,
    $password,
    $db,
    $port
);
if ($success) {
    //定义所需要的字段
    $openid = '';
    $token = '';
    $content = '';
    $userid = '';
    $headimgurl = '';
    $submission_date = '';
    //获取前端传入的t_code参数

    $t_code = "none";

    if (is_string($_GET["t_code"])) {
        $t_code = $_GET["t_code"];
    }

    //通过解密$t_code获取openid
    $openid = strrev(base64_decode($t_code));
    //通过openid在数据库中查找记录
    $sql = "SELECT openid,content,submission_date,headimgurl,userid FROM userinfo WHERE openid ='" . $openid . "'";
    $result = mysqli_query($link, $sql);
    // 从数据库中查找记录
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['openid'] == $openid) {
                $content = $row['content'];
                $submission_date = $row['submission_date'];
                $headimgurl = $row['headimgurl'];
                $userid = $row['userid'];
            }
        }
    }
    $data = array('status' => true,
        'content' => $content,
        'userid' => $userid,
        'headimgurl' => $headimgurl,
        'date' => $submission_date,
    );
    $jsonstring = json_encode($data);
    header('Content-Type: application/json');
    echo $jsonstring;
} else {
    echoJSON(false, "Connect Error: " . mysqli_connect_error());
}
