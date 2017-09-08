<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 如果是通过命令行来访问
if (isset($argv)){
    // 参数个数要为 4：脚本名，模块，控制器，操作
    if ($argc == 4){
        $_GET['m'] = $argv[1] ?: 'Home';
        $_GET['c'] = $argv[2] ?: 'Index';
        $_GET['a'] = $argv[3] ?: 'index';

        define('APP_CRONTAB', 1);    // 进行缓存,定义常亮标记是否为计划任务脚本
        //var_dump($argv);exit;
    } else {
        die("命令行参数错误!\n");
    }
}

//var_dump($argv);

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

// 绑定 Admin 模块到当前入口文件
//define('BIND_MODULE', 'Admin');

// 定义应用目录
define('APP_PATH','./Apps/');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单
