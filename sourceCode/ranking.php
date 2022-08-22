<?php
session_start(); //为了后面获取用户在线情况
date_default_timezone_set('PRC'); //php时间戳转换时区设定为中国
include 'control/connect.php';

//默认按照 力扣评分， 做题数， codeforces最高评分， codeforces当前评分 进行降序排序
$sql = "SELECT * FROM rankinglist st ORDER BY st.leetCodeIntegral DESC, st.solveProblems DESC, st.codeForcesIntegralMAX DESC, st.codeForcesIntegralNOW DESC";
$ranking = mysqli_query($publicdata, $sql);

?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/css/general.css">
    <link rel="stylesheet" href="style/css/ranking.css">
    <link rel="shortcut icon" href="style/image/favicon.ico">
    <title>WHPU天梯</title>
</head>

<body>
    <script src="/script/jQuery.min.js"></script>
    <script src="/script/loginCheck.js"></script>
    <?php include 'views/nav.html' ?>
    <table class="ranking" border="0" cellspacing="0" id="tableSort">
        <thead>
            <tr class="person-head">
                <th onclick="tableSort(0)" onselectstart="return false"><span>排名</span><span class="iconfont sort">&#xe627;</span></th>
                <th onclick="tableSort(1)" onselectstart="return false"><span>姓名</span><span class="iconfont sort">&#xe627;</span></th>
                <th onclick="tableSort(2)" onselectstart="return false"><img src="style/image/leetcode-logo.png"><span>当前评分</span><span class="iconfont sort">&#xe627;</span></th>
                <th onclick="tableSort(3)" onselectstart="return false"><span>刷题数</span><span class="iconfont sort">&#xe627;</span></th>
                <?php
                //获取周赛场次
                for ($i = 1; $i <= 2; $i++) {
                    $leetCodeContest = "select * from homepage where name = 'leetCodeContest" . $i . "'";
                    $temp = mysqli_fetch_assoc(mysqli_query($publicdata, $leetCodeContest));
                    preg_match('/^(bi)?weekly-contest-(\d+)$/', $temp['content'], $contestNumber);
                    $leetCodeContest_Arr[] = $temp;
                    echo '<th onclick="tableSort(' . $i + 3 . ')" onselectstart="return false"><span>';
                    if ($contestNumber[1])
                        echo "双周赛-" . $contestNumber[2];
                    else
                        echo "周赛-" . $contestNumber[2];
                    echo '</span><span class="iconfont sort">&#xe627;</span></th>';
                }
                ?>

                <th onclick="tableSort(6)" onselectstart="return false"><img src="style/image/codeforces-logo.png" alt=""><span>当前评分</span><span class="iconfont"></span><span class="iconfont sort">&#xe627;</span></th>
                <th onclick="tableSort(7)" onselectstart="return false"><img src="style/image/codeforces-logo.png" alt=""><span>最高评分</span><span class="iconfont"></span><span class="iconfont sort">&#xe627;</span></th>
                <th onclick="tableSort(8)" onselectstart="return false"><span>AcWing</span><span class="iconfont"></span><span class="iconfont sort">&#xe627;</span></th>
                <th onclick="tableSort(9)" onselectstart="return false"><span>最近活跃</span><span class="iconfont"></span><span class="iconfont sort">&#xe627;</span></th>
                <th onclick="tableSort(10)" onselectstart="return false"><span>在线情况</span><span class="iconfont"></span><span class="iconfont sort">&#xe627;</span></th>
                <th onclick="tableSort(11)" onselectstart="return false"><span>点赞</span><span class="iconfont"></span><span class="iconfont sort">&#xe627;</span></th>
            </tr>
        </thead>
        <tbody>
            <?php
            //获取所有用户信息
            for ($i = 1; $row = mysqli_fetch_assoc($ranking); $i++) {
            ?><tr class="person-data">
                    <?php
                    $accountSQL = "select * from user where username = '" . $row['username'] . "'";
                    $accountInfo = mysqli_fetch_assoc(mysqli_query($account, $accountSQL));
                    ?>
                    <td><?= $i; ?></td>
                    <td class="nickname"><a href=""><img src="<?= $accountInfo['headImg'] ?>" alt="no"><span><?= $row['username'] ?></span></a></td>
                    <td><a href="https://leetcode.cn/u/<?= $row['username_LC'] ?>"><?= $row['leetCodeIntegral'] ?></a></td>
                    <td><?= $row['solveProblems'] ?></td>
                    <td><a href="javascript:post('https://lcpredictor.herokuapp.com/contest/<?= $leetCodeContest_Arr[0]['content'] ?>/ranking/search', {user: '<?= $row['username_LC'] ?>'})"><?= $row['leetCodeContest1'] ?></a></td>
                    <td><a href="javascript:post('https://lcpredictor.herokuapp.com/contest/<?= $leetCodeContest_Arr[1]['content'] ?>/ranking/search', {user: '<?= $row['username_LC'] ?>'})"><?= $row['leetCodeContest2'] ?></a></td>
                    <td><a href="https://codeforces.com/profile/<?= $row['username_CF'] ?>"><?= $row['codeForcesIntegralNOW'] ?></a></td>
                    <td><a href="https://codeforces.com/profile/<?= $row['username_CF'] ?>"><?= $row['codeForcesIntegralMAX'] ?></a></td>
                    <td><a href="">-</a></td>
                    <?php
                    echo '<td>' . date("Y-m-d H:i:s", $accountInfo['lastLoginTime']) . '</td>';
                    $online = $accountInfo['online'] == 1 ? "在线" : "离线";
                    echo '<td>' . $online . '</td>';
                    ?>

                    <td onselectstart="return false"><span id="<?= $row['username'] ?>" onclick="giveLike(this)" class="iconfont like"><?php
                                                                                                                                        if (isset($_SESSION['username'])) {
                                                                                                                                            /** 查询当前用户是否对该人进行点赞过 */
                                                                                                                                            $usernameTemp = $_SESSION['username'];
                                                                                                                                            $likeuser = $row['username'];
                                                                                                                                            $sql = "select * from todaylike where likeuser = '$likeuser' and username = '$usernameTemp'";
                                                                                                                                            $searchResult = mysqli_query($account, $sql);
                                                                                                                                            if ($searchResult && mysqli_num_rows($searchResult) == 0)
                                                                                                                                                /** 未查询到当前用户对他的点赞 */
                                                                                                                                                echo '&#xec7f;';
                                                                                                                                            else
                                                                                                                                                echo '&#xec8c;';
                                                                                                                                        } else
                                                                                                                                            echo '&#xec7f;';
                                                                                                                                        ?></span> <span id="like<?= $row['username'] ?>"><?= $accountInfo['like']; ?></span></td>
                </tr>
            <?php
            } ?>
        </tbody>
    </table>
    <div class="about">
        <h3>排行榜说明</h3>
        <ul>
            <li>默认以: <span style="text-decoration: underline;">力扣评分 > 做题数 > codeforces最高评分 > codeforces当前评分</span> 进行降序排序</li>
            <li>任一用户更新个人信息时[功能尚未完成] 和 每晚0时 将触发排行榜更新</li>
            <li>AcWing数据暂时未完成</li>
            <li>力扣相关数据以及codeforces数据均来源与用户个人主页</li>
            <li>力扣比赛数据来源于<a href="https://lcpredictor.herokuapp.com/">[力扣预测]</a></li>
            <li>每位用户每日可对多人进行一次点赞/取消</li>
        </ul>
    </div>
    <button id="retTop" title="返回"><img src="/style/image/retTop.png" alt="" id="img">
        <p id="font" style="display: none;">回到顶部</p>
    </button>
    <script src="script/ranking.js"></script>
    <script src="script/coco-message.js"></script>
    <script>
        cocoMessage.config({
            duration: 10000,
        });

        //页面滚动显示回到顶部按钮
        $(window).scroll(function() {
            if ($(window).scrollTop() >= 100) {
                $('#retTop').fadeIn(200);
                $('.person-head').css({
                    "background-color": "white"
                });
            } else {
                $('#retTop').fadeOut(200);
                $('.person-head').css({
                    "background-color": "transparent"
                });
            }
        });
        //点击按钮回到顶部
        $('#retTop').click(function() {
            $('html,body').animate({
                scrollTop: 0
            }, 500);
        });
        // 鼠标悬浮按钮之上，图片消失，文字显示
        $("#retTop").mouseover(function() {
            $("#img").hide();
            $("#font").show();
        })
        //鼠标离开，文字消失，图片显示。
        $("#retTop").mouseout(function() {
            $("#font").hide();
            $("#img").show();
        })
    </script>
    <script>
        function giveLike(obj) {
            //通过session检查是否登录
            let usernameData = <?php
                                if (isset($_SESSION['username']))
                                    echo '"' . $_SESSION['username'] . '"';
                                else
                                    echo '"not_login"' ?>,
                likeuserData = $(obj).attr('id');
            let userIdlike = document.getElementById(likeuserData),
                userLikeNumberId = document.getElementById('like' + likeuserData),
                userLikeNumber = Number(userLikeNumberId.innerHTML);
            $.ajax({
                type: "post",
                url: "/control/like.php",
                data: JSON.stringify({
                    mode: "user-like",
                    username: usernameData,
                    likeuser: likeuserData,
                }),
                dataType: "json",
                success: function(data) {
                    if (data.result == "未登录") {
                        cocoMessage.error("未登录！", 2000);
                    } else if (data.result == "点赞成功") {
                        userIdlike.innerHTML = "&#xec8c;";
                        userLikeNumberId.innerHTML = userLikeNumber + 1;
                    } else if (data.result == "点赞取消") {
                        userIdlike.innerHTML = "&#xec7f;";
                        userLikeNumberId.innerHTML = userLikeNumber - 1;
                    } else if (data.result == "参数错误")
                        userIdlike.innerHTML = "参数错误";
                },
                error: function(data) {
                    userIdlike.innerHTML = "连接服务器失败";
                }
            });
        }
    </script>
</body>

</html>