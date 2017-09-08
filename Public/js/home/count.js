/*
 * 阅读数
 */

var data = {};

$('.news_count').each(function(i){

    data[i] = $(this).attr('news-id');

});

//console.log(data);

$.post(URL.post, data, function(result){

    if (result.status == 1){

        var counts = result.data;
        // console.log(counts);

        $.each(counts, function(index, value){
            $('.node-' + index).html(value);
        })

    } else {
        console.log('无法获取阅读数据!');
    }

}, 'json');