<?php
require_once './wxjssdk.class.php';
$weixin = new class_weixin();
//获取签名包
$signPackage = $weixin->getSignPackage();
echo json_encode($signPackage);
