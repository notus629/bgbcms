<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/6/25
 * Time: 下午7:51
 * 公共函数
 */

/**
 * 检测是否已登录, 每个页面都需要检测
 */
function checkLogin()
{
    if($_SESSION['username']){
        return true;
    }

    return false;
}

function show($status, $msg, $url='', $data='')
{
    header('Content-type: application/json; charset=UTF-8');
    $data = [
        'status' => $status,
        'msg' => $msg,
        'url' => $url,
        'data' => $data,
    ];
    exit(json_encode($data));
}

/*
 * kindeidtor 返回的 json 数据格式
 */
function showKind($status, $msg)
{
    header('Content-Type: application/json; charset=UTF-8');

    if ($status == 0){//失败，这里比较奇怪，是因为 kindeditor 定义的 1 表示失败
        $data = [
            'error' => 1,
            'message' => $msg,
        ];
    } else { //成功
        $data = [
            'error' => 0,
            'url' => $msg,
        ];
    }
    exit(json_encode($data));
}

/*
 * 返回当前登录的用户名
 */
function getLoginName()
{
    return $_SESSION['username'] ?: '';
}

/*
 * 得到文章来源
 * @ $no 编号
 */
function getCopyFrom($no)
{
    return C('COPY_FROM')[$no];
}

/*
 * 是否是管理员
 */
function isAdmin()
{
    return D('Admin')->isAdmin($_SESSION['username'])['isadmin'];
}