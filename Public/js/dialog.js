/**
 * Created by Notus on 17/6/24.
 * 弹出层封装
 */

$(function () {

    dialog = {

        // 失败弹出层
        error: function (message) {
            layer.open({
                title: '错误提示',
                content: message,
                icon: 2,
            });
        },

        // 成功弹出层
        success: function (message, url) {
            layer.open({
                title: '完成',
                content: message,
                icon: 1,
                yes: function () {
                    location.href = url? url : location.href;
                }
            })
        },

        // 确认弹出层
        confirm: function (message, callback) {
            layer.open({
                title: '请确认',
                content: message,
                btn: ['是', '否'],
                yes: function () {
                    callback();
                },

            })
        }

    }

});