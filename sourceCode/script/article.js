//让toc目录父级高度为页面高度
window.onload = function () {
    $('.module').css({
        "height": $('.article-show').css('height')
    });
    //滚动1像素滚动触发滚动事件，必需
    $(window).scrollTop(1);
};

//滚动到指定对象的锚点位置，headerH为滚动后锚点距离顶部高度
function scrollAppoint(headerH, obj) {
    let nowActive = $.attr(obj, 'href').substr(1, $.attr(obj, 'href').length);
    $('html, body').animate({
        scrollTop: $(`a[name='${nowActive}']`).offset().top - headerH
    }, 400);
}

//对具有子目录的目录标签添加下拉按钮
$(function () {
    let highest = 6;
    for (let i = 6; i >= 1; i--) {
        if ($('.toc-level-' + i).length != 0) {
            highest = Math.min(highest, i);
            //对存在的目录标签查询是否存在子目录
            for (let j = 0; j < $('.toc-level-' + i).length; j++) {
                if ($('.toc-level-' + i).eq(j).siblings('ul').length != 0) {
                    $('.toc-level-' + i).eq(j).after('<i class="iconfont fold-toc" onclick="TocHideOrShow(this);">&#xe616;</i>');
                }
            }
        }
        //查询最后一个toc标签是否具有子目录
        if ($(`.toc-level-${i}:last`).siblings('ul').html() == '') {
            $(`.toc-level-${i}:last`).siblings('i').remove();
        }
    }

    //隐藏除最高级外所有toc标签
    for (let i = 6; i > highest; i--) {
        $('.toc-level-' + i).closest('ul').hide();
    }
});

//展开或收缩下拉菜单
function TocHideOrShow(obj) {
    if ($(obj).siblings('ul').css('display') == 'none') {
        $(obj).addClass('reserve');
        $(obj).siblings('ul').slideDown(200);
    } else {
        $(obj).removeClass('reserve');
        $(obj).siblings('ul').slideUp(200);
    }
}

$(function () {
    //提取目录标签
    let tocTextArr = Array();
    let tocCount = $('.article-toc-content a').length;
    for (let i = 0; i < tocCount; i++) {
        let tocTemp = $('.article-toc-content a').eq(i).attr('href');
        tocTextArr.push(tocTemp.substr(1, tocTemp.length));
    }
    //监听滚动事件,操作停止20ms后执行
    $(window).scroll(function () {
        //clearTimeout() 方法可取消由 setTimeout() 方法设置的定时操作。
        clearTimeout($.data(this, 'scrollTimer'));
        $.data(this, 'scrollTimer', setTimeout(function () {
            let k = 0;
            $('.article-toc-content a').removeClass('active');
            //滚动到某一个标签的顶部距离-120px   ||  当前滚动距离+当前对象高度等于文档高度时（这只针对于最后一个标签）
            for (let i = 0; i < tocTextArr.length; i++) {
                if ($(window).scrollTop() > $(`a[name='${tocTextArr[i]}']`).offset().top - 120 ||
                    $(this).scrollTop() + $(this).height() == $(document).height()) {
                    //将之前自动打开的隐藏
                    // $(`a[href='#${tocTextArr[i]}']`).parents('ul:not([class])').hide();
                    $(`a[href='#${tocTextArr[i]}']`).parents('ul:not([class])').hide();
                    k = i;
                }
            }
            //将当前元素的父级全部打开下拉按钮
            $(`a[href='#${tocTextArr[k]}']`).parents('li').children('i').addClass('reserve');
            //将当前元素的所有前兄弟节点关闭下拉按钮
            $(`a[href='#${tocTextArr[k]}']`).parent('li').prevAll().children('i').removeClass('reserve');
            // $(`a[href='#${tocTextArr[k]}']`).siblings('i').addClass('reserve');

            //将当前打开的父级和兄弟显示
            $(`a[href='#${tocTextArr[k]}']`).parents('ul:not([class])').show();
            $(`a[href='#${tocTextArr[k]}']`).siblings('ul').slideDown(200);
            $(`.article-toc-content li a[href='#${tocTextArr[k]}'`).addClass('active');
        }, 20));
    });

    //点击目录标签滚动到对应指向位置
    $('.article-toc-content a').on("click", function () {
        //隐藏或显示子目录
        TocHideOrShow($(this).siblings('i'));
        scrollAppoint(80, this);

        //返回false不修改url，返回true修改url
        return false;
    });

    //滚动到评论区
    $('.aside-module a').on("click", function () {
        scrollAppoint(160, this);
        return false;
    });

    //滚动到评论编辑器
    $('.scroll-comment-editor').on("click", function () {
        let username = window.localStorage.getItem('account');
        if (username != null) {
            scrollAppoint(160, this);
        } else {
            cocoMessage.error("请登录后在评论", 3000);
        }
        return false;
    });
});

//页面滚动显示回到顶部按钮
$(window).scroll(function () {
    if ($(window).scrollTop() >= 100) {
        $('#retTop').fadeIn(400);
    } else {
        $('#retTop').fadeOut(400);
    }
    if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
        $('#retBottom').fadeOut(400);
    } else {
        $('#retBottom').fadeIn(400);
    }
});
//点击按钮回到顶部
$('#retTop').click(function () {
    $('html,body').animate({
        scrollTop: 0
    }, 500);
});

//点击按钮滚动到底部
$('#retBottom').click(function () {
    $('html,body').animate({
        scrollTop: $(document).height()
    }, 500);
});

//给所有的代码区添加一键复制按钮
$(function () {
    let len = $("#article-content pre").length;
    for (let i = 0; i < len; i++) {
        let item = $("#article-content pre").eq(i);
        item.css({
            "position": "relative"
        });
        item.append('<div class="copycode iconfont" result="" onclick="copyCode(this)" title="复制"></div>');
    }
});

function copyUrl() {
    navigator.clipboard.writeText(location.href).then(function () {
        cocoMessage.success("复制分享链接成功!", 3000);
    }, function () {
        cocoMessage.error("复制分享链接失败!请检查浏览器权限", 3000);
    });
}

//复制代码到剪贴板
function copyCode(obj) {
    let code = $(obj).parent('pre')[0].innerText + '\n\n';
    let author = "作者：" + $(".author").children('p').text() + '\n',
        link = "链接：" + window.location.href + '\n',
        source = "来源：武汉轻工大学算法协会\n",
        copyright = "著作权归作者所有。商业转载请联系作者获得授权，非商业转载请注明出处。\n";

    code += author + link + source + copyright;

    navigator.clipboard.writeText(code).then(function () {
        $(obj).css({ "color": "#00C853", "border-color": "#00c8534a" });
        $(obj).attr({ 'result': "", 'title': "复制成功" });
        setTimeout(function () {
            $(obj).css({ "color": "#333", "border-color": "#cbcbcb78" });
            $(obj).attr({ 'result': "", 'title': "复制" });
        }, 5000);
    }, function () {
        cocoMessage.error("复制代码失败!请检查浏览器权限", 3000);
    });
}