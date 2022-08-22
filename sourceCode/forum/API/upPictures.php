<?php
header('Content-type: text/json');
$result = array(
    'success' => 0,
    'message' => null,
    'url'     => null
);
session_start();
if (!isset($_SESSION['username'])) {
    $result['message'] = "请登录!";
    echo json_encode($result);
    return;
}

// 允许上传的图片后缀
$allowedExts = array("gif", "jpeg", "jpg", "png");
// 获取文件后缀名
$temp = explode(".", $_FILES["editormd-image-file"]["name"]);
$extension = end($temp);
//使用 $_FILES["file"]["type"] 检查文件二进制代码是否符合格式要求
if (in_array($extension, $allowedExts)) {
    // 大于 10MB
    if ($_FILES["editormd-image-file"]["size"] > 10 * 1024 * 1024) {
        $result['message'] = "图片过大，上传限制10MB";
    } else if ($_FILES["editormd-image-file"]["error"] > 0) {
        $result['message'] = "发生未知错误，请联系管理员";
    } else {
        //使用时间重命名文件
        $_FILES["editormd-image-file"]["name"] = uniqid() . '.' . $extension;
        // 将文件上传到 ../forum/temp/ 目录下
        $folderName = "/forum/temp/" . $_FILES["editormd-image-file"]["name"];
        move_uploaded_file($_FILES["editormd-image-file"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $folderName);
        $result['success'] = 1;
        $result['message'] = "上传成功";
        $result['url'] = $folderName;
    }
} else {
    $result['message'] = "仅支持 ";
    for ($i = 0; $i < count($allowedExts); $i++)
        $result['message'] .= $allowedExts;
    $result['message'] .= " 格式的图片文件";
}

echo json_encode($result);
