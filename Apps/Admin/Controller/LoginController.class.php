<?php
/**
 * Created by PhpStorm.
 * User: llos
 * Date: 17/6/24
 * Time: 下午7:08
 */

namespace Admin\Controller;

use Think\Controller;
use Think\Exception;


class LoginController extends Controller
{
    /*
     * @ 登录表单
     */
    public function index()
    {
        if (!$_SESSION['username']){
            $this->display();
        } else {
            redirect(U('Admin/Index/index'));
        }
    }


    /*
     * 登录处理（验证等）
     */
    public function check()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            if (!($ret = D('Admin')->getPwdByName($username))){
//                dump($ret);exit;
                return show(0, '用户名不存在!');
            }

            if (!password_verify($password, $ret['password'])){
                return show(0, '密码错误!');
            }

            // 登录成功处理，（更新登录信息，如最后登录时间）
            if (!D('Admin')->updateLoginInfo($username)){
                return show(0, '登录信息无法记录!');
            }
        } catch (Exception $e){
            return show(0, $e->getMessage());
        }

        $_SESSION['username'] = $username;
//        $isAdmin = D('Admin')->isAdmin($_SESSION['username'])['isadmin'];
//        $_SESSION['isadmin'] = $isAdmin? 1 : 0;

        return show(1, '登录成功', U('Admin/Index/index'));
    }


    /*
     * 用户注册
     */
    public function logon(){

        // 注册表单显示
        if (!IS_POST){
            return $this->display();
        }

        $user = [
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'email' => !empty($_POST['email']) ?: 'nobody@nobody.com',
            'realname' => !empty($_POST['realname']) ?: 'nobody',
            'isadmin' => 0,     // 普通用户
        ];

        if (empty($user['username'])){
            return show(0, '用户名不能为空!');
        }

        if (empty($user['password'])){
            return show(0, '密码不能为空!');
        }

        // 检查用户名是否已存在
        try {

            if (!empty(D('Admin')->checkExist(['username' => $user['username']]))){
                return show(0, '用户名已存在!');
            }

            if (!D('Admin')->addUser($user)){
                return show(0, '注册失败!');
            }
        } catch (Exception $e) {
            return show(0, $e->getMessage());
        }

        return show(1, '注册成功');

    }


    /*
     * 退出登录
     */
    public function logout(){

        $_SESSION['username'] = NULL;
//        $_SESSION['isadmin'] = NULL;

        redirect(U('Admin/Login/index'));

    }
}
