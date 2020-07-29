
<?php
//已立flag的跳转页面，设为首页
require '../func/http.php';
require '../Medoo/src/Medoo.php';
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['stoken']) || empty($_SESSION['stoken']) || !isset($_SESSION['userid']) || empty($_SESSION['userid']) || !isset($_SESSION['headimgurl']) || empty($_SESSION['headimgurl']) || empty($_SESSION['openid'])) {
    //没stoken 回去入口取
    header('Location:../loginData.php');
    die;
} else {
    if (isset($_SESSION["t_code"])) {
        $token = $_SESSION["t_code"];
    } else {
        $token = "tokenError";
    }
}

?>
<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>荔Flag</title>
    <link rel="stylesheet" href="../CSS/index.css" type="text/css">
</head>

<body>
    <div class="flag_frame">

        <div id="flag_title">
            <img alt="荔Flag标题" src="http://lizhi-1251014091.cosgz.myqcloud.com/LiFlag/IMAGE/flag_title.png">
        </div>

        <div id="create_flag">
            <a href="#">
                <img id="create" alt="立个Flag按钮" src="http://lizhi-1251014091.cosgz.myqcloud.com/LiFlag/IMAGE/create_flag.png">
            </a>
        </div>

        <div id="my_flag">
            <a href="view_letter.html?<?php echo $token ?>">
                <img id="my" alt="我的Flag按钮" src="http://lizhi-1251014091.cosgz.myqcloud.com/LiFlag/IMAGE/my_flag.png">
            </a>
        </div>

        <div id="notice">
            <p>
                注：每人只有一次立Flag机会。学期结束时，你的Flag会完成得怎样呢？
            </p>
        </div>


        <div id="bottom">
            <p>
                如需帮助或反馈&nbsp;请加小知微信：WithSzu<br> Copyright&copy;深大荔知<br>
            </p>
            <br>
            <p id="author">
                Lindo &amp; J1MMY &amp; ckjim &amp; 佚名
            </p>
        </div>
    </div>

    <script type="text/javascript">
        var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
        document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F55471b6aff2ffa1f9e2ac5cbe755ef91' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript" src="http://tajs.qq.com/stats?sId=65455601" charset="UTF-8"></script>

    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script src="https://cdn.bootcss.com/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="../JS/after_index.js"></script>
</body>

</html>