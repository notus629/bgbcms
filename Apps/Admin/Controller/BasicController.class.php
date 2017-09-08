<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/8/23
 * Time: 上午10:49
 */

namespace Admin\Controller;

use Think\Exception;

/*
 * 基本配置
 */

class BasicController extends CommonController
{

    public function __construct()
    {
        parent::__construct();

        // 一级导航设置
        parent::setFirstBreadCrumb([
            'name' => '基本配置',
            'url' => U('Admin/Basic/index')
        ]);
    }

    public function index()
    {
        // 二级导航设置
        parent::setSecondBreadCrumb([
            'name' => '基本配置',
            'url' => '',
        ]);

        $config = D('Basic')->select();

        $this->assign('config', $config);

//        var_dump($config);

        $this->assign('action', 0);
        $this->display();
    }


    /*
     * 配置保存到静态缓存
     */
    public function save()
    {
        // 数据检查
        if (empty($_POST)){
            return show(0, '提交数据为空!');
        }

        if (empty($_POST['title'])){
            return show(0, '标题不能为空!');
        }

        if (empty($_POST['adminTitle'])){
            return show(0, '标题不能为空!');
        }

        if (empty($_POST['keywords'])){
            return show(0, '关键字不能为空!');
        }

        if (empty($_POST['description'])){
            return show(0, '描述不能为空!');
        }


        $data = [
            'title' => $_POST['title'],
            'keywords' => $_POST['keywords'],
            'description' => $_POST['description'],
            'adminTitle' => $_POST['adminTitle'],
            'db-backup' => $_POST['db-backup'],
            'cache' => $_POST['cache'],
        ];

        //print_r($_POST);exit;
        try {
            if (false === D('Basic')->save($data)){
                return show(0, '保存配置信息失败!');
            }
        } catch (Exception $e){
            return show(0, $e->getMessage());
        }

        return show(1, '保存配置信息成功!');

    }

    public function cache()
    {
        // 二级导航设置
        parent::setSecondBreadCrumb([
            'name' => '缓存配置',
            'url' => '',
        ]);

        $this->assign('action', 1);
        $this->display();
    }


    /*
     * 立即生成缓存按钮操作
     */
    public function build()
    {
        return show(1);
    }

    /*
     * 立即更新按钮操作
     */
    public function backup()
    {
        // 要执行的 bash 命令
        $sh = 'mysqldump -u' . C('DB_USER') . ' -p' . C('DB_PWD') . ' ' . C('DB_NAME') . ' > /root/bgbcms' . date('YmdHis')  .'.sql';
//        dump(exec($sh));
        exec($sh);
//        dump($sh);
        return show(1);
    }
}
