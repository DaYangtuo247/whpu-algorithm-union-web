/**
 * 开启coco弹窗
 * 来源于https://github.com/TheWindRises-2/coco-message
 */
cocoMessage.config({
    duration: 10000,
});

//按下用户button按钮
function btnUser(obj) {
    $(obj).attr('class', 'userEnterBtn');
    $('.select-menu').fadeIn(200);
}

//点击非下拉菜单区域隐藏下拉菜单
$('html').on('click', function (e) {
    if (!$(e.target).closest('.online').length > 0) {
        $('.select-menu').fadeOut(200);
        $('.userEnterBtn').removeClass('userEnterBtn');
    }
});

//使用github登录
function githubLogin() {
    cocoMessage.info("github登录暂未实现，请等待", 3000);
}

//设置cookie
function setCookie(name, value) {
    var Days = 30;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
}
//拿到cookie
function getCookie(name) {
    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
    if (arr = document.cookie.match(reg))
        return unescape(arr[2]);
    else
        return null;
}
//删除cookie
function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = getCookie(name);
    if (cval != null)
        document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}

//登录表单正则验证
function loginRegex(data) {
    let username = data[0].value,
        pwd = data[1].value;

    if (username == '' || pwd == '')
        return '表单填写不完整';
    return "符合规范";
}

//注册表单正则验证
function registerRegex(data) {
    let username = data[0].value,
        pwd1 = data[1].value,
        pwd2 = data[2].value;
    let idRegex = /^[0-9a-zA-Z_]{3,16}$/,
        pwdRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\S]{6,18}$/;

    if (username == '' || pwd1 == '' || pwd2 == '')
        return '表单填写不完整';
    if (!idRegex.test(username))
        return '账号不符合规范';
    if (pwd1 !== pwd2)
        return '两次密码不一致';
    if (!pwdRegex.test(pwd1))
        return '密码不符合规范';
    return "符合规范";
}

//退出登录注册界面
function changeModalQuit() {
    $(".modal-content").hide().animate({ "width": "-=370px" }, 400);
    $('.changeModal').fadeOut(150);
}

//转到注册界面
function toregister() {
    $('.changeModal').fadeIn(150);
    $('.modal-content').show().animate({ "width": "370px" }, 400);
    $('#register').show();
    $('#login').hide();
}

//转到登录界面
function tologin() {
    $('.changeModal').fadeIn(150);
    $('.modal-content').show().animate({ "width": "370px" }, 400);
    $('#register').hide();
    $('#login').show();
}

// 点击弹窗内容以外的地方关闭弹窗
$('.changeModal').on('click', function (e) {
    if (!$(e.target).closest('.modal-content').length > 0) {
        changeModalQuit();
    }
});

//显示搜索框
function showSearch() {
    let searchBut = document.getElementById('searchBut'),
        searchText = document.getElementById('searchText');
    if (searchText.style.opacity == 0) {
        searchText.style.opacity = 1;
        searchText.style.width = '240px';
    } else {
        searchText.style.opacity = 0;
        searchText.style.width = '0px';
    }
}

//获取cookie信息，填写到登录表单
$(function () {
    if (getCookie('name') && getCookie('password')) {
        $('#username').val(getCookie('name'));
        $('#password').val(getCookie('password'));
        $('#memory').prop('checked', 'checked');
    } else {
        $('#username').val('');
        $('#password').val('');
    }
});

//点击取消记住我按钮
function memoryClick() {
    if (!$('#memory').prop('checked')) {
        delCookie('name');
        delCookie('password');
    }
}

//登录验证
function LoginUser() {
    let result = loginRegex($('#form1').serializeArray());
    if (result !== '符合规范') {
        cocoMessage.error(result, 3000);
        return;
    }
    //ajax提交表单
    $.ajax({
        type: "POST",
        url: "/control/login.php",
        data: $('#form1').serialize(),
        dataType: "json",
        async: true,
        //ajax请求前loading
        beforeSend: function () {
            $('.loadings').fadeIn(200);
        },
        //ajax请求完成后
        complete: function () {
            $('.loadings').fadeOut(200);
        },
        success: function (data) {
            if (data.result == "登陆成功") {
                // cocoMessage.success("登陆成功", 3000);
                //将账号信息保存到cookie中
                var username = $('#username').val();
                var password = $('#password').val();
                setCookie("name", username);
                setCookie("password", password);
                window.localStorage.setItem('account', username);
                // setTimeout("location.reload();", 500);
                location.reload();
            } else {
                cocoMessage.error(data.result, 3000);
            }
        },
        error: function (data) {
            cocoMessage.error("连接服务器失败，请联系管理员", 3000);
        }
    });
}

//注册表单
function RegisterUser() {
    let result = registerRegex($('#form2').serializeArray());
    if (result !== '符合规范') {
        cocoMessage.error(result, 3000);
        return;
    }
    $.ajax({
        type: "POST",
        url: "/control/register.php",
        data: $('#form2').serialize(),
        dataType: "json",
        async: true,
        //ajax请求前loading
        beforeSend: function () {
            $('.loadings').fadeIn(200);
        },
        //ajax请求完成后
        complete: function () {
            $('.loadings').fadeOut(200);
        },
        success: function (data) {
            console.log(data);
            if (data.result == "注册成功") {
                cocoMessage.success("注册成功", 3000);
                changeModalQuit();
            } else {
                cocoMessage.error(data.result, 3000);
            }
        },
        error: function (data) {
            cocoMessage.error("连接服务器失败，请联系管理员", 3000);
        }
    });
}

//退出请求
$('.select-menu').on('click', '.quit', function () {
    $.ajax({
        type: "get",
        url: '/control/quit.php'
    })
    window.localStorage.removeItem('account');
});