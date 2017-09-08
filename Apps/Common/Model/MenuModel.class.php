<?php
/**
 * Created by PhpStorm.
 * User: llos
 * Date: 17/7/5
 * Time: 下午5:37
 */

namespace Common\Model;

use Think\Model;


class MenuModel
{
    private $_db;

    public function __construct()
    {
        $this->_db = M('menu');
    }

    /*
     * 获取满足条件的菜单总数
     */
    public function getMenuCount($data = [])
    {
        // 查询所有类别
        if ($data['type']  == -1){
            unset($data['type']);
        }

        $data['status'] = ['neq', -1];

        $count = $this->_db->where($data)->count();

        return $count;
    }

    /*
     * 查询满足条件的所有菜单，带分页功能
     */
    public function getMenus($data , $p=1, $pageSize=2)
    {
        // type 为 -1 即选择所有类型
        if ($data['type'] == -1){
            unset($data['type']);
        }

        $data['status'] = ['neq', -1];

        $offset = ($p - 1) * $pageSize;

        $result = $this->_db->where($data)
            ->field('order, id, name, mname, type, status')
            ->limit($offset, $pageSize)
            //->order(['order' => 'asc', 'id' => 'desc'])
            ->order('`order` desc, `id` desc')
            ->select();

        return $result;
    }

    /*
     * 查询某个菜单的相关属性
     */
    public function getMenu($id)
    {
        $data['id'] = $id;

        $data['status'] = ['neq', -1];

        $result = $this->_db->where($data)
            ->field('id, name, type, mname, cname, aname, status')
            ->find();

        return $result;
    }

    /*
     * 添加新菜单到数据库
     */
    public function addMenu($data)
    {
        if(!$data || !is_array($data)){
            return false;
        }

        return $this->_db->add($data);
    }

    /*
     * 修改菜单
     */
    public function saveMenu($data)
    {
        if(!$data || !is_array($data)){
            return false;
        }

        return $this->_db->save($data);
    }

    /*
     * 更新菜单排序
     */
    public function renewOrder($data)
    {
        if(!$data || !is_array($data)){
            return false;
        }

        // 更新行数
        $retvalue = 0;

        foreach($data as $dt){
            if( $this->_db->save($dt)){
                $retvalue++;
            }
        }

        return $retvalue;
    }

    /*
     * 删除指定菜单
     */
    public function removeMenu($id)
    {
        // 即将菜单 status 置为 -1
        return $this->_db->where('id='.$id)->save(['status' => -1]);
    }

    /*
     * 变更菜单状态
     */
    public function changeMenuStatus($data)
    {
        return $this->_db->save($data);
    }

    /*
     * 获取所有前台栏目
     */
    public function getBarMenus()
    {
        $data = [
            'type' => 0,    // 前台菜单
            'status' => 1
        ];

        $res = $this->_db->where($data)
            ->field('name, id, aname')
            ->order(['order' => 'desc', 'id' => 'desc'])
            ->select();

        return $res;
    }

    /*
     * 根据 id 值获取前台某一栏目
     */
    public function getBarMenu($id)
    {
        $data = [
            'id' => $id,
            'status' => [
                'neq', -1
            ]
        ];

        return $this->_db->where($data)
            ->field('name')
            ->find();
    }
}




































