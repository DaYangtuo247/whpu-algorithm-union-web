<?php
session_start();
include '../control/connect.php';
$url = $_SERVER['REQUEST_URI'];
$url = mb_substr($url, stripos($url, "?") + 1); //截取问好后的参数
parse_str($url, $articleArr); //将参数转换为关联数组
$article_id = $articleArr['id'];
//获取文章主题内容
$articleSQL = "SELECT * FROM article WHERE article_id = '$article_id'";
$articleResult = mysqli_query($forum, $articleSQL);
if ($articleResult && mysqli_num_rows($articleResult) == 0)
    header("Location: /error/error.html");
$article = mysqli_fetch_assoc($articleResult);

// 浏览量检测逻辑：访问某一页面，创建session，并存储文章id，如果在该session存在周期内，session数组中不存在该文章id，文章id存入数组，访问量+1，反之无操作
if (isset($_SESSION['pageView'])) {
    if (!in_array($article_id, $_SESSION['pageView'])) {
        $_SESSION['pageView'][]  = $article_id;
        $addPageViewSQL = "UPDATE article_describe SET page_view=page_view+1 WHERE article_id = '$article_id'";
        mysqli_query($forum, $addPageViewSQL);
    }
} else {
    $_SESSION['pageView'] = [];
}

//获取文章描述
$article_describeSQL = "SELECT * FROM article_describe WHERE article_id = '$article_id'";
$article_describe = mysqli_fetch_assoc(mysqli_query($forum, $article_describeSQL));
//获取作者头像
$usernameSQL = "SELECT * FROM user WHERE username = '" . $article_describe['username'] . "'";
$article_describe['headImg'] = mysqli_fetch_assoc(mysqli_query($account, $usernameSQL))['headImg'];


?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/style/image/favicon.ico">
    <link rel="stylesheet" href="/style/css/general.css">
    <link rel="stylesheet" href="/style/css/article.css">
    <link rel="stylesheet" href="/style/css/header.css">
    <link rel="stylesheet" href="editormd/css/editormd.css" />
    <link rel="stylesheet" href="/forum/reply-editor/css/jquery.mCustomScrollbar.min.css" />
    <link rel="stylesheet" href="/forum/reply-editor/css/jquery.emoji.css" />
    <title><?= $article_describe['title'] ?> - WHPU论坛 </title>
</head>

<body>
    <script src="/script/jQuery.min.js"></script>
    <?php
    include '../views/nav.html';
    include '../views/header.php';
    ?>
    <div class="show">
        <div class="article-show">
            <div class="module">
                <div class="author">
                    <img src="<?= $article_describe['headImg'] ?>" alt="">
                    <p class="username"><?= $article_describe['username'] ?></p>
                    <ul>
                        <li>
                            <p class="iconfont">&#xe635;</p>
                            <p>文章</p>
                        </li>
                        <li>
                            <p class="iconfont">&#xe8ba;</p>
                            <p>收藏</p>
                        </li>
                        <li>
                            <p class="iconfont">&#xe744;</p>
                            <p>评论</p>
                        </li>
                        <li>
                            <p class="iconfont">&#xe761;</p>
                            <p>获赞</p>
                        </li>
                    </ul>
                </div>
                <!-- 目录容器 -->
                <div class="article-toc">
                    <h3>目录</h3>
                    <div class="article-toc-content"></div>
                </div>
            </div>
            <!-- 数据库md文件容器 -->
            <div id="article-content">
                <div class="editArticle iconfont" title="编辑">&#xe602;</div>
                <h1 class='article-title'>
                    <p><?= $article_describe['title'] ?></p>
                    <ul class='articleinfo iconfont'>
                        <li><span>&#xe640;</span> <?= $article_describe['tag'] ?></li>
                        <li class="createTime">&#xe63f; 发布于 <span><?= $article_describe['create_time'] ?></span></li>
                        <li id='exist-modify'><a>&#xe666; 有改动</a>
                            <div>修改于 <span class="amend"><?= $article_describe['last_change_time'] ?></span><span></span></div>
                        </li>
                        <li>&#xe6ee; <?= $article_describe['page_view'] ?> 阅读 </li>
                        <li class="wordsNumber">&#xe6ee; <span></span> 字数</li>
                    </ul>
                </h1>
                <textarea style="display:none;"><?= $article['content'] ?></textarea>
            </div>
            <div class="comment-reply">
                <h2 class="comment-reply-title"><span class="comment-count"><?= $article_describe['comment'] ?></span> 条评论</h2>
                <a name="comment-anchor"></a>
                <a href="#scroll-comment-editor" class="scroll-comment-editor"><span class="iconfont">&#xe90a;</span>我要评论</a>
                <ul class="comments"></ul>
                <div class="comment-editor-area">
                    <a name="scroll-comment-editor"></a>
                    <div id="add-comment"></div>
                    <input type="submit" value="发表评论" id="comment-editor-submit">
                </div>
            </div>
        </div>
        <div class="aside">
            <?php
            //检查文章收藏点赞情况
            $LikeUserSQL = "SELECT * FROM article_describe WHERE article_id ='$article_id'";
            $LikeUser = mysqli_fetch_array(mysqli_query($forum, $LikeUserSQL));
            ?>
            <ul class="aside-module iconfont">
                <li class="article-like" title="点赞">
                    <p><?php
                        //用户已登录，检查该用户对该评论是否点赞过
                        $exist_like = false;
                        if (isset($_SESSION['username'])) {
                            $like_user_list = explode(',', $LikeUser['like_user']);
                            //该用户点赞过
                            if (in_array($_SESSION['username'], $like_user_list)) {
                                $exist_like = true;
                            }
                        }
                        echo ($exist_like) ? "&#xec8c;" : "&#xec7f;";
                        ?></p>
                    <div class="like-count"><?= $article_describe['like'] ?></div>
                </li>
                <li class="collection-exist" title="收藏">
                    <p><?php
                        $exist_like = false;
                        if (isset($_SESSION['username'])) {
                            $like_user_list = explode(',', $LikeUser['collection_user']);
                            //该用户点赞过
                            if (in_array($_SESSION['username'], $like_user_list)) {
                                $exist_like = true;
                            }
                        }
                        echo ($exist_like) ? "&#xe8c6;" : "&#xe8ba;";
                        ?></p>
                    <div class="collection-count"><?= $article_describe['collection'] ?></div>
                </li>
                <li title="评论区">
                    <a href="#comment-anchor">
                        <div class="comment-count"><?= $article_describe['comment'] ?></div>
                        <p>&#xe744;</p>
                    </a>
                </li>
                <li onclick="copyUrl()" class="copyUrl" title="分享">
                    <p>&#xe632;</p>
                    <div class="qrcode"><span></span></div>
                </li>
                <li id="retTop" title="到页首">
                    <p>&#xe605;</p>
                </li>
                <li id="retBottom" title="到页尾">
                    <p style="transform: rotate(180deg);">&#xe605;</p>
                </li>
            </ul>
        </div>
    </div>

    <script src="/script/jquery.qrcode.min.js"></script>
    <script src="editormd/editormd.js"></script>
    <script src="editormd/lib/flowchart.min.js"></script>
    <script src="editormd/lib/jquery.flowchart.min.js"></script>
    <script src="editormd/lib/marked.min.js"></script>
    <script src="editormd/lib/prettify.min.js"></script>
    <script src="editormd/lib/raphael.min.js"></script>
    <script src="editormd/lib/underscore.min.js"></script>
    <script src="editormd/lib/sequence-diagram.min.js"></script>

    <script src="/script/moment.min.js"></script>
    <script src="/script/moment-zh_cn.min.js"></script>
    <script src="/script/coco-message.js"></script>

    <script src="/forum/reply-editor/script/jquery.mousewheel-3.0.6.min.js"></script>
    <script src="/forum/reply-editor/script/jquery.mCustomScrollbar.min.js"></script>
    <script src="/forum/reply-editor/script/jquery.emoji.js"></script>
    <script src="/script/timeFormat.js"></script>

    <script src="/script/header.js"></script>
    <script>
        let username = window.localStorage.getItem('account');
        //检查是否登录，并且登录用户是否是文章作者
        if (username == null || username != $('.author .username').text()) {
            $('.editArticle').remove();
        }
        $('.editArticle').on('click', function() {
            location.href = 'create-edit.php?id=<?= $article_id ?>';
        });

        //修改header模板标题
        $(function() {
            $('#site-title').text("<?= $article_describe['title'] ?>");
            //返回上一页
            $(".header .show").prepend('<a onclick="window.history.go(-1)" class="iconfont retprev">&#xe669;</a>');
            //生成网页二维码
            $('.qrcode').qrcode({
                width: 120,
                height: 120,
                text: location.href
            });
        });

        //文章及目录渲染
        $(function() {
            editormd.markdownToHTML("article-content", {
                path: "editormd/lib/",
                htmlDecode: false, //不解析style和script语句，使用"style,script"
                emoji: true,
                taskList: true,
                tex: true, // 默认不解析
                flowChart: true, // 默认不解析
                sequenceDiagram: true, // 默认不解析
                codeFold: true,
                tocContainer: '.article-toc-content', //指定目录容器的id
            });

            //添加文章信息
            let wordsNumber = $('#article-content').text().length - $('.article-title').text().length - 30;

            $('.createTime span').text(moment.unix($('.createTime span').text()).format('YYYY-MM-DD HH:mm:ss'));
            $(".wordsNumber span").text(wordsNumber);

            if ($('#exist-modify div span.amend').text() != 0) {
                $('#exist-modify div span.amend').text(moment.unix($('#exist-modify div span.amend').text()).format('YYYY-MM-DD HH:mm:ss'));
            } else {
                $('#exist-modify').remove();
            }

            //检测是否创建目录
            let exist_toc_obj = $('.article-toc .markdown-toc-list');
            if (exist_toc_obj.text() == '') {
                exist_toc_obj.append("<li style='text-align:center;border:0px;margin-left:0px'>无</li>");
            }
        });

        //评论获取
        function getCommentReply() {
            let result = $("<ul></ul>");
            let comment_count = 0; //评论+回复数量
            $.ajax({
                type: "post",
                url: "API/getComment-reply.php",
                data: JSON.stringify({
                    page: 1,
                    article_id: "<?= $article_id ?>"
                }),
                dataType: "json",
                async: false,
                success: function(data) {
                    if (data.success == true) {
                        comment_count += data['comments'].length;
                        for (let i = 0; i < data['comments'].length; i++) {
                            let comment = data['comments'][i];

                            //存在回复
                            let replys = "";
                            if (comment['reply_number'] != 0) {
                                comment_count += comment['replys'].length;
                                for (let j = 0; j < comment['replys'].length; j++) {
                                    let reply = comment['replys'][j];
                                    let exist_reply_user = reply['reply_user'];
                                    if (exist_reply_user === 'none') {
                                        exist_reply_user = "";
                                    } else {
                                        exist_reply_user = "<span> 回复 </span><a href='/userinfo/userinfo.php?username=" + reply['reply_user'] + "'>" + reply['reply_user'] + "</a>";
                                    }
                                    replys += '<li class="reply"><img class="headImg" src="' + reply['headImg'] + '"><div class="reply-data">' +
                                        '<h4 class="reply-data-user"><a href="/userinfo/userinfo.php?username=' + reply['username'] + '">' + reply['username'] + '</a>' + exist_reply_user + '<span class="reply-data-time">' + getDateTimeFormatComment(reply['reply_time']) + '</span></h4>' +
                                        '<div class="reply-data-content">' + reply['content'] + '</div>' +
                                        '<div class="comment-reply-submit"  userdata=\'{\"username\":\"' + reply['username'] + '\"}\'><span class="iconfont">&#xe634;</span><span>回复</span></div>' +
                                        '</div></li>';
                                }
                            }
                            result.append(
                                `<li class="comment" commentid="${comment.comment_id}"><img class="headimg" src="${comment.headImg}"><div class="comment-data">` +
                                `<h3 class="comment-data-user"><a href="/userinfo/userinfo.php?username=${comment.username}">${comment.username}</a><span class="comment-data-time">${getDateTimeFormatComment(comment.comment_time)}</span></h3>` +
                                `<div class="comment-data-content markdown-body editormd-html-preview">${comment.content}</div>` +
                                '<ul class="comment-data-module">' +
                                `<li class="comment-like"><span class="iconfont">&#xec7f;</span>点赞(<span class="comment-like-count">${comment.like}</span>)</li>` +
                                `<li class="comment-unlike"><span class="comment-unlike-reverse iconfont">&#xec7f;</span>踩(<span class="comment-unlike-count">${comment.unlike}</span>)</li>` +
                                `<li class="comment-reply-submit"><span class="iconfont">&#xe634;</span>回复(<span class="replys-count">${comment.reply_number}</span>)</li></ul></div>` +
                                `<div class='reply-area'><ul class='replys'>${replys}</ul></div></li>`);

                            let nowCommentObj = result.find(`.comment[commentid=${comment['comment_id']}]`);
                            //不存在评论回复，删除该评论回复区域
                            if (comment['reply_number'] == 0) {
                                nowCommentObj.find('.reply-area').remove();
                            }

                            //当前用户对某评论进行 [点赞 or 踩] 操作
                            if (comment.nowUserExistLike == 1) {
                                nowCommentObj.find('.comment-like').addClass('active');
                                nowCommentObj.find('.comment-unlike').removeClass('active');
                                nowCommentObj.find('.comment-like span[class~="iconfont"]').text('');
                            } else if (comment.nowUserExistLike == 2) {
                                nowCommentObj.find('.comment-like').removeClass('active');
                                nowCommentObj.find('.comment-unlike').addClass('active');
                                nowCommentObj.find('.comment-unlike span[class~="iconfont"]').text('');
                            }
                        }
                        $('.comment-count').html(comment_count); //更新评论数量
                    } else {
                        cocoMessage.error(data.message, 3000);
                    }
                },
                error: function(data) {
                    cocoMessage.error("评论获取失败，请联系管理员", 3000);
                }
            });

            if (result.html() == '') {
                result.html('<img src="/style/image/no-content.png" alt="" style="display: block;margin: 0px auto;width: 500px;margin-bottom: 15px;">');
            }
            return result.html();
        }

        //评论以及回复
        $(function() {
            //初始化评论区
            $('.comments').html(getCommentReply());
            //评论编辑框
            var editor = editormd("add-comment", {
                width: "100%",
                height: "550px",
                path: "editormd/lib/",
                emoji: true,
                taskList: true,
                codeFold: true, //代码折叠
                tex: true, //开启latex公式支持
                flowChart: true, // 开启流程图支持，默认关闭
                sequenceDiagram: true, // 开启时序/序列图支持，默认关闭,
                delay: 0,
                lineNumbers: false, //行号关闭
                saveHTMLToTextarea: true, // 保存 HTML 到 Textarea
                toolbarAutoFixed: false, //工具栏自动固定定位的开启与禁用
                watch: false,
                syncScrolling: "single",
                placeholder: "把你想对博主说的内容写下来吧！\n\n支持markdown、latex语法、时序图、流程图等\n\n更多内容见工具栏使用教程",
                imageUpload: true, //开启图片上传
                crossDomainUpload: false, //禁止跨域上传
                imageFormats: ["jpg", "jpeg", "gif", "png"],
                imageUploadURL: "/forum/API/upPictures.php",

                dialogLockScreen: false, // 设置弹出层对话框不锁屏，不设置该项目，将导致sticky被隐藏
                dialogMaskOpacity: 0.2, // 设置透明遮罩层的透明度，全局通用，默认值为0.1
                dialogMaskBgColor: "#000", // 设置透明遮罩层的背景颜色，全局通用，默认为#fff

                //自定义工具栏
                toolbarIcons: function() {
                    return ["undo", "redo", "|", "bold", "del", "italic", "quote", "|", "list-ul", "list-ol", "hr", "|", "link", "image", "code", "code-block", "table", "datetime", "emoji", "|", "watch", "preview", "fullscreen", "|", "help"];
                },
                //点击全屏按钮，取消圆角边框
                onfullscreen: function() {
                    this.editor.css("border-radius", 0).css("z-index", 120);
                },
                //退出全屏按钮，开启圆角边框
                onfullscreenExit: function() {
                    this.editor.css({
                        zIndex: 10,
                        "border-radius": "15px"
                    });
                    this.resize();
                }
            });

            //未登录，删除评论编辑框
            if (username == null) {
                $('.comment-editor-area').remove();
            }
            //评论提交
            $("#comment-editor-submit").click(function() {
                if (editor.getMarkdown() == '') {
                    cocoMessage.error("请填写评论内容！", 3000);
                    return;
                }
                $.ajax({
                    type: "post",
                    url: "API/addComment-reply.php",
                    data: JSON.stringify({
                        mode: "comment",
                        username: username,
                        article_id: "<?= $article_id ?>",
                        content: editor.getHTML(),
                    }),
                    dataType: "json",
                    success: function(data) {
                        if (data.success == true) {
                            cocoMessage.success("评论成功", 3000);
                            $('.comments').html(getCommentReply());
                            editor.clear();
                        } else
                            cocoMessage.error(data.message, 3000);
                    },
                    error: function(data) {
                        cocoMessage.error("服务器出现问题，请联系管理员", 3000);
                    }
                });
            });

            //点击回复按钮显示回复区域
            $(".comment-reply").on('click', '.comment-reply-submit', function() {
                if (username == null) {
                    cocoMessage.error("请先登录", 3000);
                    return;
                }
                let comment = $(this).parents('.comment');
                //获取当前点击元素是否存在回复对象
                let userdata = $(this).attr('userdata');
                let replyUser = "";
                if (userdata != undefined) {
                    userdata = jQuery.parseJSON(userdata);
                    replyUser = '<span contenteditable="false" reply_user="' + userdata['username'] + '">回复 ' + userdata['username'] + ' :</span> ';
                }

                //不存在回复区域,添加评论回复区域
                if (comment.children('.reply-area').length === 0) {
                    comment.append("<div class='reply-area'><ul class='replys'></ul></div>");
                }

                //上一个评论回复区域不存在回复
                if ($('.reply-module').siblings('.replys').children('li').length === 0) {
                    $('.reply-module').parents('.reply-area').remove();
                }

                //检测当前所在评论是否存在回复框
                let nowObjExistCommentEditor = false;
                if (comment.find('.reply-module').length != 0)
                    nowObjExistCommentEditor = true;
                //当前评论不存在回复框
                if (!nowObjExistCommentEditor) {
                    //删除上一个评论回复模块
                    $('.reply-module').remove();

                    let obj = comment.children('.reply-area');
                    obj.append(
                        '<div class="reply-module" style="display:none;">' +
                        '<div class="reply-editor" contenteditable="true">' + replyUser + '</div>' +
                        '<ul class="reply-extension">' +
                        '<li class="reply-module-emoji iconfont"><a>&#xe612;</a></li>' +
                        '<li class="reply-module-at"><a>@</a></li>' +
                        '<li class="reply-submit"><a>回复</a></li>' +
                        '</ul></div>'
                    );
                    obj.children('.reply-module').slideDown(600);
                } else {
                    comment.find('.reply-editor').html(replyUser);
                }
                //滚动到回复编辑框且获取焦点
                $('html, body').animate({
                    scrollTop: $('.reply-module .reply-editor').offset().top - ($(window).height() / 2)
                }, 400);
                $('.reply-module .reply-editor').focus();

                //留言编辑框
                $(".reply-editor").emoji({
                    showTab: true,
                    button: ".reply-module-emoji",
                    animation: 'slide',
                    position: 'topRight',
                    icons: [{
                        name: "贴吧表情",
                        path: "/forum/reply-editor/img/tieba/",
                        maxNum: 50,
                        file: ".jpg",
                        placeholder: ":{alias}:"
                    }, {
                        name: "QQ表情",
                        path: "/forum/reply-editor/img/qq/",
                        maxNum: 91,
                        file: ".gif",
                        placeholder: "#qq_{alias}#"
                    }, {
                        name: "原生emoji",
                        path: "/forum/reply-editor/img/emoji/",
                        maxNum: 84,
                        file: ".png",
                        placeholder: "#emoji_{alias}#"
                    }]
                });
            });

            //回复编辑完成提交
            $(".comment-reply").on('click', '.reply-submit', function() {
                //获取文本编辑框
                let editorText = $(this).parents('.reply-module').children('.reply-editor');
                let exist_reply_user = editorText.children('span[reply_user]').attr('reply_user');

                //获取编辑框内容，提取包括编辑框在内的含有html标签，转换为jquery对象
                let content = $(editorText.prop("outerHTML"));
                //将该对象下的回复对象剔除，得到得即是结果文本
                content.children().remove('span[reply_user]');

                let comment_id = $(this).parents('.comment').attr('commentid'),
                    reply_user = "";
                if (exist_reply_user !== undefined) {
                    reply_user = exist_reply_user;
                }
                $.ajax({
                    type: "post",
                    url: "API/addComment-reply.php",
                    data: JSON.stringify({
                        mode: "addreply",
                        username: username,
                        article_id: "<?= $article_id ?>",
                        comment_id: comment_id,
                        reply_user: reply_user,
                        content: content.html(),
                    }),
                    dataType: "json",
                    success: function(data) {
                        if (data.success == true) {
                            cocoMessage.success("回复成功", 3000);
                            $('.comments').html(getCommentReply());
                        } else
                            cocoMessage.error(data.message, 3000);
                    },
                    error: function(data) {
                        cocoMessage.error("服务器出现问题，请联系管理员", 3000);
                    }
                });
            });

            //文章点赞和评论点赞，以及收收藏
            $(".aside-module, .comments").on('click', '.article-like, .comment-like, .comment-unlike, .collection-exist', function() {
                if (username == null) {
                    cocoMessage.error("请先登录", 3000);
                    return;
                }
                let nowObj = $(this);
                console.log(nowObj.prop('outerHTML'));
                $.ajax({
                    type: "post",
                    url: "/control/like.php",
                    data: JSON.stringify({
                        //千万注意，getCommentReply()会给当前登录用户点赞的文章添加active，那这里获取得到的clas就不在是原先的类名了
                        mode: nowObj.attr('class').split(" ")[0],
                        username: username,
                        article_id: "<?= $article_id ?>",
                        comment_id: $(this).parents('.comment').attr('commentid')
                    }),
                    dataType: "json",
                    success: function(data) {
                        if (data.success == true) {
                            let nowObjCount = nowObj.children('div[class$="count"]');
                            //文章点赞
                            if (nowObj.attr('class') == 'article-like') {
                                if (nowObj.children('p').text() == '') {
                                    nowObj.children('p').text('');
                                    nowObjCount.text(Number(nowObjCount.text()) + 1);
                                } else {
                                    nowObj.children('p').text('');
                                    nowObjCount.text(Number(nowObjCount.text()) - 1);
                                }
                            }
                            //文章收藏
                            else if (nowObj.attr('class') == 'collection-exist') {
                                if (nowObj.children('p').text() == '') {
                                    nowObj.children('p').text('');
                                    nowObjCount.text(Number(nowObjCount.text()) + 1);
                                } else {
                                    nowObj.children('p').text('');
                                    nowObjCount.text(Number(nowObjCount.text()) - 1);
                                }
                            }
                            //评论点赞
                            else {
                                $('.comments').html(getCommentReply());
                                console.log("true");
                            }
                        } else
                            cocoMessage.error(data.message, 3000);
                    },
                    error: function(data) {
                        cocoMessage.error("服务器出现问题，请联系管理员", 3000);
                    }
                });
            });
        });
    </script>
    <script src="/script/article.js"></script>
</body>

</html>