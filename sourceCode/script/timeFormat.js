/*
*  把传入的时间戳与当前时间比较,计算几分钟前、几小时前、几天前等等
*/

//文章描述时间显示
function getDateTimeFormat(unixtime) {
    var currTime = Date.parse(new Date());
    var time = ((parseInt(currTime) / 1000) - parseInt(unixtime));

    // 少于60秒
    if (time < 60) {
        return "刚刚";
    }

    // 少于60分钟
    var minuies = time / 60;
    if (minuies < 60) {
        return Math.floor(minuies) + "分钟前";
    }

    // 少于24小时 
    var hours = time / 3600;
    if (hours < 24) {
        return Math.floor(hours) + "小时前";
    }
    // 少于三天
    var days = time / 3600 / 24;
    if (days <= 3) {
        return Math.floor(days) + "天前";
    }
    //少于12月
    var months = time / 3600 / 24 / 30;
    if (months < 12) {
        return moment.unix(unixtime).format('M月D日');
    }
    // 大于1年
    return moment.unix(unixtime).format('YYYY年M月D日');
};

//评论及回复时间显示
function getDateTimeFormatComment(unixtime) {
    var currTime = Date.parse(new Date());
    var time = ((parseInt(currTime) / 1000) - parseInt(unixtime));

    // 少于60秒
    if (time < 60) {
        return "刚刚";
    }

    // 少于60分钟
    var minuies = time / 60;
    if (minuies < 60) {
        return Math.floor(minuies) + "分钟前";
    }

    // 少于3小时 
    var hours = time / 3600;
    if (hours <= 3) {
        return Math.floor(hours) + "小时前";
    }

    // 大于3小时小于12个月
    var months = time / 3600 / 24 / 30;
    if (months <= 12) {
        return moment.unix(unixtime).format('M月D日 hh:mm');
    }

    // 大于1年
    return moment.unix(unixtime).format('YYYY年M月D日 hh:mm');
};