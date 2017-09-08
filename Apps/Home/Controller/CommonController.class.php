<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/8/23
 * Time: 下午4:12
 */

namespace Home\Controller;


use Think\Controller;

class CommonController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // 读取基本配置
        $metaConfig = D('Basic')->select();
        $this->assign('metaConfig', $metaConfig);
    }


    /*
     * 错误页面
     */
    public function error($msg = '')
    {
        $msg = $msg ?: '页面不存在!';

        $this->assign('msg', $msg);
        $this->display('Index/error');
    }
}