<?php
header('Content-type: text/json');
include "../../control/connect.php";
// 获取json数据
$result = array(
    'success' => false,
    'message' => "发布成功",
    'article_id' => null
);

session_start();
if (!isset($_SESSION['username'])) {
    $result['message'] = "请登录!";
    echo json_encode($result);
    return;
}

$article = file_get_contents("php://input");
// 获取不到json
if (!$article) {
    $result['message'] = "参数错误";
    echo json_encode($result);
    return;
}
$articleJson = json_decode($article, true);
//获取的json对象参数个数不为2，且不包含键title和键content               count($articleJson) != 2 || 
if (!array_key_exists('title', $articleJson) || !array_key_exists('content', $articleJson)) {
    $result['message'] = "参数错误2";
    echo json_encode($result);
    return;
}
//获取的json对象参数为空
if (!$articleJson['title'] || !$articleJson['content']) {
    $result['message'] = "参数错误3";
    echo json_encode($result);
    return;
}

$time = time();
$article_id = $time;
//编辑模式
if ($articleJson['mode'] == 'edit') {
    $article_id = $articleJson['article_id'];
    //更新修改时间
    $editUpdateSQL = "UPDATE article_describe SET last_change_time = '$time' WHERE article_id = '$article_id'";
    mysqli_query($forum, $editUpdateSQL);
    $result['message'] = "修改成功";
}

$create_time = $article_id;
$username = $_SESSION['username'];
$tag = $articleJson['tag'];
$title = $articleJson['title'];
$content = $articleJson['content'];
$describe = mb_substr($content, 0, 200) . "...";

$describe = str_replace('<', '&lt;', $describe);
$describe = str_replace('>', '&gt;', $describe);

//移动图片到文章文件夹下
$imageRegx = '/!\[.*\]\(\/forum\/temp\/(.+\.[\w]+)(\S+.+)?\)/';
$imageRegxSuccess = preg_match_all($imageRegx, $content, $inageArray, PREG_SET_ORDER);
if ($imageRegxSuccess) {
    $nowFolder = "../articleImage/" . $username . "-" . $article_id;
    if (!is_dir($nowFolder)) {
        Mkdir($nowFolder, 0777, true);
    }
    for ($i = 0; $i < count($inageArray); $i++) {
        //移动文件
        rename("../temp/" . $inageArray[$i][1], $nowFolder . '/' . $inageArray[$i][1]);

        //将content内容区域的图片路径替换为对应文章的路径
        $nowpath = 'articleImage/' . $username . "-" . $article_id;
        $inageArrayTemp = str_replace('temp', $nowpath, $inageArray[$i][0]);
        $content = str_replace($inageArray[$i][0], $inageArrayTemp, $content);
    }
}

$tagsSQL = "INSERT ignore INTO tags(tag_name, tag_number) VALUE('$tag', '0')";
mysqli_query($forum, $tagsSQL);
$tagsSQL = "UPDATE tags SET tag_number=tag_number+1 WHERE tag_name='$tag'";
mysqli_query($forum, $tagsSQL);


$article_describeSQL = "INSERT ignore INTO article_describe(article_id, username,title,`like`,`collection`,comment,create_time,last_change_time,page_view,tag) VALUE('$article_id', '$username', '$title', '0', '0', '0','$create_time', '0', '0', '$tag')";
mysqli_query($forum, $article_describeSQL);

$articleSQL = "INSERT ignore INTO article(article_id) VALUE('$article_id')";
mysqli_query($forum, $articleSQL);

//参数化查询，防止sql注入
$article_describeSQL = $forum->prepare("UPDATE article_describe SET `describe` = ? WHERE article_id = '$article_id'");
$article_describeSQL->bind_param("s", $describe);
$article_describeSQL->execute();

$articleSQL = $forum->prepare("UPDATE article SET content = ? WHERE article_id = '$article_id'");
$articleSQL->bind_param("s", $content);
$articleSQL->execute();

$result['success'] = true;
$result['article_id'] = $article_id;
echo json_encode($result);
