<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/8/15
 * Time: 下午9:12
 */

namespace Admin\Controller;

/*
 * 推荐位管理
 */
use Think\Exception;

class FeatureController extends CommonController
{
    /*
     * 提取需要保存的更新信息
     */
    private function getUpdateData($data)
    {
        $ret = [];
        $fields = ['id', 'name', 'description', 'status']; // 要保存的字段

        if (!$data || !is_array($data)){
            return $ret;
        }

        foreach ($data as $key => $value){
            if (in_array($key, $fields)){
                $ret[$key] = $value;
            }
        }

        return $ret;

    }

    public function __construct()
    {
        parent::__construct();

        // 一级面包屑导航设置
        parent::setFirstBreadCrumb([
            'name' => '推荐位管理',
            'url' => U('Admin/Feature/index')
        ]);

    }


    /*
     * 推荐位管理首页
     */
    public function index()
    {
        // 二级面包屑导航设置
        parent::setSecondBreadCrumb(['name'=>'推荐位列表', 'url'=>'']);

        // 查询条件1、2：当前页和页面大小
        $conditions['pagesize'] = $_GET['pagesize']?: C('PAGE_SIZE');
        $conditions['p'] = $_GET['p']?: 1;
        $this->assign('currentPage', $conditions['p']);


        //var_dump($conditions);exit;
        try {
            $features = D('Feature')->getFeatures($conditions);   // 取得所有推荐位
            $totalPage = D('Feature')->getPages($conditions);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }

        if ($features === false){
            $this->error('获取推荐位列表失败!');
        }

        if (false === $totalPage){
            return $this->error('获取分页数失败!');
        }

        //TODO
        // 总页数
        $this->assign('totalPage', $totalPage);

        // 传给前端的页数数组
        $pageNoArray = $totalPage? range(1, $totalPage) : [];
        $this->assign('pagecnt', $pageNoArray);

        $this->assign('features', $features);

        $this->display();
    }


    /*
     * "添加推荐位"表单页面
     */
    public function add()
    {
        // 二级面包屑导航设置
        parent::setSecondBreadCrumb(['name'=>'添加推荐位', 'url'=>'']);

        $this->display();

    }

    /*
     * 保存新添加的推荐位
     */
    public function insert()
    {
        try{
            if (!D('Feature')->insert($_POST)){
                return show(0, '新增推荐位失败!');
            }
        } catch(Exception $e) {
            return show(0, $e->getMessage());
        }

        return show(1, '新增推荐位成功!');

    }

    /*
     * 改变状态
     */
    public function statusChange()
    {

        if (!$_GET['id'] || !is_numeric($_GET['id'])){
            return show(0, 'id 格式不正确!');
        }

        if (($_GET['status'] != '0' && $_GET['status'] != '1') || !is_numeric($_GET['status'])){
            return show(0, '状态非法!');
        }

        $data = [
            'id' => $_GET['id'],
            'status' => $_GET['status']
        ];

        if (!D('Feature')->updateStatus($data)){
            return show(0, '状态变更失败!');
        }



        return show(1, '状态变更成功!');

    }

    /*
     * 编辑"推荐位"之 id 检查
     */
    public function preEdit()
    {
        $id = $_GET['id'];

        if (!$id || !is_numeric($id)){
            return show(0, '请求 ID 非法!');
        }

//        $this->redirect('Feature/edit', ['id' => $id]);
//        redirect(U('Admin/Feature/edit') . '&id='. $id);
        return show(1, '编辑推荐位成功!', U('Admin/Feature/edit') . '?id=' . $id);

    }

    /*
     * 编辑 "推荐位" 页面处理
     */
    public function edit()
    {
//        var_dump($_GET);exit;

        if (!($feature = D('Feature')->getFeature($_GET['id']))){
            return $this->error('数据库查询失败!');
        }

        // 二级面包屑导航
        parent::setSecondBreadCrumb([
            'name' => '编辑推荐位',
            'url' => ''
        ]);

        //var_dump($feature);exit;
        $this->assign('feature', $feature);
        $this->display();
    }

    /*
     * 更新/保存修改的"推荐位"信息
     */
    public function update()
    {
        //var_dump($_POST);exit;

        $data = $this->getUpdateData($_POST);

        //var_dump($data);exit;

        try {
            if (!D('Feature')->update($data)){
                return show(0, '推荐位信息保存失败!');
            }

            return show(1, '推荐位信息保存成功');

        } catch (Exception $e) {

            return show(0, $e->getMessage());

        }
    }


    /*
     * 删除某一推荐位
     */
    public function remove()
    {
        if (!D('Feature')->removeItem($_GET['id'])){
            return show(0, '删除失败!');
        }

        return show(1, '删除推荐位成功!', U('Admin/Feature/index'));
    }


}
