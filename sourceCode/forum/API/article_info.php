<?php
session_start();
header('Content-type: text/json');
include '../../control/connect.php';

$result = array(
    'success' => true,
    'message' => null,
    'article_like' => false,
    'collection_exst' => false
);

// 获取json数据
$urlJson = file_get_contents("php://input");
// 获取不到json
if (!$urlJson) {
    echo "{\"result\":\"参数错误1\"}";
    return;
}
$urlJson = json_decode($urlJson, true);
$article_id = $urlJson['article_id'];

//用户已登录，检查该用户对该评论是否点赞过
if (isset($_SESSION['username'])) {
    $LikeUserSQL = "SELECT * FROM article_describe WHERE article_id ='$article_id'";
    $LikeUser = mysqli_fetch_array(mysqli_query($forum, $LikeUserSQL))['like_user'];
    $like_user_list = explode(',', $LikeUser);
    if (in_array($_SESSION['username'], $like_user_list)) {
        echo "&#xec8c;";
    } else {
        echo "&#xec7f;";
    }
} else {
    $result['message'] = "&#xec7f;";
}

echo json_encode($result);
