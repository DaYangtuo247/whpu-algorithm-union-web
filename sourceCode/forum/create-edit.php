<?php
//检测到未登录
session_start();

if (!isset($_SESSION['username'])) {
    echo '<script>
    alert("请登录!!!")
    location.href = "/forum.php";
    </script>';
} else {
    include '../control/connect.php';
    $url = $_SERVER['REQUEST_URI'];
    $url = mb_substr($url, stripos($url, "?") + 1); //截取问好后的参数
    parse_str($url, $articleInfo); //将参数转换为关联数组

    $editPower = false;
    if (!empty($articleInfo) && array_key_exists('id', $articleInfo)) {
        $article_id = $articleInfo['id'];
        $requestArticleSQL = "SELECT * FROM article_describe WHERE article_id = '$article_id'";

        $article_describe = mysqli_fetch_array(mysqli_query($forum, $requestArticleSQL), MYSQLI_ASSOC);
        if (empty($article_describe)) {
            echo '{"result":"查询不到文章!"}';
            return;
        }

        if ($article_describe['username'] != $_SESSION['username']) {
            echo '{"result":"你不是该文章作者！"}';
            return;
        }
        $editPower = true;
        $articleContentSQL = "SELECT * FROM article WHERE article_id = '$article_id'";
        $article = mysqli_fetch_array(mysqli_query($forum, $articleContentSQL), MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <title><?php if ($editPower) echo "编辑帖子";
            else echo "发布帖子"; ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/css/general.css">
    <link rel="stylesheet" href="editormd/css/editormd.css" />
    <link rel="stylesheet" href="/style/css/create.css">
</head>

<body>
    <div class="show">
        <h1 class="site-title"><?php if ($editPower) echo "编辑帖子";
                                else echo "发布帖子"; ?></h1>
        <div class="edit">
            <div class="head">
                <input type="text" id="tag-title" placeholder="标签" maxlength="10">
                <input type="color" style="vertical-align: middle;">
                <input type="text" id="title" placeholder="标题" maxlength="44" value="<?php if ($editPower) echo $article_describe['title']; ?>">
            </div>
            <form id="form" method="post">
                <div id="test-editor">
                    <textarea style="display:none;" name="content"><?php if ($editPower) echo $article['content'] ?></textarea>
                </div>
            </form>
            <button id="retForm" onclick="location.href='<?php if ($editPower) echo "/forum/article.php?id=" . $article_id;
                                                            else echo "/forum.php"; ?>'">取消</button>
            <button id="submit"><?php if ($editPower) echo "确认修改";
                                else echo "发布"; ?></button>
        </div>
    </div>

    <script src="/script/jQuery.min.js"></script>
    <script src="editormd/editormd.js"></script>
    <script src="/script/coco-message.js"></script>
    <script src="/script/create.js"></script>
    <script type="text/javascript">
        $(function() {
            var editor = editormd("test-editor", {
                width: "100%",
                height: "600px",
                path: "editormd/lib/",
                emoji: true,
                taskList: true,
                codeFold: true, //代码折叠
                tex: true, //开启latex公式支持
                flowChart: true, // 开启流程图支持，默认关闭
                sequenceDiagram: true, // 开启时序/序列图支持，默认关闭,
                delay: 0,
                toc: false, //关闭目录支持
                // saveHTMLToTextarea: true, // 保存 HTML 到 Textarea
                toolbarAutoFixed: false, //工具栏自动固定定位的开启与禁用
                syncScrolling: "single",
                placeholder: "\n\n支持markdown、latex语法、时序图、流程图等\n\n允许拖拽上传图片 和 粘贴上传图片（限定一次最多10张）\n其中每张图片大小限定在10MB以内\n\n更多内容见工具栏使用教程",
                imageUpload: true, //开启图片上传
                crossDomainUpload: false, //禁止跨域上传
                imageFormats: ["jpg", "jpeg", "gif", "png"],
                imageUploadURL: "/forum/API/upPictures.php",
                //自定义工具栏
                toolbarIcons: function() {
                    return ["undo", "redo", "|", "bold", "del", "italic", "quote", "ucwords", "uppercase", "lowercase", "|", "h1", "h2", "h3", "|", "list-ul", "list-ol", "hr", "|", "link", "image", "code", "code-block", "table", "datetime", "emoji", "|", "watch", "preview", "fullscreen", "|", "help"];
                },
                //点击全屏按钮，取消圆角边框
                onfullscreen: function() {
                    this.editor.css("border-radius", 0).css("z-index", 120);
                },
                //退出全屏按钮，开启圆角边框
                onfullscreenExit: function() {
                    this.editor.css({
                        zIndex: 10,
                        "border-radius": "12px"
                    });
                    this.resize();
                },
                onload: function() {
                    initPasteDragImg(this); //支持复制，粘贴，拖拽上传图片
                }
            });

            //发布帖子
            $("#submit").click(function() {
                if ($('#title').val() == '' || editor.getMarkdown() == '') {
                    cocoMessage.error("请填写标题和内容！", 3000);
                    return;
                }
                let tag = $('#tag-title').val();
                if (tag == '')
                    tag = "其他";
                $.ajax({
                    type: "post",
                    url: "API/createArt.php",
                    data: JSON.stringify({
                        mode: "<?php if ($editPower) echo 'edit';
                                else echo 'create' ?>",
                        article_id: "<?php if ($editPower) echo $article_id; ?>",
                        tag: tag,
                        title: $('#title').val(),
                        content: editor.getMarkdown(),
                    }),
                    dataType: "json",
                    success: function(data) {
                        if (data.success == true) {
                            cocoMessage.success(`${data.message}, 3秒后跳转到文章`, 3000);
                            setTimeout("location.href='/forum/article.php?id=" + data.article_id + "';", 3000);
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
</body>

</html>