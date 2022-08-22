<?php
header('Content-type: text/json');
include "../../control/connect.php";
// 获取json数据
$result = array('success' => false);

// session_start();
// if (!isset($_SESSION['username'])) {
//     $result['message'] = "请登录!";
//     echo json_encode($result);
//     return;
// }

$article = file_get_contents("php://input");
// 获取不到json
if (!$article) {
    $result['message'] = "参数错误";
    echo json_encode($result);
    return;
}
$articleJson = json_decode($article, true);
//获取的json对象参数个数不为2，且不包含键title和键content               count($articleJson) != 2 || 
if (!array_key_exists('username', $articleJson) || !array_key_exists('content', $articleJson)) {
    $result['message'] = "参数错误2";
    echo json_encode($result);
    return;
}
//获取的json对象参数为空
if (!$articleJson['username'] || !$articleJson['content']) {
    $result['message'] = "参数错误3";
    echo json_encode($result);
    return;
}

//获取毫秒时间戳
$time = explode(" ", microtime());
$time = ($time[1] + $time[0]) * 1000;
$time = round($time) . '';

$username = $articleJson['username'];
$content = $articleJson['content'];
$article_id = $articleJson['article_id'];

//移动图片到文章commentPictures文件夹下
$imageRegx = '/<img src="\/forum\/temp\/(.+\.[\w]+)(\S+.+)?" alt=".*"( title=".*")?>/';
$imageRegxSuccess = preg_match_all($imageRegx, $content, $inageArray, PREG_SET_ORDER);
if ($imageRegxSuccess) {
    $commentImgPath = "../articleImage/" . $username . "-" . $article_id . "/commentImg";
    if (!is_dir($commentImgPath)) {
        Mkdir($commentImgPath, 0777, true);
    }

    for ($i = 0; $i < count($inageArray); $i++) {
        //移动文件
        rename("../temp/" . $inageArray[$i][1], $commentImgPath . '/' . $inageArray[$i][1]);

        //将content内容区域的图片路径替换为对应文章的路径
        $nowpath = 'articleImage/' . $username . "-" . $article_id . "/commentImg";
        $inageArrayTemp = str_replace('temp', $nowpath, $inageArray[$i][0]);
        $content = str_replace($inageArray[$i][0], $inageArrayTemp, $content);
    }
}

//评论 还是 回复， 默认评论
$comment_or_reply = 0;

//评论
if ($articleJson['mode'] == "comment") {
    $comment_id = $time;
    $comment_time = time();

    $addCommentSQL = $forum->prepare("INSERT INTO comment(article_id,username,comment_id,comment_time,content,`like`,unlike,reply_number) VALUE('$article_id','$username','$comment_id','$comment_time',?,'0','0','0')");
    $addCommentSQL->bind_param("s", $content);
    $addCommentSQL->execute();


    $result['success'] = true;
    $result['message'] = "评论成功";
    $result['content'] = $content;
    $result['comment_id'] = $comment_id;
    $result['comment_time'] = $comment_time;
    echo json_encode($result);
}
//回复
else {
    $comment_or_reply = 1;
    $comment_id = $articleJson['comment_id'];
    $reply_user = $articleJson['reply_user'];
    $reply_id = $time;
    $reply_time = time();

    $addCommentSQL = "INSERT INTO reply(reply_id,comment_id,article_id,username,reply_time) VALUE('$reply_id','$comment_id','$article_id','$username','$reply_time')";
    mysqli_query($forum, $addCommentSQL);


    $addCommentSQL = $forum->prepare("UPDATE reply SET reply_user = '$reply_user', content  = ? WHERE reply_id='$reply_id'");
    if ($reply_user == '') {
        $addCommentSQL = $forum->prepare("UPDATE reply SET reply_user = 'none', content = ? WHERE reply_id='$reply_id'");
    }
    $addCommentSQL->bind_param("s", $content);
    $addCommentSQL->execute();

    $result = array('success' => true, 'message' => "回复成功", 'reply_time' => $reply_time);
    echo json_encode($result);

    //回复+1
    $commentSuccessSQL = "UPDATE comment SET reply_number = reply_number+1 WHERE comment_id = '$comment_id'";
    mysqli_query($forum, $commentSuccessSQL);
}

//评论+1
$time /= 1000;
$commentReplyAddSQL = "UPDATE article_describe SET comment = comment+1,latest_reply_time='$time' WHERE article_id = '$article_id'";

mysqli_query($forum, $commentReplyAddSQL);
