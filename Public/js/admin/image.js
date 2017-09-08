/*
 * 图片上传相关处理
 */

$(function () {

    // 图片上传控件-Uploadify
    $('#upload-img-btn').uploadify({
        'swf': URL.image_upload_swf,
        'uploader': URL.image_upload,
        'buttonText': '上传图片',
        'fileTypeDesc': 'Image Files',
        'fileTypeExts': '*.png; *.jpg; *.gif',
        'fileObjName': 'article_img',
        'onUploadSuccess': function (file, data, response) {
            if (response) {
                var obj = JSON.parse(data);

                // 闪现的进度条后的'completed'换成'上传完毕'
                $('#' + file.id).find('.data').html('上传完毕');

                // 显示预览图，多张图片只显示最后一张
                $('#upload-img-display').attr('src', obj.data);
                $('#upload-img-src').attr('value', obj.data);
                $('#upload-img-display').show();
                console.log(obj.data);
                console.log(file);
            } else {
                alert('上传失败!');
            }
        }
    });


    // 文章编辑页面的缩略显示
    // 存在图片则显示缩略图，否则隐藏
    var $img = $('#upload-img-display');
    if ($img.attr('src')) {
        $img.show();
    } else {
        $img.hide();
    }


});