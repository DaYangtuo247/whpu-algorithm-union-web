/**
 * 心跳连接保持
 * 来源于：https://www.workerman.net/
 */
$(function () {
    let username = window.localStorage.getItem('account');
    if (username == null)
        return;
    $.ajax({
        type: "post",
        url: "/control/loginCheck.php",
        data: JSON.stringify({
            username: username
        }),
        dataType: "json",
        success: function (data) {
            //当前用户已登录，心跳连接启动
            if (data.success == true) {
                wsClient = new WebSocket("ws://127.0.0.1:8888");
                var checkTime = 8000; //每8秒向服务器发送一次数据
                //连接成功
                wsClient.onopen = function () {
                    wsClient.send(username); //发送用户名
                    setInterval(function () {
                        keepalive(wsClient)
                    }, checkTime);
                };

                //连接成功回调
                wsClient.onmessage = function (e) {
                    console.log(e.data + "依然在连接状态");
                };

                //保持连接
                function keepalive(wsClient) {
                    wsClient.send(username);
                }

                //连接失败
                wsClient.onerror = function (e) {
                    window.localStorage.removeItem('account');
                    cocoMessage.warning("登录成功，但是服务器端出现问题，请联系管理员处理", 0);
                };
            } else {
                window.localStorage.removeItem('account');
            }
        }
    });
});