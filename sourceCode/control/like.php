<?php
header('Content-type: text/json');
include 'connect.php';

$result = array(
    'success' => true
);

// 获取json数据
$url = file_get_contents("php://input");
// 获取不到json
if (!$url) {
    echo "{\"result\":\"参数错误0\"}";
    return;
}
$urlJson = json_decode($url, true);

//用户点赞
if ($urlJson['mode'] == "user-like") {
    //获取的json对象参数个数不为2，且不包含键username和键likeuser
    if (count($urlJson) != 3 || !array_key_exists('username', $urlJson) || !array_key_exists('likeuser', $urlJson)) {
        echo "{\"result\":\"参数错误1\"}";
        return;
    }
    //获取的json对象参数为空
    if (!$urlJson['username'] || !$urlJson['likeuser']) {
        echo "{\"result\":\"参数错误2\"}";
        return;
    }

    if ($urlJson['username'] == 'not_login') {
        echo "{\"result\":\"未登录\"}";
        return;
    }
    //点赞发起人
    $username = $urlJson['username'];
    //被点赞人
    $likeuser = $urlJson['likeuser'];
    //在今日点赞表中插入该对点赞情况
    $sql = "SELECT * FROM todaylike WHERE username = '$username' AND likeuser = '$likeuser'";
    $searchResult =  mysqli_query($account, $sql);

    //获取用户被点赞数目
    $userLikeNumberSQL = "SELECT * FROM user WHERE username = '$likeuser'";
    $userLikeNumberResult = mysqli_query($account, $userLikeNumberSQL);
    $userLikeNumber = mysqli_fetch_assoc($userLikeNumberResult)['like'];

    //未查询到A对B的点赞
    if ($searchResult && mysqli_num_rows($searchResult) == 0) {
        $sql = "insert into todaylike(username, likeuser) value('$username', '$likeuser')";
        mysqli_query($account, $sql);

        //更新点赞数目
        $userLikeNumber++;
        $userLikeNumberSQL = "UPDATE user SET `like` = '$userLikeNumber' WHERE username = '$likeuser'";

        mysqli_query($account, $userLikeNumberSQL);
        echo "{\"result\":\"点赞成功\"}";
        return;
    } else {
        //查询到A对B的点赞
        $sql = "DELETE FROM todaylike WHERE username='$username' AND likeuser='$likeuser'";
        mysqli_query($account, $sql);

        //更新点赞数目
        $userLikeNumber--;
        $userLikeNumberSQL = "UPDATE user SET `like` = '$userLikeNumber' WHERE username = '$likeuser'";

        mysqli_query($account, $userLikeNumberSQL);
        echo "{\"result\":\"点赞取消\"}";
        return;
    }
}

//默认点赞对象是评论表点赞
$username = $urlJson['username'];
$operator_db = "comment";
$operator_db_id = "comment_id";

//点赞对象是评论表
if ($urlJson['mode'] == 'comment-like' || $urlJson['mode'] == 'comment-unlike') {
    $operator_id_num = $urlJson['comment_id'];
    $LikeUserSearchSQL = "SELECT * FROM $operator_db WHERE $operator_db_id ='$operator_id_num'";
    $LikeUserSearch = mysqli_fetch_array(mysqli_query($forum, $LikeUserSearchSQL));
    //获取点赞名单
    $LikeUser = $LikeUserSearch['like_user'];
    $unLikeUser = $LikeUserSearch['unlike_user'];
    $LikeUserCount = $LikeUserSearch['like'];
    $unLikeUserCount = $LikeUserSearch['unlike'];
    //按逗号分隔为数组
    $like_user_list = explode(',', $LikeUser);
    $unlike_user_list = explode(',', $unLikeUser);
    //点赞
    if ($urlJson['mode'] == 'comment-like') {
        //存在点赞，删除点赞
        if (in_array($username, $like_user_list)) {
            if (($key = array_search($username, $like_user_list))) {
                unset($like_user_list[$key]);
                $LikeUserCount--;
            }
        } else {
            array_push($like_user_list, $username);
            $LikeUserCount++;

            //查询是否存在踩记录
            if (in_array($username, $unlike_user_list)) {
                if (($key = array_search($username, $unlike_user_list))) {
                    unset($unlike_user_list[$key]);
                    $unLikeUserCount--;
                }
            }
        }
    }
    //踩
    else {
        //存在踩，删除踩
        if (in_array($username, $unlike_user_list)) {
            if (($key = array_search($username, $unlike_user_list))) {
                unset($unlike_user_list[$key]);
                $unLikeUserCount--;
            }
        } else {
            array_push($unlike_user_list, $username);
            $unLikeUserCount++;

            //查询是否存在点赞记录
            if (in_array($username, $like_user_list)) {
                if (($key = array_search($username, $like_user_list))) {
                    unset($like_user_list[$key]);
                    $LikeUserCount--;
                }
            }
        }
    }
    $like_user_list = implode(',', $like_user_list);
    $unlike_user_list = implode(',', $unlike_user_list);
    $LikeUserSearchSQL = "UPDATE $operator_db SET `like` = '$LikeUserCount', `unlike` = '$unLikeUserCount', like_user = '$like_user_list', unlike_user = '$unlike_user_list' WHERE $operator_db_id = '$operator_id_num'";
    mysqli_query($forum, $LikeUserSearchSQL);
}
//文章点赞或收藏
else if ($urlJson['mode'] == 'article-like' || $urlJson['mode'] == 'collection-exist') {
    $operator_db = "article_describe";
    $operator_db_id = "article_id";
    $operator_id_num = $urlJson['article_id'];
    $operator_fields = 'like';
    if ($urlJson['mode'] == 'collection-exist') {
        $operator_fields = 'collection';
    }

    $LikeUserSQL = "SELECT * FROM $operator_db WHERE $operator_db_id ='$operator_id_num'";
    //获取点赞名单
    $LikeUser = mysqli_fetch_array(mysqli_query($forum, $LikeUserSQL))[$operator_fields . '_user'];
    //按逗号分隔为数组
    $like_user_list = explode(',', $LikeUser);
    //该用户点赞过
    if (in_array($username, $like_user_list)) {
        //删除点赞记录
        if (($key = array_search($username, $like_user_list))) {
            unset($like_user_list[$key]);
        }
        $like_user_list = implode(',', $like_user_list);
        $LikeUserSQL = "UPDATE $operator_db SET `$operator_fields` = `$operator_fields`-1, `$operator_fields" . "_user" . "` = '$like_user_list' WHERE $operator_db_id = '$operator_id_num'";
    } else {
        array_push($like_user_list, $username);
        $like_user_list = implode(',', $like_user_list);
        $LikeUserSQL = "UPDATE $operator_db SET `$operator_fields` = `$operator_fields`+1, `$operator_fields" . "_user" . "` = '$like_user_list' WHERE $operator_db_id = '$operator_id_num'";
    }
    mysqli_query($forum, $LikeUserSQL);
}

$result['success'] = true;
echo json_encode($result);
