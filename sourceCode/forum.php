<?php
session_start();
include 'control/connect.php';
?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/css/general.css">
    <link rel="stylesheet" href="style/css/forum.css">
    <link rel="stylesheet" href="style/css/header.css">
    <link rel="shortcut icon" href="style/image/favicon.ico">
    <title>论坛主页</title>
</head>

<body>
    <script src="/script/jQuery.min.js"></script>
    <?php include 'views/nav.html' ?>
    <?php include 'views/header.php' ?>
    <div class="forum-show">
        <div class="main">
            <div class="left">
                <a href="forum/create-edit.php" class="publish iconfont" onclick="return online()"><span>&#xe628;</span> 发布帖子</a>
                <div class="tags">
                    <ul class="clearfix">
                        <li onclick="getTag('all')">所有帖子</li>
                        <?php
                        $tagsSQL = "SELECT * FROM tags st ORDER BY st.tag_number DESC";
                        $tags = mysqli_query($forum, $tagsSQL);
                        while ($row = mysqli_fetch_assoc($tags)) {
                        ?>
                            <li onclick="getTag('<?= $row['tag_name'] ?>')"><?= $row['tag_name'] . " " . $row['tag_number']; ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="right">
                <div class="tools">
                    <button class="toolbar" onclick="btnTools(this)"><span class="toolbarName">最新回复</span><span class="iconfont">&#xe616;</span></button>
                    <ul class="toolbar-view">
                        <li class="iconfont iconActive" onclick="getTopic('latest_reply', this)"><span>&#xe63e;</span>最新回复</li>
                        <li class="iconfont" onclick="getTopic('hot_topic', this)"><span>&#xe63e;</span>热门帖子</li>
                        <li class="iconfont" onclick="getTopic('recent_posts', this)"><span>&#xe63e;</span>近期帖子</li>
                        <li class="iconfont" onclick="getTopic('history_post', this)"><span>&#xe63e;</span>历史帖子</li>
                    </ul>
                    <button class="refresh iconfont" id="refresh" onclick="getTagArticle()">&#xe61b;</button>
                </div>
                <div class="loadings" onselectstart="return false">
                    <div class="loading"></div>
                </div>
                <div class="articles-back">
                    <ul id="articles"></ul>
                    <div class="pagination"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="/script/coco-message.js"></script>
    <script src="/script/header.js"></script>
    <script src="/script/moment.min.js"></script>
    <script src="/script/moment-zh_cn.min.js"></script>
    <script src="/script/forum.js"></script>
    <script src="/script/timeFormat.js"></script>
    <script>
        function online() {
            let username = window.localStorage.getItem('account');
            if (username == null) {
                cocoMessage.error('未登录！', 2000);
                return false;
            }
            return true;
        }

        //修改header模板标题
        $(function() {
            $('#site-title').text("WHPU Forum");
        });

        //点击显示菜单区域
        function btnTools(obj) {
            $(obj).addClass('toolEnterBtn');
            $('.toolbar-view').fadeIn(200);
        }

        //点击非下拉菜单区域隐藏下拉菜单
        $('html').on('click', function(e) {
            if (!$(e.target).closest('.toolbar').length > 0 && !$(e.target).closest('.toolbar-view').length > 0) {
                $('.toolbar-view').fadeOut(200);
                $('.toolEnterBtn').removeClass('toolEnterBtn');
            }
        });

        //ajax请求某页帖子
        $(function() {
            let url = window.location.href,
                pageRegx = /#page=(\d+)$/,
                result = pageRegx.exec(url);
            let page = result == null ? 1 : result[1];
            getTagArticle(page);
        });

        //当前请求标签 和 请求操作
        var nowTagName = 'all',
            nowClassifyName = 'latest_reply';

        //获取标签文章
        function getTag(tagName) {
            nowTagName = tagName;
            getTagArticle();

        }

        //获取用户分类选项
        function getTopic(topic, obj) {
            nowClassifyName = topic;
            let topicArray = {
                'latest_reply': '最新回复',
                'hot_topic': '热门帖子',
                'recent_posts': '近期帖子',
                'history_post': '历史帖子'
            };
            $('.toolbarName').text(topicArray[topic]);
            //去除原来的iconActive
            $('.iconActive').removeClass('iconActive');
            //对当前点击的li元素添加类名iconActive
            $(obj).addClass('iconActive');
            //隐藏下拉菜单
            $('.toolbar-view').hide();
            $('.toolEnterBtn').removeClass('toolEnterBtn');
            //获取文章
            getTagArticle();
        }

        //获取具有tag和用户选项的文章
        function getTagArticle(nowpage = 1) {
            $('#articles').empty();
            $.ajax({
                type: "post",
                url: "/forum/API/getArticle_describe.php",
                data: JSON.stringify({
                    page: nowpage,
                    tag: nowTagName,
                    classify: nowClassifyName
                }),
                dataType: "json",
                contentType: "application/json; charset=utf-8",
                //ajax请求前loading
                beforeSend: function() {
                    $('.pagination').hide();
                    $('.loadings').show();
                },
                //ajax请求完成后
                complete: function() {
                    $('.pagination').show();
                    $('.loadings').hide();
                },
                success: function(data) {
                    for (let i = 0; i < data['articles'].length; i++) {
                        let dataTemp = data['articles'][i];
                        let article = '<li class="ArticleInfo"><div class="tag" onclick="getTag(\'' + dataTemp.tag + '\')">' + dataTemp.tag + '</div><a class="userinfo" href="/userinfo/userinfo.php?username=' + dataTemp.username + '">';
                        article += '<img src="' + dataTemp.headImg + '" alt=""></a><a class="article-describe" href="/forum/article.php?id=' + dataTemp.article_id + '">';
                        article += '<h3 class="title">' + dataTemp.title + '</h3>';
                        article += '<p class="describe">' + dataTemp.describe + '</p></a>';
                        article += '<ul class="rests iconfont"><li>发表于 ' + getDateTimeFormat(dataTemp.create_time) + '</li><li>&#xe744; ' + dataTemp.comment + '</li><li>&#xe6ee; ' + dataTemp.page_view + '</li></ul></li>';
                        $('#articles').append(article);
                        $('.articles-back').fadeIn(300);
                    }
                    //限定页数在范围内，防止用户手动输入
                    if (nowpage <= 0)
                        nowpage = 1;
                    else if (nowpage > data.pageNumber)
                        nowpage = data.pageNumber;

                    //分页
                    createPagination(data.pageNumber, nowpage, false);

                    //让body,.main高度为页面高度,为了保证sticky的有效
                    $('body,.main').css({
                        "height": $('.main .right').css('height')
                    });
                },
                error: function(data) {
                    $('#articles').text("<p style='text-align:center;'>获取文章失败，请联系管理员！</p>");
                }
            });
        }
    </script>
</body>

</html>