<?php
header('Content-type: text/json');
include "../../control/connect.php";
//每页显示的数据个数
$number = 15;
// 获取json数据
$article = file_get_contents("php://input");
// 获取不到json
if (!$article) {
    echo "{\"result\":\"参数错误1\"}";
    return;
}

$articleJson = json_decode($article, true);
//获取的json对象参数个数不为2，且不包含键title和键content               count($articleJson) != 2 || 
// if (!array_key_exists('title', $articleJson) || !array_key_exists('content', $articleJson)) {
//     echo "{\"result\":\"参数错误2\"}";
//     return;
// }
// //获取的json对象参数为空
// if (!$articleJson['title'] || !$articleJson['content']) {
//     echo "{\"result\":\"参数错误3\"}";
//     return;
// }


$articleSQL = "SELECT * FROM article_describe st ";

$page = $articleJson['page'];
$tag = $articleJson['tag'];
$classify = $articleJson['classify'];

if ($tag != 'all')
    $articleSQL .= "WHERE st.tag = '$tag' ";

switch ($classify) {
    case 'latest_reply':
        $articleSQL .= "ORDER BY st.latest_reply_time DESC";
        break;
    case 'hot_topic':
        $articleSQL .= "ORDER BY st.comment DESC";
        break;
    case 'recent_posts':
        $articleSQL .= "ORDER BY st.create_time DESC";
        break;
    case 'history_post':
        $articleSQL .= "ORDER BY st.create_time ASC";
        break;
}
$articles = mysqli_query($forum, $articleSQL);

if (!preg_match('/^\d+$/', $page)) { //验证结果是不是数字
    echo "{\"result\":\"请不要使用post工具获取数据\"}";
    return;
}

$result = [];

//数据库查询到的数据个数
$dataNumber = mysqli_num_rows($articles);
//总页数
$pageNumber = ceil(($dataNumber / $number));
$result['pageNumber'] = $pageNumber;

//当前页面限定在范围内
if ($page <= 0)
    $page = 1;
else if ($page > $pageNumber)
    $page = $pageNumber;

//起始遍历位置
$start = ($page - 1) * $number;
//结束位置
$end = $page * $number;

//查询的结果数组
$articles = mysqli_fetch_all($articles, MYSQLI_ASSOC);

for ($i = $start; $i < $end && $i < $dataNumber; $i++) {
    //获取该用户的头像
    $username = $articles[$i]['username'];
    $userHeadImgSQL = "SELECT * FROM user WHERE username = '$username'";
    $userHeadImgResult = mysqli_fetch_assoc(mysqli_query($account, $userHeadImgSQL));
    $articles[$i]['headImg'] = $userHeadImgResult['headImg'];

    $result['articles'][] = $articles[$i];
}
echo json_encode($result);
