<?php

/*
 * 个人中心设置
 */

namespace Admin\Controller;

use Think\Exception;


class PersonalController extends CommonController
{

    public function __construct()
    {
        parent::__construct();

        // 一级导航设置
        parent::setFirstBreadCrumb([
            'name' => '个人中心',
            'url' => U('Admin/Personal/index')
        ]);
    }

    public function index()
    {
        //  二级导航
        parent::setSecondBreadCrumb([
            'name' => '配置',
            'url' => '',
        ]);

        //TODO

        $username = $_SESSION['username'];

        try {

            if (false === ($user = D('Admin')->selectByName($username))){

                return $this->error('用户名不存在');

            }

        } catch (Exception $e) {

            return $this->error($e->getMessage());
        }

        $this->assign('user', $user);

        $this->display();
    }


    /*
     * '密码修改'页面
     */
    public function pwd()
    {
        //  二级导航
        parent::setSecondBreadCrumb([
            'name' => '密码修改',
            'url' => '',
        ]);

        $username = $_SESSION['username'];

        try {

            if (false === ($user = D('Admin')->selectByName($username))){

                return $this->error('用户名不存在');

            }

        } catch (Exception $e) {

            return $this->error($e->getMessage());
        }

        $this->assign('user', $user);

        $this->display();
    }


    /*
     * 个人信息修改
     */
    public function updateInfo()
    {
        // 表单数据检查
        $data['id'] = $_POST['id'];
        $data['realname'] = $_POST['realname'];
        $data['email'] = $_POST['email'];

        if (empty($data['email'])){
            return show(0, '邮箱不能为空!');
        }

        try {

            if (!D('Admin')->updateUser($data)){
                return show(0, '信息更新失败!');
            }
        } catch (Exception $e) {
            return show(0, $e->getMessage());
        }

        return show(1, '信息修改成功');
    }

    /*
     * 密码修改
     */
    public function updatePwd()
    {
//        dump($_POST);exit;

        $data['id'] = $_POST['id'];
        $oldpassword = $_POST['password'];
        $newpassword = $_POST['newpassword'];
        $newpassword2 = $_POST['newpassword2'];

        if (empty($oldpassword) || empty($newpassword) || empty($newpassword2)){
            return show(0,'密码不能为空!');
        }

        if (empty($data['id']) || !is_numeric($data['id']) || $data['id'] <= 0){
            return show(0, 'id 格式不正确!');
        }

        if ($newpassword2 !== $newpassword){
            return show(0, '两次密码不匹配!');
        }

        $data['password'] = password_hash($newpassword, PASSWORD_DEFAULT);

        try {
            if (false === ($pwd = D('Admin')->getPwd($data['id']))){
                return show(0, '密码查询失败!');
            }

            if (!password_verify($oldpassword, $pwd['password'])){
                return show(0, '原始密码不正确!');
            }

            //dump($data);exit;
            if (!D('Admin')->updatePwd($data)){
                return show(0, '密码更新失败!');
            }

        } catch (Exception $e) {

            return show(0, $e->getMessage());

        }

        // 登出
        $_SESSION['username'] = NULL;
//        $_SESSION['isadmin'] = NULL;
        return show(1, '密码更新成功，请重新登录!');

    }
}
