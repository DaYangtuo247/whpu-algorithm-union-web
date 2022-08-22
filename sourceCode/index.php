<?php
session_start();
include 'control/connect.php';
date_default_timezone_set('PRC'); //php时间戳转换时区设定为中国
$sql = "select * from homepage";
$result = mysqli_query($publicdata, $sql); //执行sql
while ($row = mysqli_fetch_assoc($result))
    $data[$row['name']] = $row['content'];
?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/css/general.css">
    <link rel="stylesheet" href="style/css/index.css">
    <link rel="stylesheet" href="style/css/header.css">
    <link rel="shortcut icon" href="style/image/favicon.ico">
    <title>WHPU Algorithms Association</title>
</head>

<body>
    <script src="script/jQuery.min.js"></script>
    <?php include 'views/nav.html' ?>
    <?php include 'views/header.php' ?>
    <div class="main">
        <div class="banner">
            <div>
                <div>
                    <div>
                        <div style="padding: 80px 150px;line-height: 2;">
                            <h1><span>公告</span></h1>
                            <p><span>当前处于测试阶段，如在使用过程中发现bug，劳烦发送到</span></p>
                            <p>QQ：<a href="tencent://message/?uin=2473605320&Site=&Menu=yes"><span>2473605320</span></a><span> </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div><img src="banner/3.jpg"></div>
            </div>
        </div>
        <div class="schedule">
            <div class="calendar" onselectstart="return false">
                <?php
                $month = array("一月大", "二月平", "三月大", "四月小", "五月大", "六月小", "七月大", "八月大", "九月小", "十月大", "十一月小", "十二月大");
                $weekarray = array("日", "一", "二", "三", "四", "五", "六");
                echo '<span class="month">' . $month[date("n") - 1] . '</span>';
                echo '<span class="month-today">' . date("d") . '</span>';
                echo '<span class="week">' . "星期" . $weekarray[date("w")] . '</span>';
                ?>
            </div>
            <ul class="count-down">
                <li>距离ICPC还剩number天hour时</li>
                <li>距离ICPC还剩number天hour时</li>
                <li>距离ICPC还剩number天hour时</li>
                <li>距离ICPC还剩number天hour时</li>
            </ul>
        </div>
        <div>
            <ul class="service">
                <li>
                    <h3>当前在线</h3>
                    <p><?php
                        $accountSQL = "SELECT * FROM user WHERE `online`='1'";
                        echo mysqli_num_rows(mysqli_query($account, $accountSQL));
                        ?></p>
                    <i class="iconfont">&#xe72a;</i>
                </li>
                <li>
                    <h3>用户总数</h3>
                    <p><?php
                        $accountSQL = "SELECT * FROM user";
                        echo mysqli_num_rows(mysqli_query($account, $accountSQL));
                        ?></p>
                    <i class="iconfont">&#xe600;</i>
                </li>
                <li>
                    <h3>今日访问</h3>
                    <p><?php echo $data['todayVisted'] ?></p>
                    <i class="iconfont">&#xe8c7;</i>
                </li>
                <li>
                    <h3>今日发帖</h3>
                    <p><?php
                        $today_start_time = strtotime(date('Y-m-d', time()));
                        $articleSQL = "SELECT * FROM article_describe WHERE create_time>='$today_start_time'";
                        echo mysqli_num_rows(mysqli_query($forum, $articleSQL));
                        ?></p>
                    <i class="iconfont">&#xe628;</i>
                </li>
                <li>
                    <h3>帖子总数</h3>
                    <p><?php
                        $articleSQL = "SELECT * FROM article_describe";
                        echo mysqli_num_rows(mysqli_query($forum, $articleSQL));
                        ?></p>
                    <i class="iconfont">&#xe601;</i>
                </li>
                <li>
                    <h3>网盘文件</h3>
                    <p><?php echo $data['solveAll'] ?></p>
                    <i class="iconfont" style="font-size: 45px;margin-right: -4px;">&#xe618;</i>
                </li>
            </ul>
        </div>
        <div class="content">
            <div class="recent">
                <h2>近期帖子</h2>
                <ul class="recent-data">
                </ul>
            </div>
        </div>
        <div class="ranking-hot">
            <h2>最近活跃</h2>
            <table class="ranking-hot-data" border="0">
                <tr>
                    <th>排名</th>
                    <th>用户名</th>
                    <th>最后登录时间</th>
                </tr>
                <?php
                $lastLoginTimeSQL = "SELECT * FROM user st ORDER BY st.lastLoginTime DESC";
                $lastLoginTime = mysqli_query($account, $lastLoginTimeSQL);
                for ($i = 0; $i < 10 && $row = mysqli_fetch_assoc($lastLoginTime); $i++) {
                    echo "<tr class='user'><td>" . $i + 1 . '</td><td class="username"><img src="' . $row['headImg'] . '" alt="无"><span>' . $row['username'] . "</span></td><td>" . date("Y-m-d H:i:s", $row['lastLoginTime']) . "</td></td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <script src="script/HappyImage.min.js"></script>
    <script src="script/coco-message.js"></script>
    <script src="script/header.js"></script>
    <script src="/script/moment.min.js"></script>
    <script src="/script/timeFormat.js"></script>
    <script>
        $(function() {
            $('#site-title').text("WHPU Algorithms Association");
        });
        /**
         * 轮播图
         * 来源于 https://github.com/liumingmusic/HappyImage
         */
        $(".banner").HappyImage({
            arrowHoverShow: true,
            autoplay: 6000,
            duration: 500
        });

        $(function() {
            $('#articles').empty();
            $.ajax({
                type: "post",
                url: "/forum/API/getArticle_describe.php",
                data: JSON.stringify({
                    page: 1,
                    tag: 'all',
                    classify: 'recent_posts'
                }),
                dataType: "json",
                contentType: "application/json; charset=utf-8",
                success: function(data) {
                    for (let i = 0; i < data['articles'].length && i < 12; i++) {
                        let dataTemp = data['articles'][i];
                        let article = '<li class="Article-quick"><a href="/forum/article.php?id=' + dataTemp.article_id + '"><div class="tag" >' + dataTemp.tag + '</div>';
                        article += '<img src="' + dataTemp.headImg + '" alt="">';
                        article += '<h3 class="title">' + dataTemp.title + '</h3>';
                        article += '<p class="describe">' + dataTemp.describe + '</p>';
                        article += '<ul class="rests iconfont"><li>' + getDateTimeFormat(dataTemp.create_time) + '</li><li>&#xe744; ' + dataTemp.comment + '</li><li>&#xe6ee; ' + dataTemp.page_view + '</li></ul></a></li>';
                        $('.recent-data').append(article);
                    }
                },
                error: function(data) {
                    $('#articles').text("<p style='text-align:center;'>获取文章失败，请联系管理员！</p>");
                }
            });
        });
    </script>
</body>

</html>