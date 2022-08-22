<?php
include '../control/connect.php';
$url = $_SERVER['REQUEST_URI'];

//使用nginx伪静态后关闭该注释
// $usernameRet = preg_match('/\/userinfo\/(.+)$/', $url, $username);
// if (!$usernameRet)
//     header("Location: error/error.html");
// echo $username[1];

$url = mb_substr($url, stripos($url, "?") + 1); //截取问号后的参数
parse_str($url, $username); //将参数转换为关联数组
$username = $username['username'];
$userinfoSQL = "SELECT * FROM user WHERE username = '$username'";
$userinfo = mysqli_query($account, $userinfoSQL);
if ($userinfo && mysqli_num_rows($userinfo) == 0)
    header("Location: /error/error.html");

$userinfo = mysqli_fetch_assoc($userinfo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="userinfo.css">
    <link rel="stylesheet" href="/style/css/general.css">
    <link rel="stylesheet" href="/style/css/nav.css">
    <link rel="stylesheet" href="/style/css/header.css">
    <title>用户信息</title>
</head>

<body>

    <?php include '../views/nav.html' ?>
    <?php include '../views/header.php' ?>

    <div class="personal-info clearfix">
        <ul class="sonNav">
            <li><a href="">我的资料</a></li>
            <li><a href="">修改信息</a></li>
            <li><a href="">我的收藏</a></li>
            <li><a href="">我的发帖</a></li>
            <li><a href="">我的通知</a></li>
        </ul>
        <div class="content">
            <div class="myinfo">
                <ul class="myinfo-head clearfix">
                    <li class="info-left">
                        <div class="headimg">
                            <img src="/style/image/headEdit.png" class="edit">
                            <img src="<?= $userinfo['headImg'] ?>" class="headimgsrc">
                            <input type="file" name="file" id="fileUpload" accept="image/gif, image/jpeg, image/png" onchange="uploadFile(this)">
                        </div>
                        <p><?= $userinfo['username']; ?></p>
                    </li>
                    <li class="info-right">
                        <p>个人简介:</p>
                        <div class="introduce"><?php if ($userinfo['introduce'] == '') echo "该用户太懒，没有填写哦";
                                                else echo $userinfo['introduce']; ?></div>
                    </li>
                </ul>
                <ul class="myinfo-body clearfix">

                    <li>
                        <p><span class="iconfont">&#xec8c;<?= $userinfo['like'] ?></span></p>
                    </li>
                    <li><a href="<?= $userinfo['leetCodePage'] ?>">leetCode主页</a></li>
                    <li><a href="<?= $userinfo['codeforcesPage'] ?>">codeForces主页</a></li>
                    <li><a href="<?= $userinfo['AcWingPage'] ?>">AcWing主页</a></li>
                    <li><span>注册时间:</span> <span class="registerTime"><?= $userinfo['registrationDate'] ?></span></li>
                    <li><span>最后一次离线时间: </span><span class="last_online_time"><?= $userinfo['lastLoginTime'] ?></span></li>
                </ul>
            </div>
        </div>
    </div>
    </div>
    <script src="/script/jQuery.min.js"></script>
    <script src="/script/jquery.ajaxfileupload.js"></script>
    <script src="/script/coco-message.js"></script>
    <script src="/script/header.js"></script>
    <script src="/script/moment.min.js"></script>
    <script src="/script/moment-zh_cn.min.js"></script>
    <script src="/script/timeFormat.js"></script>
    <script>
        $(function() {
            $('#site-title').text("个人信息");
            $('.registerTime').text(getDateTimeFormat(Number($('.registerTime').text())));
            $('.last_online_time').text(getDateTimeFormat(Number($('.last_online_time').text())));
        });


        function uploadFile(obj) {
            $.ajaxFileUpload({
                url: "../control/updHeadImg.php",
                secureuri: false, // 一般设置为false
                fileElementId: "fileUpload", // 文件上传表单的id <input type="file" id="fileUpload" name="file" />
                dataType: 'json', // 返回值类型 一般设置为json
                success: function(data) {
                    cocoMessage.success("成功", 3000);
                },
                error: function(data) {
                    cocoMessage.success("服务器异常", 3000);
                }
            });
            return false;
        }
    </script>
</body>

</html>