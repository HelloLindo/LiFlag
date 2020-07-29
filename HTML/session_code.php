<?php
session_start();

$userid = "nodata";
$headimgurl = "nodata";
$token = "nodata";

if (isset($_SESSION['userid']) && isset($_SESSION['headimgurl'])) {
    $userid = $_SESSION['userid'];
    $headimgurl = $_SESSION['headimgurl'];
    $token = $_SESSION["t_code"];
}
$json_data = array("userid" => $userid, "headimgurl" => $headimgurl, "token" => $token);
$json_obj = json_encode($json_data);
echo $json_obj;
