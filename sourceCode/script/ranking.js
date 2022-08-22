//a标签发送post请求
function post(url, param) {
    // 原理是创建一个临时不可见的form,通过form表单发送post请求
    let tmp = document.createElement("form");
    tmp.action = url;
    tmp.method = "post";
    tmp.style.display = "none";
    for (let item in param) {
        let ipt = document.createElement("input");
        ipt.type = "text";
        ipt.id = item;
        ipt.name = item;
        ipt.value = param[item];
        tmp.appendChild(ipt);
    }
    document.body.appendChild(tmp);
    tmp.submit();
    return tmp;
}

// 表格排序
function tableSort(Idx) {
    var table = document.getElementById('tableSort'),
        tbody = table.tBodies[0],//表格正文tbody
        tr = tbody.rows,//表格的行
        trValue = new Array();
    //将表格中各行的信息存储在新建的数组中
    for (var i = 0; i < tr.length; i++) {
        trValue[i] = tr[i];
    }
    //sortCol属性时额外给table添加的属性，用于作顺反两种顺序排序时的判断，区分首次排序和后面的有序反转
    if (tbody.sortCol == Idx) {//如果该列已经进行排序过了，则直接对其反序排列
        trValue.reverse();
    } else {
        trValue.sort(function (tr1, tr2) {
            //正则表达式去除空实心大拇指符号、html标签和空格的影响
            var value1 = tr1.cells[Idx].innerHTML.replace(/[]?<\/?[^>]*> ?/g, '');
            var value2 = tr2.cells[Idx].innerHTML.replace(/[]?<\/?[^>]*> ?/g, '');
            if (!isNaN(value1) && !isNaN(value2)) {
                // 数字排序
                return value1 - value2;
            } else {
                // 字符串排序
                return value1.localeCompare(value2);
            }
        });
    }
    //新建一个代码片段，用于保存排序后的结果
    var fragment = document.createDocumentFragment();
    for (var i = 0; i < trValue.length; i++) {
        fragment.appendChild(trValue[i]);
    }
    //将排序的结果替换掉之前的值
    tbody.appendChild(fragment);
    tbody.sortCol = Idx;
}