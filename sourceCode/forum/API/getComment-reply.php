<?php
session_start();
header('Content-type: text/json');
include "../../control/connect.php";

// 获取json数据
$comment = file_get_contents("php://input");
// 获取不到json
if (!$comment) {
    echo "{\"result\":\"参数错误1\"}";
    return;
}

$commentJson = json_decode($comment, true);

$commentSQL = "SELECT * FROM comment WHERE article_id = '" . $commentJson['article_id'] . "'";
$comments = mysqli_query($forum, $commentSQL);

$comments = mysqli_fetch_all($comments, MYSQLI_ASSOC);

$result['success'] = true;
if (count($comments) == 0) {
    $result['comments'] = [];
}
for ($i = 0; $i < count($comments); $i++) {
    $comment_id = $comments[$i]['comment_id'];
    $temp = array(
        'comment_id' => $comment_id,
        'username' => $comments[$i]['username'],
        'comment_time' => $comments[$i]['comment_time'],
        'content' => $comments[$i]['content'],
        'headImg' => null,
        'reply_number' => $comments[$i]['reply_number'],
        'like' => $comments[$i]['like'],
        'unlike' => $comments[$i]['unlike'],
        //0代表无点赞情况，1代表点赞，2代表踩
        'nowUserExistLike' => 0,
        'replys' => array()
    );
    //获取主评论用户头像
    $userHeadImgSQL = "SELECT * FROM user WHERE username = '" . $temp['username'] . "'";
    $userHeadImgResult = mysqli_fetch_assoc(mysqli_query($account, $userHeadImgSQL));
    $temp['headImg'] = $userHeadImgResult['headImg'];

    //该评论存在子评论
    if ($temp['reply_number'] != 0) {
        $replySQL = "SELECT * FROM reply WHERE comment_id = '$comment_id'";
        $replys = mysqli_query($forum, $replySQL);
        $replys = mysqli_fetch_all($replys, MYSQLI_ASSOC);

        //获取评论子回复用户头像
        for ($j = 0; $j < count($replys); $j++) {
            $replyUserHeadImgSQL = "SELECT * FROM user WHERE username = '" . $replys[$j]['username'] . "'";
            $replyUserHeadImgResult = mysqli_fetch_assoc(mysqli_query($account, $replyUserHeadImgSQL));
            $replys[$j]['headImg'] = $replyUserHeadImgResult['headImg'];
        }
        $temp['replys'] = $replys;
    }

    //用户已登录，检查该用户对该评论是否点赞过
    if (isset($_SESSION['username'])) {
        $commentSQL = "SELECT * FROM comment WHERE comment_id ='$comment_id'";
        $comment = mysqli_fetch_array(mysqli_query($forum, $commentSQL));
        $likeUser = $comment['like_user'];
        $unlikeUser = $comment['unlike_user'];
        $like_user_list = explode(',', $likeUser);
        $unlike_user_list = explode(',', $unlikeUser);
        if (in_array($_SESSION['username'], $like_user_list)) {
            $temp['nowUserExistLike'] = 1;
        } else if (in_array($_SESSION['username'], $unlike_user_list)) {
            $temp['nowUserExistLike'] = 2;
        }
    }

    $result['comments'][] = $temp;
}

echo json_encode($result);
