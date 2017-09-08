<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/8/20
 * Time: 下午7:43
 */

namespace Common\Model;

use Think\Model;
use Think\Exception;


/*
 * 自定义的模型公共类
 */
class CommonModel extends Model
{
    protected $cmsDb = '';
    protected $tableName = '';


    /*
     * 设置状态值
     */
    private function setStatus($id, $status)
    {
        $condition['id'] = $id;

        $data['status'] = $status;
        $data['update_time'] = time();

        return $this->cmsDb
            ->where($condition)
            ->save($data);
    }


    /*
     * 检查 ID 是否合法
     */
    private function checkId($id)
    {
        if (empty($id) || !is_numeric($id) || intval($id) <= 0){
            throw new Exception('ID 值不合法!');
        }
    }


    /*
     * 检查页面大小是否合法
     */
    private function checkPageSize($pageSize)
    {
        //var_dump($pageSize);exit;
        if (!isset($pageSize)){
            return false;
        }

        if (empty($pageSize) || !is_numeric($pageSize) || $pageSize <= 0){
            var_dump($pageSize);exit;
            throw new Exception('分页值大小不合法!');
        }
        //echo('------------');exit;
    }


    /*
     * 检查当前页码是否合法
     */
    private function checkPageNo($p)
    {
        if (!isset($p)){
            return false;
        }

        // 大于等于1
        if (empty($p) || !is_numeric($p) || $p <=0 ){
            throw new Exception('当前页码不正确!');
        }
    }


    /*
     * 检查分类搜索条件的合法性
     * $type == -1 表示无限制条件
     */
    private function checkType($type)
    {
        // 没有设置此条件
        if (!isset($type)){
            return false;
        }

        // $type 是一个 ID 值，限定其不为0，故可用 empty
        if (empty($type) || !is_numeric($type) || $type <= -2){
            throw new Exception('分类搜索条件设置错误!');
        }
    }

    /*
     * 检查标题模糊搜索条件的合法性
     */
    private function checkTitle($title)
    {
        // 没有设置此条件
        if (!isset($title)){
            return false;
        }

        // 可为空
        if (!preg_match('/^[a-zA-Z0-9]*$/', $title)){
            throw new Exception('搜索标题只能为字母或数字!');
        }
    }

    /*
     * 检查状态值是否合法
     */
    private function checkStatus($status)
    {
        // status 不为数字，或为数字时不等于0或1
        if (!is_numeric($status) || ($status != 1 && $status != 0)){
            throw new Exception('状态值不合法!');
        }
    }

    /*
     * 检查状态值是否合法
     */
    private function checkOrder($order)
    {
        // status 不为数字，或为数字时不等于0或1
        if (!is_numeric($order)){
            throw new Exception('排序值不合法!');
        }
    }


    /*
     * 待更新的状态数据检查
     */
    private function checkStatusArr($data)
    {
        $this->checkId($data['id']);
        $this->checkStatus($data['status']);
    }


    /*
     * 待更新的排序数据检查
     */
    private function checkOrderArr($data)
    {
        $this->checkDataArr($data);

        foreach ($data as $item){
            $this->checkId($item['id']);
            $this->checkOrder($item['order']);
        }
    }


    /*
     * 检查查询条件
     */
    private function checkConditions($cond)
    {
        //var_dump($cond);exit;
        $this->checkType($cond['type']);
        $this->checkTitle($cond['title']);
        $this->checkPageSize($cond['pagesize']);
        $this->checkPageNo($cond['p']);
    }


    /*
     * 常规数组数据检查
     */
    protected function checkDataArr($data)
    {
        if (!$data || !is_array($data)){
            throw new Exception('数据集不合法！');
        }
    }



    public function __construct($tableName)
    {
//        parent::__construct();

        $this->cmsDb = M($tableName);
        $this->tableName = $tableName;


    }


    /*
     * 取出所有条目
     */
    public function selectItems($fields, $conditions)
    {
        //print_r($conditions);echo('3--------<br/>');

        // 查询条件检查
        $this->checkConditions($conditions);


        // 条件设置
        if (isset($conditions['status'])){
            $where['status'] = $conditions['status'];
        } else {
            $where['status'] =  ['neq', -1];
        }

        // 针对推荐位类型, -1 即全部类型
        if (isset($conditions['type']) && $conditions['type'] != -1){
            $where['feature_id'] = $conditions['type'];
        }

        // 推荐位类型
        if (isset($conditions['feature_id'])){
            $where['feature_id'] = $conditions['feature_id'];
        }

        // 针对推荐位内容标题
        if (isset($conditions['title'])){
            $where['title'] = ['like', '%' . $conditions['title'] . '%'];
        }

        // 文章栏目类型
        if (isset($conditions['catid'])){
            $where['catid'] = $conditions['catid'];
        }

        // 缩略图条件
        if (isset($conditions['thumb'])){
            $where['thumb'] = $conditions['thumb'];
        }

        // 分页的起始页，每页大小
        $limit = '';
        if (isset($conditions['limit'])){
            $limit = $conditions['limit']; // 用于分页类
            //var_dump($conditions['limit']);exit;
        } else if (isset($conditions['p']) && isset($conditions['pagesize'])){
            $offset = ($conditions['p'] - 1) * $conditions['pagesize'];     // 当前页偏移
            $limit = $offset . ', ' . $conditions['pagesize'];
        }

//        var_dump($conditions['limit']);exit;

        //排序条件
        $order = '';
        if ($conditions['order']){
            $order  = $conditions['order'];
        } else {
            switch ($this->tableName){
                case 'article':
                    $order = '`listorder` desc, `article_id` desc';
                    break;
                case 'feature_content':
                    $order = '`order` desc, `id` desc';
                    break;
                default:
                    $order = '`id` desc';
                    break;
            }
        }


        return $this->cmsDb
            ->where($where)
            ->field($fields)
//            ->order($order . $id . 'desc')
            ->order($order)
            ->limit($limit)
            ->select();
    }

    /*
     * 取出指定 ID 条目
     */
    public function selectItemById($id, $fields = '')
    {
        $this->checkId($id);

        $condition['status'] = ['neq', -1];
        $condition['id'] = $id;

        return $this->cmsDb->where($condition)
            ->field($fields)
            ->find();
    }


    /*
     * 新增条目
     */
    public function insert($data)
    {
        $this->checkDataArr($data);

        $data['create_time'] = time();
        $data['update_time'] = time();

        return $this->cmsDb->add($data);
    }

    /*
     * 更新条目
     */
    public function update($data)
    {
        $this->checkDataArr($data);

        $data['update_time'] = time();

        return $this->cmsDb->save($data);
    }


    /*
     * 状态更新
     */
    public function updateStatus($data)
    {
        $this->checkStatusArr($data);
        $data['update_time'] = time();

        return $this->cmsDb->save($data);
    }


    /*
     * 切换（开关）当前状态
     * @param $status 当前状态
     */
    public function switchStatus($id, $status)
    {
        $status = $status? 0 : 1;
        return $this->setStatus($id, $status);
    }


    /*
     * 更新排序
     */
    public function updateOrder($data)
    {
        $this->checkOrderArr($data);

        $ret = 0;
        foreach ($data as $item){
            if ($this->cmsDb->save($item)){
                $ret++;
            }
        }

        return $ret;
    }

    /*
     * 删除某一条目（表中一行）
     */
    public function removeItem($id)
    {

        //$this->checkId($id);

//        $condition['id'] = $id;
//        $data['status'] = -1;
//
//        return $this->cmsDb
//            ->where($condition)
//            ->save($data);
        $status = -1;

        return $this->setStatus($id, $status);

    }


    /*
     * 得到分页数
     */
    public function getPages($conditions)
    {

        $this->checkType($conditions['type']);
        $this->checkTitle($conditions['title']);
        $this->checkPageSize($conditions['pagesize']);
       // print_r($conditions);echo('2--------<br/>');

        $pagesize = $conditions['pagesize'];

        $where['status']=  ['neq', -1];

        // 推荐位类型, -1 即全部类型
        if (isset($conditions['type']) && $conditions['type'] != -1){
            $where['feature_id'] = $conditions['type'];
        }

        // 推荐位内容标题
        if (isset($conditions['title'])){
            $where['title'] = ['like', '%' . $conditions['title'] . '%'];
        }


        $res  = $this->cmsDb->where($where)->count();

        if (false === $res){
            return $res;
        }

        // 若 $res 为 0，则返回 0， 即不显示页数
        return ceil($res / $pagesize);

    }


}