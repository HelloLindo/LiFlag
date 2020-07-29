// var textarea_flag = false;

var token = "";

$(document).ready(function() {
    var myDate = new Date();
    var year = myDate.getFullYear(); //获取当前年
    var month = myDate.getMonth() + 1; //获取当前月
    var day = myDate.getDate(); //获取当前日
    $("#date span").text(year + "." + month + "." + day);

    var userid = "";
    var headimgurl = "";

    $.getJSON("../HTML/session_code.php", function(data, status) {
        userid = data.userid;
        headimgurl = data.headimgurl;
        token = data.token;

        $("#profile img").attr("src", headimgurl);
        $("#name span").text(userid);
    });

    // $("#letter_textarea textarea").scroll(function() {
    //     var len = $("#letter_textarea textarea").val().length;
    //     var cut_content = $("#letter_textarea textarea")
    //         .val()
    //         .substr(0, len - 1);
    //     $("#letter_textarea textarea").val(cut_content);
    // });
    setInterval("checkLength()", 500);

    var current_url = decodeURI(window.location.href);

    var s_appId = "";
    var s_nonceStr = "";
    var s_timestamp = "";
    var s_signature = "";
    var s_jsApiList = "";
    var getsign_state = false;

    $.ajax({
        url: "../HTML/get_signpakage.php",
        type: "GET",
        dataType: "JSON",
        data: {
            current_url: current_url
        },
        success: function(data, status) {
            s_appId = data.appId;
            s_nonceStr = data.nonceStr;
            s_timestamp = data.timestamp;
            s_signature = data.signature;
            s_jsApiList = [
                "checkJsApi",
                "onMenuShareTimeline",
                "onMenuShareAppMessage"
            ];
            getsign_state = true;
        },
        error: function() {
            alert("后端批准分享Flag时出错啦！请截图并联系小知~");
        },
        // error: function(jqXHR, textStatus, errorThrown) {
        //     /*弹出jqXHR对象的信息*/
        //     alert(jqXHR.responseText);
        //     // alert(jqXHR.status);
        //     // alert(jqXHR.readyState);
        //     // alert(jqXHR.statusText);
        //     // /*弹出其他两个参数的信息*/
        //     // alert(textStatus);
        //     // alert(errorThrown);
        // },
        async: false
    });

    if (getsign_state) {
        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: s_appId, // 必填，公众号的唯一标识
            timestamp: s_timestamp, // 必填，生成签名的时间戳
            nonceStr: s_nonceStr, // 必填，生成签名的随机串
            signature: s_signature, // 必填，签名
            jsApiList: s_jsApiList // 必填，需要使用的JS接口列表
        });
        wx.ready(function() {
            // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，
            // 所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
            wx.onMenuShareAppMessage({
                title: "荔Flag – 『深大荔知』",
                desc: "http://lizhi.szer.me/flag/",
                link: "http://lizhi.szer.me/flag/",
                imgUrl: "http://lizhi-1251014091.cosgz.myqcloud.com/LiFlag/IMAGE/share_img.jpg",
                trigger: function(res) {
                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                },
                success: function(res) {
                    alert("分享成功");
                },
                cancel: function(res) {},
                fail: function(res) {
                    alert(
                        "好像分享出了点问题哦~请截图并联系小知~\n" + JSON.stringify(res)
                    );
                    // alert(JSON.stringify(res));
                }
            });
            wx.onMenuShareTimeline({
                title: "荔Flag – 『深大荔知』", // 分享标题
                link: "http://lizhi.szer.me/flag/", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: "http://lizhi-1251014091.cosgz.myqcloud.com/LiFlag/IMAGE/share_img.jpg", // 分享图标
                success: function() {
                    alert("分享成功");
                    // 用户确认分享后执行的回调函数
                },
                cancel: function() {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
    }
});

$("#create_button button").click(function() {
    var $content = $("#letter_textarea textarea").val();
    if ($content == "") {
        alert("至少要写点儿什么才能算立下Flag哦~");
    } else {
        if (confirm("是否确定立下Flag，确定后无法修改！")) {
            $.ajax({
                url: "../HTML/save.php",
                type: "POST",
                dataType: "JSON",
                data: { letter_text: $content },
                success: function(data, status) {
                    if (data.retcode == 200) {
                        $.cookie("token", token, { expires: 7, path: "/" });
                        $.cookie("flag_state", true, { expires: 7, path: "/" });
                        alert("立Flag成功！快叫小伙伴来监督吧~");
                        window.location.href = "../HTML/view_letter.html?" + data.token;
                    } else {
                        alert("立Flag时出错啦！请截图联系小知~\n" + data);
                    }
                },
                // error: function(data) {
                //     alert("立Flag时出错啦！请联系小知~\n" + data);
                // }
                error: function(jqXHR, textStatus, errorThrown) {
                    /*弹出jqXHR对象的信息*/
                    alert(jqXHR.responseText);
                    // alert(jqXHR.status);
                    // alert(jqXHR.readyState);
                    // alert(jqXHR.statusText);
                    // /*弹出其他两个参数的信息*/
                    // alert(textStatus);
                    // alert(errorThrown);
                }
            });

            // $.post(
            //     "../HTML/save.php", {
            //         letter_text: $content
            //     },
            //     function(data, status) {
            //         if (data == "success") {
            //             alert("立Flag成功！快点右上角分享给小伙伴监督吧~");
            //             window.location.href = "../HTML/view_letter.html";
            //         } else {
            //             alert("立Flag时出错啦！请联系小知~");
            //         }
            //     }
            // );
        }
    }
});

function checkLength() {
    var $length = $("#letter_textarea textarea").val().length;
    if ($length > 117) {
        var $cut_content = $("#letter_textarea textarea").val();
        $cut_content = $cut_content.substr(0, 117);
        $("#letter_textarea textarea").val($cut_content);
    }
}