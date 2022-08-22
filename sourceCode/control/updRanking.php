<?php
//取消php超时时间设置
ini_set('max_execution_time', '0');

header("Content-Type: text/html; charset=utf8");
//连接数据库
include 'connect.php';

//PHP向API发送Json对象数据, $url 请求url, $jsonStr 发送的json字符串, $transitionJson 结果是否转换未json格式
function http_post_json($url, $jsonStr, $transitionJson)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($jsonStr)
        )
    );
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    //解析为json数据
    if ($transitionJson)
        $response = json_decode($response, true);
    return array($httpCode, $response);
}

//各做题网站API
//力扣竞赛积分+以往周赛API
$leetCodeIntegralUrl = "https://leetcode.cn/graphql/noj-go/";
//力扣做题统计, 周赛场次API
$leetCodeSolvelUrl = "https://leetcode.cn/graphql/";
//codeforces竞赛积分
$codeForcesIntegralUrl = "https://codeforces.com/api/user.info?handles=";
$codeForcesIntegralUser = "";

$userSql = "SELECT * FROM user";
$userResult = mysqli_query($account, $userSql);
//将所有用户信息导入数组
while ($row = mysqli_fetch_assoc($userResult))
    $userNumber[] = $row;

//获取力扣最近两场比赛名称
$jsonStr = json_encode(array('operationName' => "contestHistory", 'variables' => array('pageNum' => 1, 'pageSize' => 5), 'query' => "query contestHistory(\$pageNum: Int!, \$pageSize: Int) {\n contestHistory(pageNum: \$pageNum, pageSize: \$pageSize) {\n contests {\n title\n titleSlug\n}\n}\n}\n"));
list($returnCode, $leetcodeContestResult) = http_post_json($leetCodeSolvelUrl, $jsonStr, true);
$leetcodeContest = $leetcodeContestResult['data']['contestHistory']['contests'];
for ($i = 0, $j = 0; $i < count($leetcodeContest); $i++) {
    $test = preg_match('/^(bi)?weekly-contest-\d+$/', $leetcodeContest[$i]['titleSlug'], $contestName);
    if ($test) {
        $leetCodeContestTitle[$j++] = $contestName[0];
        $sql = "UPDATE homepage SET content = '$contestName[0]' WHERE `name` = 'leetCodeContest$j'";
        mysqli_query($publicdata, $sql);
    }
    if ($j >= 2)
        break;
}

//为了方便接下来更新填写codeforces链接的用户
$updateCodeForcesUser = [];

//更新力扣数据
//遍历用户数据库
for ($i = 0; $i < count($userNumber); $i++) {
    //获取用户数据库用户名
    $username = $userNumber[$i]["username"];
    //当该用户不存在时，将该用户插入数据库
    $updateRanking = "INSERT ignore INTO rankinglist(username) VALUE('$username')";
    mysqli_query($publicdata, $updateRanking);

    //---------------------------------------------------更新codeforces请求url，题外话------------------------------------------------------
    //由于codeforces可以一次性获取10000以内的用户数据，且只允许2秒内获取一次，因此这里对请求url后加username形式，之后一次性获取
    $CFrequest_state = preg_match('/^https:\/\/codeforces\.com\/profile\/([\w0-9]+)\/?$/', $userNumber[$i]['codeforcesPage'], $username_cf);
    //当捕获到用户填写的codeforces链接后,将该名字追加到请求url中
    if ($CFrequest_state) {
        $codeForcesIntegralUser .= $username_cf[1] . ";";
        $updateCodeForcesUser[$username_cf[1]] = $username;
        $updateUsername_CF_SQL = "UPDATE rankinglist SET username_CF = '$username_cf[1]' WHERE username = '$username'";
        mysqli_query($publicdata, $updateUsername_CF_SQL);
    }
    //------------------------------------------------------------------------------------------------------------------------------------

    //参数说明：返回值为是否捕获成功，捕获leetcode用户名正则表达式；获取力扣主页链接；返回捕获到的力扣用户名，codeforces同理
    $LCrequest_state = preg_match('/^https:\/\/leetcode\.cn\/u\/([\w-]+)\/?$/', $userNumber[$i]['leetCodePage'], $username_lc);
    if (!$LCrequest_state) //未捕获到力扣用户名
        continue;

    //将力扣用户名加入数据库
    $updateUsername_LC_SQL = "UPDATE rankinglist SET username_LC = '$username_lc[1]' WHERE username = '$username'";
    mysqli_query($publicdata, $updateUsername_LC_SQL);

    //获取力扣竞赛积分json
    $requestContentJson1 = json_encode(array('query' => "\n query userContestRankingInfo(\$userSlug: String!) {\n userContestRanking(userSlug: \$userSlug) {\n rating\n}\n\n}\n", 'variables' => array('userSlug' => $username_lc[1])));
    list($returnState1, $json_obj1) = http_post_json($leetCodeIntegralUrl, $requestContentJson1, true);

    //由于网站反爬虫，随机暂停10-40秒
    // sleep(mt_rand(10, 40));

    //获取力扣做题数量json
    $requestContentJson2 = json_encode(array('query' => "\n query userQuestionProgress(\$userSlug: String!) {\n userProfileUserQuestionProgress(userSlug: \$userSlug) {\n numAcceptedQuestions {\n difficulty\n count\n}\n}\n}\n", 'variables' => array('userSlug' => $username_lc[1])));
    list($returnState2, $json_obj2) = http_post_json($leetCodeSolvelUrl, $requestContentJson2, true);

    //获取近两场比赛的参加情况
    $requestContentJson3 = json_encode(array('user' => $username_lc[1]));
    for ($j = 1; $j < 3; $j++) {
        $leetCodeContestUrl = "https://lcpcdn.f15.pw/contest/" . $leetCodeContestTitle[$j - 1] . "/ranking/search";
        list($returnState3, $json_obj3) = http_post_json($leetCodeContestUrl, $requestContentJson3, false);

        // sleep(mt_rand(5, 20));

        //如果未找到，则代表参加了该场次
        if (!strpos($json_obj3, "No matching records found"))
            $leetCodeContestNumberSQL = "UPDATE rankinglist SET leetCodeContest$j = '[ √ ]' WHERE username = '$username'";
        else
            $leetCodeContestNumberSQL = "UPDATE rankinglist SET leetCodeContest$j = '[ X ]' WHERE username = '$username'";
        mysqli_query($publicdata, $leetCodeContestNumberSQL);
    }

    //竞赛积分+做题数量获取失败
    if (!$returnState1 || !$returnState2)
        continue;

    //将leetcode竞赛积分json中的rating提取
    $userLeetCodeIntegral = $json_obj1["data"]["userContestRanking"]["rating"];
    //做题统计初始为0
    $userLeetCodeSolveProblems = 0;
    //依次将简、中、难题相加得到leetcode做题总数
    for ($j = 0; $j < 3; $j++)
        $userLeetCodeSolveProblems += $json_obj2["data"]["userProfileUserQuestionProgress"]["numAcceptedQuestions"][$j]["count"];

    //根据该数据，更新内容
    $updateRanking = "UPDATE rankinglist SET leetCodeIntegral = '$userLeetCodeIntegral', solveProblems = '$userLeetCodeSolveProblems' WHERE username = '$username'";
    $rankingResult = mysqli_query($publicdata, $updateRanking);
}

//更新codeforces竞赛积分
//存在填写了codeforces链接的用户
if ($codeForcesIntegralUser) {
    list($returnState4, $json_obj4) = http_post_json($codeForcesIntegralUrl . $codeForcesIntegralUser, "", true);
    for ($i = 0; $i < count($json_obj4['result']); $i++) {
        //获取codeforces用户名
        $temp = $json_obj4["result"][$i]['handle'];
        //使用之前的关联数组，讲codeforces用户名匹配到网站下创建的用户名
        $username = $updateCodeForcesUser[$temp];
        //获取codeforces最大竞赛积分和当前竞赛积分
        if (array_key_exists("rating", $json_obj4["result"][$i])) {
            $codeForcesIntegralMAX = $json_obj4["result"][$i]["maxRating"];
            $codeForcesIntegralNOW = $json_obj4["result"][$i]["rating"];
        } else {
            $codeForcesIntegralMAX = 0;
            $codeForcesIntegralNOW = 0;
        }
        $updateRanking = "UPDATE rankinglist SET codeForcesIntegralNOW = '$codeForcesIntegralNOW', codeForcesIntegralMAX = '$codeForcesIntegralMAX' WHERE username = '$username'";
        $rankingResult = mysqli_query($publicdata, $updateRanking);
    }
}
