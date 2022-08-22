# 力扣

## 竞赛积分+近期比赛数据

接口网址：

```
https://leetcode.cn/graphql/noj-go/
```

```json
{
    "query": "\n    query userContestRankingInfo($userSlug: String!) {\n  userContestRanking(userSlug: $userSlug) {\n    attendedContestsCount\n    rating\n    globalRanking\n    localRanking\n    globalTotalParticipants\n    localTotalParticipants\n    topPercentage\n  }\n  userContestRankingHistory(userSlug: $userSlug) {\n    attended\n    totalProblems\n    trendingDirection\n    finishTimeInSeconds\n    rating\n    score\n    ranking\n    contest {\n      title\n      titleCn\n      startTime\n    }\n  }\n}\n    ",
    "variables": {
        "userSlug": "dayangtuo247"
    }
}
```

只需要竞赛积分

```json
{
    "query": "\n query userContestRankingInfo($userSlug: String!) {\n userContestRanking(userSlug: $userSlug) {\n rating\n}\n\n}\n",
    "variables": {
        "userSlug": "dayangtuo247"
    }
}
```



## 做题统计

 接口：

```
https://leetcode.cn/graphql/
```

```json
{
    "query": "\n query userQuestionProgress($userSlug: String!) {\n userProfileUserQuestionProgress(userSlug: $userSlug) {\n numAcceptedQuestions {\n difficulty\n count\n}\n}\n}\n",
    "variables": {
        "userSlug": "dayangtuo247"
    }
}
```





## codeforces竞赛积分

```
https://codeforces.com/api/user.rating?handle=dayangtuo247
```

方式二：

```
https://codeforces.com/api/user.info?handles=dayangtuo247;kuan525,wzy666
```

做题榜单

```
http://codeforces.com/problemset/standings
```





## 力扣预测

```
https://lcpcdn.f15.pw/contest/weekly-contest-299/ranking/search
https://lcpcdn.f15.pw/contest/biweekly-contest-81/ranking/search
https://lcpredictor.herokuapp.com/contest/weekly-contest-301/ranking/search
```

发送数据

```
{
    "user": "dayangtuo247"
}
```



## 获取力扣最近场次

接口

```
https://leetcode.cn/graphql
```

```json
{
    "operationName": "contestHistory",
    "variables": {
        "pageNum": 1,
        "pageSize": 10
    },
    "query": "query contestHistory($pageNum: Int!, $pageSize: Int) {\n  contestHistory(pageNum: $pageNum, pageSize: $pageSize) {\n contests {\n title\n titleSlug\n}\n}\n}\n"
}
```

```json
{
    "operationName": "contestHistory",
    "variables": {
        "pageNum": 1,
        "pageSize": 10
    },
    "query": "query contestHistory($pageNum: Int!, $pageSize: Int) {\n  contestHistory(pageNum: $pageNum, pageSize: $pageSize) {\n contests {\n title\n titleSlug\n}\n}\n}\n"
}
```
