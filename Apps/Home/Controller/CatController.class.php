<?php

namespace Home\Controller;


/*
 * 分类页
 */
class CatController extends CommonController
{

    public function index()
    {
        //$id = intval($_GET['id']);
        $id = $_GET['id'];
        if(empty($id) || !is_numeric($id) || $id <= 0){
            return $this->error('ID 值不合法!');
        }

        // 只获取开启状态为正常(1)的菜单
        $navs = D('Menu')->getBarMenus();

        // 获取文章排行列表
        $ranks = D('Article')->getArticleRank(C('ARTICLE_RANK'));

        // 获取广告位文章
        $advs = D('FeatureContent')->getAdvs(C('ARTICLE_ADV'));


        // 分页输出
        $p = $_REQUEST['p'] ?: 1;
        $pageSize = $_REQUEST['pagesize'] ?: C('ARTICLE_LIST');
        $totalArticles = D('Article')->getArticleCount([
            'status' => 1,
            'thumb' => ['neq', ''],
            'catid' => $id,
        ]);
        $page  =  new \Think\Page($totalArticles, $pageSize);
        $pageRes = $page->show();

        // 获取文章列表
        $conditions = [
            'p' => $p,
            'pagesize' => $pageSize,
            'catid' => $id,
        ];
        $articles = D('Article')->getArticlelist($conditions);




        // 返回前端的数据
        $result = [
            'navs' => $navs,                    // 前台导航
            'articles' => $articles,            // 文章列表
            'ranks' => $ranks,                  // 文章排行表
            'advs' => $advs,                    // 广告位文章
            'catId' => $id,                     // 当前栏目ID
            'pageRes' => $pageRes,              // 分页信息
        ];

        $this->assign('result', $result);


        $this->display();
    }

}