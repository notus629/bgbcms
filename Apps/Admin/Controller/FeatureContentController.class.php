<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/8/15
 * Time: 下午9:13
 */

namespace Admin\Controller;

use \Think\Exception;


/*
 * 推荐位内容管理
 */
class FeatureContentController extends CommonController
{

    /*
     * 提取需要插入到数据库的推荐位内容
     */
    private function getInsertFeatureContent($rawData)
    {
        $data = [];
        $indices = ['title', 'feature_id', 'thumb', 'url', 'article_id', 'status'];
        $escapeIndices = ['title'];

        foreach ($rawData as $key => $value){

            if (in_array($key, $indices) && !is_null($value)){

                if (in_array($key, $escapeIndices)){

                    $data[$key] = htmlspecialchars($value);

                } else {

                    $data[$key] = $value;

                }
            }
        }

        return $data;

    }


    /*
     * 提取需要更新到数据库的推荐位内容
     */
    private function getUpdateFeatureContent($rawData)
    {
        $data = [];
        $indices = ['id', 'title', 'feature_id', 'thumb', 'url', 'article_id', 'status'];

        foreach ($rawData as $key => $value){

            if (in_array($key, $indices)){

                $data[$key] = $value;

            }
        }

        return $data;

    }


    public function __construct()
    {
        parent::__construct();

        parent::setFirstBreadCrumb([
            'name' => '推荐位内容管理',
            'url' => U('Admin/FeatureContent/index')
        ]);
    }

    public function Index()
    {
        // 二级面包屑导航
        parent::setSecondBreadCrumb([
            'name' => '推荐位内容列表',
            'url' => ''
        ]);

        // 条件1,2： 推荐位 ID 和标题搜索条件
        $conditions['type'] = $_GET['type']?: -1;
        $conditions['title'] = $_GET['title'];
        $this->assign('currentFeature', $conditions['type']);
        $this->assign('title', $conditions['title']);

        // 条件3,4： 起始页和页大小
        $conditions['p'] = $_GET['p']? : 1;
        $conditions['pagesize'] = $_GET['pagesize']? : C('PAGE_SIZE');
        $this->assign('currentPage', $conditions['p']);

        //print_r($conditions);echo('1--------<br/>');

        try{
            if (false === ($totalPage = D('FeatureContent')->getPages($conditions))){
                return $this->error('获取分页信息失败!');
            }

            if (false === ($featureContents = D('FeatureContent')->getFeatureContents($conditions))){
                return $this->error('获取推荐位内容失败');
            }

            if (!($featureNames = D('Feature')->getFeatureNames())){
                return $this->error('获取推荐位名称失败');
            }


        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }

        // 获取对应的推荐位名称
        foreach ($featureContents as &$value){
            foreach ($featureNames as $v){
                if ($v['id'] == $value['feature_id']){
                    $value['featureName'] = $v['name'];
                }
            }
        }

        //var_dump($featureContents);exit;

        $this->assign('totalPage', $totalPage); // 总页数

        // 页数数组，用于循环条件，为 0 则返回空数组
        $pageNoArray = (!$totalPage) ? [] : range(1, $totalPage);
        $this->assign('pagecnt', $pageNoArray);

        $this->assign('featureNames', $featureNames);
        $this->assign('featureContents', $featureContents);
        $this->display();
    }


    /*
     * 表单页：推荐位内容管理"添加页面"处理
     */
    public function add()
    {
        // 二级面包屑导航
        parent::setSecondBreadCrumb([
            'name' => '添加推荐位内容',
            'url' => ''
        ]);

        // 获取推所属荐位名称 - 选择推荐位
        try {
            $featureNames = D('Feature')->getFeatureNames();
        } catch (Exception $e) {
            return show(0, $e->getMessage());
        }

        $this->assign('features', $featureNames);

        $this->display();
    }


    /*
     * 保存新添加的 "推荐位内容"
     */
    public function insert()
    {
        // 取得推荐位内容
        $data = $this->getInsertFeatureContent($_POST);


        try {

            // 保存
            $ret = D('FeatureContent')->insert($data);

        } catch (Exception $e) {

            return show(0, $e->getMessage());

        }


        if (!$ret){

            return show(0, '新增推荐位内容失败!');

        }

        return show(1, '添加推荐位内容成功!');
    }

    /*
     * 编辑推荐位内容预处理（检查字段）
     */
    public function preEdit()
    {
        $id = $_GET['id'];

        if(!$id || !is_numeric($id)){
            return show(0, 'id 不合法！');
        }

        return show(1, '编辑成功!', U('Admin/FeatureContent/edit', ['id' => $id]));
    }

    /*
     * 表单页：编辑推荐位内容
     */
    public function edit()
    {
        // 二级面包屑导航
        parent::setSecondBreadCrumb([
            'name' => '编辑推荐位内容',
            'url' => ''
        ]);

        try {
            // 获取推所属荐位名称 - 选择推荐位
            $featureNames = D('Feature')->getFeatureNames();
            // 获取当前推荐位内容
            $featureContent = D('FeatureContent')->selectFeatureContent($_GET['id']);
        } catch (Exception $e) {
            return show(0, $e->getMessage());
        }

        $this->assign('features', $featureNames);
        $this->assign('featureContent', $featureContent);


        $this->display();
    }

    /*
     * 保存编辑的内容
     */
    public function update()
    {
        // 提取需要更新的推荐位内容
        $updateData = $this->getUpdateFeatureContent($_POST);

        try {
            $ret = D('FeatureContent')->update($updateData);
        } catch (Exception $e){
            return show(0, $e->getMessage());
        }

        if (!$ret){
            return show(0, '更新推荐位内容失败!');
        }

        return show(1, '更新推荐位内容成功!');
    }


    /*
     * 更新状态
     */
    public function updateStatus()
    {
        $data = $this->getStatusData($_GET);
        //var_dump($data);exit;

        try {
            if (!D('FeatureContent')->updateStatus($data)){
                return show(0, '状态更新失败!');
            }
        } catch (Exception $e){
            return show(0, $e->getMessage());
        }

        return show(1, '状态更新成功!');
    }


    /*
     * 更新排序
     */
    public function updateOrder()
    {
        $data = $this->getOrderData($_POST);

        try{
            if (!D('FeatureContent')->updateOrder($data)){
                return show(0, '更新排序成功!');
            }
        } catch (Exception $e){
            return show(0, $e->getMessage());
        }

        return show(1, '更新排序成功!', U('Admin/FeatureContent/index'));
    }


    /*
     * 删除推荐位内容
     */
    public function remove()
    {
        $id = $_GET['id'];

        try {
            if (!D('FeatureContent')->removeItem($id)){
                return show(0, '删除推荐位失败!');
            }
        } catch (Exception $e){
            return show(0, $e->getMessage());
        }

        return show(1, '删除推荐位成功!');
    }

}
