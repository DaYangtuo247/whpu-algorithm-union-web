<?php
header("Content-Type: text/json");
include 'connect.php';

$username = $_POST['username']; //post获得用户名表单值
$password = $_POST['password']; //post获得用户密码单值

if (!$username || !$password) {
    echo "{\"result\":\"表单填写不完整\"}";
    return;
} else if (!preg_match('/^[0-9a-zA-Z_]{3,12}$/', $username)) {
    echo "{\"result\":\"账号不符合规范\"}";
    return;
} else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\S]{6,18}$/', $password)) {
    echo "{\"result\":\"密码不符合规范1\"}";
    return;
}

$sql = "select * from user where username='$username'";
$result = mysqli_query($account, $sql); //执行sql
if ($result && mysqli_num_rows($result) == 0) {
    $password = password_hash($password, PASSWORD_DEFAULT); //密码哈希加密后存储
    $fileName = mt_rand(1, 16) . ".jpg";
    $nowDate = time();
    $sql = "insert into user(username,password,`like`,headImg,online,onlineTime,registrationDate) value('$username','$password','0','../userinfo/userPictures/default/$fileName','0','0','$nowDate')";
    mysqli_query($account, $sql);
    session_start();
    $_SESSION["username"] = $username;
    echo "{\"result\":\"注册成功\"}";
    return;
} else {
    echo "{\"result\":\"用户名已存在\"}";
    return;
}
