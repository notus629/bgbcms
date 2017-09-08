<?php

/*
 * 用户管理模型
 */

namespace Common\Model;


class AdminModel extends CommonModel
{
    public function __construct()
    {
        parent::__construct('user');
    }

    /*
     * 返回所有用户
     */
    public function getUsers()
    {
        $cond['status'] = ['neq', -1];
        return $this->cmsDb
            ->where($cond)
            ->field('id, username, email, status, lastlogintime')
            ->order('id desc')
            ->select();
    }


    /*
     * 查询指定用户的密码
     */
    public function getPwd($id)
    {
        $cond['status'] = 1;
        $cond['id'] = $id;

        return $this->cmsDb
            ->where($cond)
            ->field('password')
            ->find();
    }


    /*
     * 查询指定用户的密码（通过用户名）
     */
    public function getPwdByName($name)
    {
        $cond['status'] = 1;
        $cond['username'] = $name;

        return $this->cmsDb
            ->where($cond)
            ->field('password')
            ->find();
    }


    /*
     * 获取今日登录用户数
     */
    public function getTodayLogins()
    {
        $cond['status'] = 1;
        $cond['lastlogintime'] = ['between', [mktime(0, 0, 0), time()]];
//        dump($cond);exit;

        return $this->cmsDb
            ->where($cond)
            ->count();
    }



    /*
     * 单个用户状态更新
     */
    public function updateStatus($data)
    {
        $where['id'] = $data['id'];
        $save['status'] = $data['status'];

        return $this->cmsDb
            ->where($where)
            ->save($save);
    }


    /*
     * 指定用户普通信息修改
     */
    public function updateUser($data)
    {
        return $this->updateInfo($data);
    }


    /*
     * 指定用户密码修改
     */
    public function updatePwd($data)
    {
        return $this->updateInfo($data);
    }


    /*
     * 单个用户信息修改
     */
    public function updateInfo($data)
    {
        // 用户表中可能被更新的字段
        $fields = ['username', 'realname', 'email', 'password', 'isadmin'];

        $condition['id'] = $data['id'];
        $save['update_time'] = time();

        foreach ($data as $k => $v){

            if (in_array($k, $fields)){
                $save[$k] = $v;
            }

        }

        //dump($save);exit;

        return $this->cmsDb
            ->where($condition)
            ->save($save);

    }


    /*
     * 更新用户的登录信息
     */
    public function updateLoginInfo($username)
    {
        $cond['username'] = $username;
        $cond['status'] = 1;

        $data['lastlogintime'] = time();
        $data['update_time'] = time();
        $data['lastloginip'] = $_SERVER['REMOTE_ADDR'];

        return $this->cmsDb
            ->where($cond)
            ->save($data);
    }


    /*
     * 检查是否已存在
     * @param $data 格式: ['username' => <username>] or ['email' => <email>]
     */
    public function checkExist($data)
    {

        return $this->cmsDb
            ->where($data)
            ->field('id')
            ->find();
    }


    /*
     * 添加用户
     */
    public function addUser($data)
    {
        $data['create_time'] = time();
        $data['update_time'] = time();
//        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT, ['cost' => 12]);
        $data['status'] = 1;    // 状态：正常

        return $this->cmsDb->add($data);
    }


    /*
     * 查询用户信息，通过用户名
     */
    public function selectByName($username)
    {
        $condition['username'] = $username;
        $condition['status'] = 1;

        return $this->cmsDb
            ->where($condition)
            ->field('id, username, email, realname')
            ->find();
    }


    /*
     * 用户身份：是否是管理员
     */
    public function isAdmin($username)
    {
        $cond['username'] = $username;

        return $this->cmsDb
            ->where($cond)
            ->field('isadmin')
            ->find();
    }




}