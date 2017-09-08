<?php

namespace Admin\Controller;

use Think\Controller;
use Think\Exception;

class MenuController extends CommonController
{
    /*
     * '管理菜单'页面的显示，
     * 选择'菜单类型'（前台或后台）后的处理
     */

    // 面包屑导航
//    private $breadNav = [];

    public function __construct()
    {
//        $this->breadNav['first'] = '菜单管理';
//        $this->breadNav['url'] = U('Admin/Menu/index');
        parent::__construct();

        parent::setFirstBreadCrumb([
            'name' => '菜单管理',
            'url' => U('Admin/Menu/index')
        ]);
    }

    public function index()
    {

//        if (!checkLogin()){
//            $this->redirect('Login/index');
//        }

        // 面包屑导航
//        $this->assign('nav', $this->breadNav);
        parent::setSecondBreadCrumb([
            'name' => '菜单列表',
            'url' => ''
        ]);

        $condition['type'] = $_GET['type'];     // 菜单类型，前台 or 后台
        $p = $_GET['p']?: 1;                    // 起始页，从1开始计数
        $pageSize = $_GET['pageSize']?: C('PAGE_SIZE');      // 每页显示几条


        // 选择显示前台、后台菜单

        // 默认显示所有类型菜单
        if(is_null($condition['type']) || !in_array($condition['type'], [-1, 0, 1]))
        {
            $condition['type'] = -1;
        }

        if (!is_numeric($p) || !is_numeric($pageSize)){
            $this->show('非法的 p 或 pageSize 参数!');
            return;
        }

        // 过滤的菜单类型
        $this->assign('menu_type', $condition['type']);

        // 获取所有菜单
        $hmenu = D('Menu');
        if($menus = $hmenu->getMenus($condition, $p, $pageSize)){
            $this->assign('menus', $menus);
        }

        /*
         * 分页显示
         */
        $menuCount      = $hmenu->getMenuCount($condition);
        $pageCount      = intval(ceil(floatval($menuCount) / floatval($pageSize) )); // 总页数
        $pageNoArray    = (!$pageCount)? [] : range(1, $pageCount);
        $this->assign('pagecnt', $pageNoArray);

        $this->assign('currentPage', $p);
        $this->assign('totalPage', $pageCount);

        $this->display(); // 输出模板

    }

    /*
     * 添加菜单表单显示
     */
    public function add()
    {
        // 面包屑导航
//        $this->breadNav['second'] = '添加';
//        $this->assign('nav', $this->breadNav);

        parent::setSecondBreadCrumb([
            'name' => '添加菜单',
            'url' => ''
        ]);

        $this->display();
    }

    /*
     * '添加菜单'页面'添加'按钮处理
     */
    public function addSubmit()
    {
        $data = $_POST;

        if(!$data['name']){
            return show(0, '菜单名不能为空!');
        }
        if(!$data['mname']){
            return show(0, '模块名不能为空!');
        }
        if(!$data['cname']){
            return show(0, '控制器名不能为空!');
        }
        if(!$data['aname']){
            return show(0, '方法名不能为空!');
        }

        if(!D('Menu')->addMenu($data)){
            return show(0, '添加菜单失败!');
        }

        return show(1, '添加菜单成功', U('Admin/Menu/index'));

    }

    /*
     * '编辑菜单'页面'保存'按钮处理
     */
    public function editSubmit()
    {
        $data = $_POST;

        if(!$data['name']){
            return show(0, '菜单名不能为空!');
        }
        if(!$data['mname']){
            return show(0, '模块名不能为空!');
        }
        if(!$data['cname']){
            return show(0, '控制器名不能为空!');
        }
        if(!$data['aname']){
            return show(0, '方法名不能为空!');
        }


        if( D('Menu')->saveMenu($data) === false){
            return show(0, '修改菜单失败!');
        }

        return show(1, '修改菜单成功', U('Admin/Menu/index'));


    }

    /*
    * 编辑菜单之前的判断
    */
    public function preEdit()
    {
        $id = $_GET['id'];
        if (!$id || !is_numeric($id)){
            return show(0, '菜单 id 不合法');
        }

        return show(1, '菜单编辑成功!', U('Admin/Menu/edit') . '?id=' . $_GET['id']);

    }

    /*
     * 编辑菜单表单显示
     */
    public function edit()
    {
        // 面包屑导航
//        $this->breadNav['second'] = '编辑';
//        $this->assign('nav', $this->breadNav);

        parent::setSecondBreadCrumb([
            'name' => '编辑菜单',
            'url' => ''
        ]);

        $id = $_GET['id'];
        $menu = D('Menu')->getMenu($id);

        $this->assign('menu', $menu);

        $this->display();
    }



    public function remove()
    {
        $id = $_GET['id'];

        if(!$id || !is_numeric($id)){
            return show(0, 'id 不合法');
        }

        if(D('Menu')->removeMenu($id) === false){
            return show(0, '删除失败!');
        }

        //echo M('Menu')->getLastSql();exit;
        return show(1, '删除成功!', U('Admin/Menu/index'));
    }

    /*
     * 更新排序
     */
    public function renewOrder()
    {
        //dump($_POST);
        $data = $_POST;

        if(D('Menu')->renewOrder($data)){
            return show(1, "更新排序成功!", U('Admin/Menu/index'));
        }

        return show(0, '排序无变化!');

    }

    /*
     * 点击更改菜单状态
     */
    public function statusChange()
    {
        //var_dump($_POST);
        $data['id'] = $_GET['id'];
        $data['status'] = $_GET['status'];

        //var_dump($data); exit;

        if(is_null($data['id']) || !is_numeric($data['id'])){
            return show(0, 'id 不合法');
        }

        if(is_null($data['status']) || !is_numeric($data['status'])){
            return show(0, '状态值不合法');
        }

        if(D('Menu')->changeMenuStatus($data) === false){
            return show(0, '变更状态失败!');
        }
        return show(1, '变更状态成功!', U('Admin/Menu/index'));

    }
}







































