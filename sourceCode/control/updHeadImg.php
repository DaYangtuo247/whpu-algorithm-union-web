<?php
include 'connect.php';

$username = "Dayangtuo247";

//检查用户是否存在
$sql = "select * from user where username='$username'";
$result = mysqli_query($account, $sql);
if ($result && mysqli_num_rows($result) == 0) {
    echo "{\"result\":\"用户不存在，请不要使用非常规路径上传！\"}";
    return;
}

// 允许上传的图片后缀
$allowedExts = array("gif", "jpeg", "jpg", "png");
// 获取文件后缀名
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);
//使用 $_FILES["file"]["type"] 检查文件二进制代码是否符合格式要求
if ((($_FILES["file"]["type"] == "image/gif")
        || ($_FILES["file"]["type"] == "image/jpeg")
        || ($_FILES["file"]["type"] == "image/jpg")
        || ($_FILES["file"]["type"] == "image/pjpeg")
        || ($_FILES["file"]["type"] == "image/x-png")
        || ($_FILES["file"]["type"] == "image/png"))
    && in_array($extension, $allowedExts)
) {
    // 大于 2MB
    if ($_FILES["file"]["size"] > 2000000) {
        echo "{\"result\":\"图片过大,上传限制2MB\"}";
        return;
    }

    if ($_FILES["file"]["error"] > 0) {
        echo "{\"result\":\"发生未知错误\"}";
    } else {
        $searchFileSQL = "SELECT * FROM user WHERE username = '$username'";
        $result = mysqli_fetch_assoc(mysqli_query($account, $searchFileSQL));
        $searchRegx = preg_match('/\/default\//', $result['headImg']);
        //该头像不属于默认头像,那么就删除原来的头像，使用新头像
        if (!$searchRegx)
            unlink($result['headImg']);

        // 将文件上传到 ../userinfo/userPictures/ 目录下
        move_uploaded_file($_FILES["file"]["tmp_name"], "../userinfo/userPictures/" . $_FILES["file"]["name"]);
        $fileName = $_FILES["file"]["name"];
        $updateHheadImg = "UPDATE user SET headImg = '../userinfo/userPictures/$fileName' WHERE username = '$username'";
        mysqli_query($account, $updateHheadImg);
        echo "{\"result\":\"上传成功\"}";
    }
} else {
    echo "{\"result\":\"图片格式错误\"}";
}
