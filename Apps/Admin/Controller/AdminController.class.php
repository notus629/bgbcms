<?php

/*
 * 用户管理，登录、注册
 */

namespace Admin\Controller;

use Think\Exception;


class AdminController extends CommonController
{
    public function __construct()
    {
        parent::__construct();

        // 一级导航设置
        parent::setFirstBreadCrumb([
            'name' => '用户管理',
            'url' => U('Admin/Admin/index')
        ]);
    }


    public function index()
    {
        //  二级导航
        parent::setSecondBreadCrumb([
            'name' => '用户列表',
            'url' => '',
        ]);

        $users = [];
        try {
            // 数据库操作错误
            if (false === ($users = D('Admin')->getUsers())){
                return false;
            }
        } catch (Exception $e) {
//            echo $e->getMessage();
            return $e->getMessage();
        }
//        dump($users);

        $this->assign('users', $users);
        $this->display();
    }


    /*
     * "添加用户"表单
     */
    public function add()
    {
        //  二级导航
        parent::setSecondBreadCrumb([
            'name' => '新增用户',
            'url' => '',
        ]);

        $this->display();
    }


    /*
     * '添加用户'表单页面提交
     */
    public function addSubmit()
    {
        // 表单数据检查
        $data['username'] = $_POST['username'];
        $data['password'] = $_POST['password'];
        $data['email'] = $_POST['email'];
        $data['isadmin'] = 1;     // 此处添加的为管理员账户

        if (empty($data['username'])){
            return show(0, '用户名不能为空!');
        }

        if (empty($data['email'])){
            return show(0, '邮箱不能为空!');
        }

        if (empty($data['password'])){
            return show(0, '密码不能为空!');
        }


        // 检查用户名、邮箱是否已存在
        try {

            if (!empty(D('Admin')->checkExist(['username' => $data['username']]))){
                return show(0, '用户名已存在!');
            }

            if (!empty(D('Admin')->checkExist(['email' => $data['email']]))){
                return show(0, '该邮箱已注册!');
            }

            if (!D('Admin')->addUser($data)){
                return show(0, '添加用户失败!');
            }
        } catch (Exception $e) {
            return show(0, $e->getMessage());
        }

        return show(1, '添加用户成功');
    }


    /*
     * 删除指定用户
     */
    public function removeItem()
    {

        return parent::removeItem('Admin');

    }


    /*
     * 切换指定用户的状态（正常到关闭状态之间转换，0变1 或 1变0)
     */
    public function switchStatus()
    {
        return parent::switchStatus('Admin');
    }

}