<?php
$configs = require_once(__DIR__ . '/../dbConfig.php');
$publicdata = new Mysqli($configs['host'], $configs['account'], $configs['password'], 'publicdata');
$account = new Mysqli($configs['host'], $configs['account'], $configs['password'], 'account');
$forum = new Mysqli($configs['host'], $configs['account'], $configs['password'], 'forum');
if ($publicdata->connect_errno || $account->connect_errno || $forum->connect_errno)
    header("Location: error/error.html");
