<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/8/15
 * Time: 下午8:15
 *
 */

namespace Admin\Controller;

use Think\Controller;
use Think\Exception;

/*
 * 自定义的公共控制器类,所有自定义控制器类的基类
 */

class CommonController extends Controller
{
    // 面包屑导航
    protected $breadCrumb = [];

    // 基本配置
    //protected $config = [];

    /*
    * 进行初始化操作
    */
    private function init()
    {
        // 登录校验: 在访问每个后台页面前都须验证是否已登录
        if (!checkLogin()) {
            redirect(U('Admin/Login/index'));
        }

        $breadCrumb['first'] = [];    // 一级面包屑导航
        $breadCrumb['second'] = [];   // 二级面包屑导航

        //static::initBreadCrumb(); // 调用子类的方法

        // 读取基本配置
        $metaConfig = D('Basic')->select();
        $this->assign('metaConfig', $metaConfig);


    }


    /*
     * 检查 ID 值是否合法
     */
    private function checkId($id)
    {
        // id 值大于 0
        if (empty($id) || !is_numeric($id) || intval($id) <= 0){
            throw new Exception('id 值不合法');
        }

    }


    /*
     * 检查状态值是否合法
     */
    private function checkStatus($status)
    {
        // 状态值须为 0（关闭）或 1（开启）
        if (is_null($status) || !in_array($status, [0, 1])){
            throw new Exception('状态值不合法');
        }
    }


    /*
     *  一级面包屑导航设置
     */
    protected function setFirstBreadCrumb($data = [])
    {
        $this->breadCrumb['first'] = [
            'name' => $data['name'],
            'url' => $data['url']
        ];
    }

    /*
    * 二级面包导航设置, 同时将值传给前端
    */
    protected function setSecondBreadCrumb($data = [])
    {
        $this->breadCrumb['second'] = [
            'name' => $data['name'],
            'url' => $data['url']
        ];

        $this->assign('breadCrumb', $this->breadCrumb);
    }

    /*
     * 提取状态更新的相关变量
     */
    protected function getStatusData($data)
    {
        $ret = [];

        if (isset($data['id']) && isset($data['status'])){
            $ret['id'] = $data['id'];
            $ret['status'] = $data['status'];
        }

        return $ret;
    }


    /*
     * 提取排序更新的相关变量
     */
    protected function getOrderData($data)
    {
        $ret = [];
        $item = [];

        if (empty($data) || !is_array($data)){
            return $ret;
        }

        foreach ($data as $value) {
            if (isset($value['id']) && isset($value['order'])) {
                $item['id'] = $value['id'];
                $item['order'] = $value['order'];
            }

            array_push($ret, $item);
        }

        return $ret;
    }


    /*
     * 删除表中一条记录
     * @param $model 对应的模型名称
     */
    protected function removeItem($model)
    {
        try {

            $id = $_GET['id'];
            $this->checkId($id);

            if (!D($model)->removeItem($id)){
                return show(0, '删除条目失败!');
            }

        } catch (Exception $e) {

            return show(0, $e->getMessage());

        }

        return show(1, '删除成功!');

    }


    /*
     * 状态变更
     */
    protected function switchStatus($model)
    {
        $id = $_GET['id'];
        $status = $_GET['status'];

        // TODO, 代码修正后删除
        $status = $status? 0 : 1;

        try {

            $this->checkId($id);
            $this->checkStatus($status);

            if (false === D($model)->switchStatus($id, $status)){
                return show(0, '状态变更失败');
            }

        } catch (Exception $e){
            return show(0, $e->getMessage());
        }

        return show(1, '状态变更成功!', U('Admin/' . $model . '/index'));

    }


    public function __construct()
    {
        parent::__construct();
        $this->init();

    }



}