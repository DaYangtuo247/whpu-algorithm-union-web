//支持复制，粘贴，拖拽上传图片
function initPasteDragImg(Editor) {
    var doc = document.getElementById(Editor.id)
    doc.addEventListener('paste', function (event) {
        var items = (event.clipboardData || window.clipboardData).items;
        var file = null;
        var files = Array();
        if (items && items.length) {
            // 搜索剪切板items
            for (var i = 0; i < items.length && i < 10; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    file = items[i].getAsFile();
                    files.push(file);
                }
            }
        } else {
            // 当前浏览器不支持
            return;
        }
        if (!file) {
            // 粘贴内容非图片
            return;
        }
        for (let i = 0; i < files.length; i++) {
            uploadImg(files[i], Editor);
        }
    });

    var dashboard = document.getElementById(Editor.id)
    dashboard.addEventListener("dragover", function (e) {
        e.preventDefault()
        e.stopPropagation()
    })
    dashboard.addEventListener("dragenter", function (e) {
        e.preventDefault()
        e.stopPropagation()
    })
    dashboard.addEventListener("drop", function (e) {
        e.preventDefault()
        e.stopPropagation()
        var files = this.files || e.dataTransfer.files;
        for (let i = 0; i < files.length && i < 10; i++)
            uploadImg(files[i], Editor);
    })
}

//上传图片ajax
function uploadImg(file, Editor) {
    var formData = new FormData();
    var fileName = new Date().getTime() + "." + file.name.split(".").pop();
    formData.append('editormd-image-file', file, fileName);
    $.ajax({
        url: Editor.settings.imageUploadURL,
        type: 'post',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (msg) {
            var success = msg['success'];
            if (success == 1) {
                let imageNameRegx = /([\w\d]{13}\.[\w]+)/;
                Editor.insertValue("![" + msg["url"].match(imageNameRegx)[1] + "](" + msg["url"] + ")\n");
                cocoMessage.success(msg.message, 3000);
            } else {
                cocoMessage.error(msg.message, 3000);
            }
        }
    });
}