<?php
//连接数据库
include '../control/connect.php';
/**
 * 心跳检测
 * 该功能用于判断用户上下线时间
 */

require_once __DIR__ . '/workerman/Autoloader.php';

use Workerman\Worker;
use Workerman\Lib\Timer;

// 心跳间隔10秒
define('HEARTBEAT_TIME', 10);
// 2秒检查一次连接是否下线
define('CHECK_HEARTBEAT_TIME', 2);

$worker = new Worker('websocket://0.0.0.0:8888');
//收到消息回调
$worker->onMessage = function ($connection, $username) {
    // 给connection临时设置一个lastMessageTime属性，用来记录上次收到消息的时间
    $connection->lastMessageTime = time();
    $connection->send("用户" . $username);
    //创建一个连接uid
    $connection->uid = $username;
    global $account; //访问外部变量需要
    $onlineSQL = "update user set online = '1' where username = '$connection->uid'";
    mysqli_query($account, $onlineSQL);
};

// 进程启动后设置一个每 CHECK_HEARTBEAT_TIME 秒运行一次的定时器
$worker->onWorkerStart = function ($worker) {
    Timer::add(CHECK_HEARTBEAT_TIME, function () use ($worker) {
        $time_now = time();
        foreach ($worker->connections as $connection) {
            // 有可能该connection还没收到过消息，则lastMessageTime设置为当前时间
            if (empty($connection->lastMessageTime) || !isset($connection->lastMessageTime)) {
                $connection->lastMessageTime = $time_now;
                continue;
            }
            echo '-------------------------------------------------------------------------------------------';
            echo date("H:i:s", $time_now) . "----------" . date("H:i:s", $connection->lastMessageTime);
            // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
            if ($time_now - $connection->lastMessageTime > HEARTBEAT_TIME) {
                $connection->send("连接超时，关闭连接");
                updateOnline($connection->uid);
                $connection->close();
            }
        }
    });
};

$worker->onClose = function ($connection) {
    echo "['$connection->uid']已下线";
    updateOnline($connection->uid);
};

//更新数据库在线情况和最后在线时间
function updateOnline($username)
{
    global $account;
    $nowTime = time();
    $onlineSQL = "update user set online = '0', lastLoginTime = '$nowTime' where username = '$username'";
    mysqli_query($account, $onlineSQL);
}

Worker::runAll();
