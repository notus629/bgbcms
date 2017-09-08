$(function () {

    /*
     * 表单提交，包含以下按钮提交事件
     *   1. 菜单添加页面的 '添加' 按钮
     *   2. 菜单编辑页面的 '保存' 按钮
     *   3. 文章添加页面的 '添加' 按钮
     *   4. 文章编辑页面的 '保存' 按钮
     *   5. 推荐位编辑页面的 '提交' 按钮
     *   6. 推荐位内容编辑页面的 '提交' 按钮
     *   7. 添加用户页面的'添加'按钮
     */
    $('#bgbcms-btn-submit').click(function (e) {
        e.preventDefault();

        var ob = {};
        var data = {};

        ob = $('#bgbcms-submit-form').serializeArray();

        $(ob).each(function () {
            data[this.name] = this.value;
        });

        var url = URL.save_url;
        var jump_url = URL.jump_url;

        $.post(url, data, function (response) {
            if (response.status == 1) {
                return dialog.success(response.msg, jump_url);
            }
            return dialog.error(response.msg);
        }, 'json');

    });


    // '菜单管理'首页，选择显示前台或后台菜单
    // '文章管理'首页，选择显示某一栏目
    $('select#type-select').on('change', function (e) {

        var url = URL.typefilter + $(this).val();

        var title = $(':text[name=title]').val();

        if (title){
            url += '&title=' + title;
        }

        window.location.href = url;

    });

    // 点击'搜索标题'按钮
    $('#search-title').on('click', function(){

        var url = URL.typefilter + $('select#type-select').val();
        var title = $(':text[name=title]').val();

        if (title){
            url += '&title=' + title;
        }

        window.location.href = url;

    });

    // 更新排序
    $('a#renew-order').on('click', function (e) {

        e.preventDefault();

        var data = {};

        $('form :input').each(function (index) {

            var el = {};

            el['order'] = $(this).val();
            el['id'] = $(this).data('id');
            data[index] = el;

        });

        $.post(URL.reneworder, data, function (response) {

            if (response.status) {
                return dialog.success(response.msg, response.url);
            }
            return dialog.error(response.msg);

        }, 'json')
    });

    // 点击删除按钮
    $('a .glyphicon-remove-circle').on('click', function (e) {

        e.preventDefault();
        //console.log('hi');

        var $curbtn = $(this);

        dialog.confirm('确认删除该项？', function () {

            $.get($curbtn.parent().attr('href'), '', function (response) {

                if (response.status == 1) {
                    return dialog.success(response.msg, response.url);
                }

                return dialog.error(response.msg);

            }, 'json')

        });
    })

    // 菜单/文章/推荐位/推荐位内容列表状态颜色修改
    var $itemStatus = $('#item-table td.item-status');

    $itemStatus.each(function () {
        if ($.trim($(this).text()) == '正常') {
            $(this).addClass('text-success');
            $(this).removeClass('text-danger');
        } else {
            $(this).addClass('text-danger');
            $(this).removeClass('text-success');
        }
    });


    // 单击'状态'文字
    // 切换菜单状态
    // 切换文章状态
    // 切换推荐位和推荐位内容状态
    // 用户列表"状态"
    $itemStatus.on('click', function (e) {

        var id = $(this).data('id');
        var status = $(this).data('status');

        if( status != 1 && status != 0){
            return dialog.error('当前状态异常!');
        }
        // status = 1 - status;  // 改变状态
        status = status? 0 : 1;  // 改变状态

        var data = {id: id, status: status};

        $.get(URL.statuschange, data, function (response) {
        // $.get(URL.updateStatus, data, function (response) {

            if (response.status == 1) { // 数据库更改成功后，才改变前端显示的状态
                if (status === 0) {
                    $(this).text('关闭');
                    $(this).addClass('text-danger');
                    $(this).removeClass('text-success');
                } else {
                    $(this).text('正常');
                    $(this).addClass('text-success');
                    $(this).removeClass('text-danger');
                }
                return dialog.success(response.msg, response.url);
            }

            return dialog.error(response.msg);

        }, 'json');

    });

    // 分页按钮事件
    $("li a.pageno").each(function () {

        var pageno = $(this).text();

        // 点击任意'页数'的事件
        $(this).click(function (e) {
            e.preventDefault();

            var url = URL.index + "&p=" + pageno;

            var type = $('select#type-select').val();
            if (type){
                url += '&type=' + type;
            }

            var title = $(':text[name=title]').val();
            if (title){
                url += '&title=' + title;
            }

            window.location.href = url;
        });

    });

    // 上一页
    $("a#prev-page").click(function (e) {

        e.preventDefault();

        // 上一页的页码
        var pageno = $(this).data('cur') - 1;

        // 已到第一页
        if (pageno < 1) {
            return;
        }

        // var type = $('select#type-select').val();
        // var url = URL.index + "&type=" + type + "&p=" + pageno;
        // var title = $(':text[name=title]').val();
        //
        // if (title){
        //     url += '&title=' + title;
        // }

        var url = URL.index + "&p=" + pageno;

        var type = $('select#type-select').val();
        if (type){
            url += '&type=' + type;
        }

        var title = $(':text[name=title]').val();
        if (title){
            url += '&title=' + title;
        }

        window.location.href  = url;
    });


    // 下一页
    $("a#next-page").click(function (e) {
        e.preventDefault();
        // 下一页的页码
        var pageno = $(this).data('cur') + 1;

        // 已到最后一页
        if (pageno > $(this).data('total')) {
            return;
        }

        // var type = $('select#type-select').val();
        // var url = URL.index + "&type=" + type + "&p=" + pageno;
        // var title = $(':text[name=title]').val();
        //
        // if (title){
        //     url += '&title=' + title;
        // }

        var url = URL.index + "&p=" + pageno;

        var type = $('select#type-select').val();
        if (type){
            url += '&type=' + type;
        }

        var title = $(':text[name=title]').val();
        if (title){
            url += '&title=' + title;
        }

        window.location.href  = url;
    });

    // 点击编辑图标: 菜单/文章/推荐位/推荐位内容编辑
    $('a .glyphicon-edit').parent().on('click', function(e){
        e.preventDefault();

        var $editBtn = $(this);
        $.get($editBtn.attr('href'), function(response){
        //$.get($editBtn.parent().attr('href'), function(response){
            if(response.status == 0){
                return dialog.error(response.msg);
            }

            //return dialog.success(response.msg, response.url);
            window.location.href = response.url;

        }, 'json');

    });


    // 文章页 checkbox 按钮事件
    $('#select-item-all:checkbox').click(function(){

        var $check = $("[name=select-item]:checkbox");

        $check.prop('checked', this.checked);

    });


    // 根据每个 checkbox 来设置全选 checkbox
    $('[name=select-item]:checkbox').click(function(){

        var flag = true;

        // 此处不能用 $(this)，因为要循环所有的 checkbox
        $('[name=select-item]:checkbox').each(function(){

            if (!this.checked){
                flag = false;
            }

        });

        $('#select-item-all:checkbox').prop('checked', flag);
    });


    // 文章推送
    $('#push-article').click(function(){

        // 至少选中一篇文章进行推送
        var flag = false;
        var selectedArticles = [];

        $('[name=select-item]:checkbox').each(function(){

            if (this.checked){
                flag = true;
                selectedArticles.push($(this).val());
            }

        });

        if (!flag){
            return dialog.error('请选择文章进行推送!');
        }



        // 选择一个推荐位
        var featureId = 0;
        if ( 0 == (featureId = $('[name=feature] :selected').val())){
            return dialog.error('请选择一个推荐位!');
        }

        // console.log(selectedArticles);
        // console.log(featureId);

        var pushData = {
            articleIds: selectedArticles,
            featureId: featureId,
        };

        // console.log(pushData);

        $.post(URL.push, pushData, function(response){
            if (response.status == 0){
                return dialog.error(response.msg);
            }
            return dialog.success(response.msg);
        }, 'json');
    })

});










































