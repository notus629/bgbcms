/**
 * Created by llos on 17/6/24.
 * Front-end login class
 */

$(function(){

    var login = {

        // 提交登录
        submit: function () {
            //alert(1);
            //dialog.error('失败', location.href);
        }
    };

    // 登录按钮处理
    $('#loginBtn').on('click', function (e) {
        e.preventDefault();

        // 读取用户输入
        var username = $('input[name="username"]').val();
        var password = $('input[name="password"]').val();

        // 不能为空检查
        if(!username.trim()){
            return dialog.error('用户名不能为空!');
        }
        if(!password.trim()){
            return dialog.error('密码不能为空!');
        }

        // 编码成 JSON 格式数据
        var data = {
            'username': username,
            'password': password,
        }
        //console.log(data);

        // 应用地址
        var appURL = $('#loginBtn').data('url');
        // 后台模块地址
        var adminURL = appURL + '?m=Admin';
        // 后台首页
        var firstPage = adminURL + '&c=Index&a=index';
        // 后台登录页
        var chkURL = adminURL + '&c=Login&a=check';

        //console.log(adminURL);

        // 执行异步 Ajax 请求
        $.post(chkURL, data, function(response){
            if(response.status == 1){
                return dialog.success(response.msg, response.url);
            } else if(response.status == 0){
                return dialog.error(response.msg);
            }

        }, 'json');
    });


    // 注册
    $('#logonBtn').on('click', function (e) {
        e.preventDefault();

        // 读取用户输入
        var username = $('input[name="username"]').val();
        var password = $('input[name="password"]').val();
        var password2 = $('input[name="password2"]').val();
        var email = $('input[name="email"]').val();
        var realname = $('input[name="realname"]').val();

        // 不能为空检查
        if(!username.trim()){
            return dialog.error('用户名不能为空!');
        }
        if(!password.trim()){
            return dialog.error('密码不能为空!');
        }
        if(!password2.trim()){
            return dialog.error('重复密码不能为空!');
        }

        // 密码一致性检查
        if (password !== password2){
            return dialog.error('两次密码输入不一致');
        }

        // 编码成 JSON 格式数据
        var data = {
            'username': username,
            'password': password,
        }

        if (email.trim()){
            data['email'] = email;
        }

        if (realname.trim()){
            data['realname'] = realname;
        }
        // console.log(data);

        // // 应用地址
        // var appURL = $('#logonBtn').data('url');
        // // 后台模块地址
        // var adminURL = appURL + '?m=Admin';
        // // 后台首页
        // var firstPage = adminURL + '&c=Index&a=index';
        // // 后台注册页
        // var logonURL = adminURL + '&c=Login&a=logon';
        //
        // console.log(adminURL);

        // 执行异步 Ajax 请求
        $.post(URL.logon_url, data, function(response){

            if(response.status == 1){
                return dialog.success(response.msg, URL.login_url);
            }

            return dialog.error(response.msg);

        }, 'json');
    });

});








































