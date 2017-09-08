<?php

namespace Home\Controller;

use Think\Exception;


class IndexController extends CommonController
{

    public function index($type='')
    {

        // 只获取开启状态为正常(1)的菜单
        $navs = D('Menu')->getBarMenus();

        // 获取推荐大图（默认取1个）
        $bigFeatures = D('FeatureContent')->getBigFeatures();

        // 获取推荐位小图（默认取3个）
        $smallFeatures = D('FeatureContent')->getsmallFeatures();


        /*
         * 分页
         */
        $p = $_REQUEST['p'] ?: 1;
        $pageSize = $_REQUEST['pagesize'] ?: C('ARTICLE_LIST');
        $totalArticles = D('Article')->getArticleCount([
            'status' => 1,
            'thumb' => ['neq', ''],
        ]);
        $page  =  new \Think\Page($totalArticles, $pageSize);
        $pageRes = $page->show();
//        var_dump($totalArticles);exit;
        // 获取文章列表
        $conditions = [
            'p' => $p,
            'pagesize' => $pageSize,
//            'limit' => $page->firstRow . ', ' . $page->listRows,
        ];
        //echo 1;
//        var_dump($conditions);exit;
//        var_dump($pageRes);exit;
        $articles = D('Article')->getArticlelist($conditions);


        // 获取文章排行列表
        $ranks = D('Article')->getArticleRank(C('ARTICLE_RANK'));

        // 获取广告位文章
        $advs = D('FeatureContent')->getAdvs(C('ARTICLE_ADV'));



        // 返回前端的数据
        $result = [
            'catId' => 0,                       // 首页类别 ID
            'navs' => $navs,                    // 前台导航
            'bigPic' => $bigFeatures[0],        // 大图推荐, 数组中只有一个
            'smallPic' => $smallFeatures,       // 小图推荐
            'articles' => $articles,            // 文章列表
            'ranks' => $ranks,                  // 文章排行表
            'advs' => $advs,                    // 广告位文章
            'pageRes' => $pageRes,              // 分页
        ];

        $this->assign('result', $result);
//        var_dump($result);exit;

//        if ('BUILD_HTML' == $type){
//            $ret = parent::buildHtml('index', dirname($_SERVER['SCRIPT_FILENAME']) . '/', 'Index/index');
//        } else {
//            $this->display();
//        }

        // 是否开启定时缓存任务
        $config = D('Basic')->select();
        $ifCache = $config['cache'];

        // 计划任务
        //var_dump(APP_CRONTAB);
        if (defined('APP_CRONTAB') && $ifCache){
            $this->buildHtml('index', dirname($_SERVER['SCRIPT_FILENAME']) . '/', 'Index/index');
            echo "构建静态化首页成功!\n";
        } else {
            $this->display();
        }

    }


//    /*
//     * 生态静态首页
//     */
//    public function cache()
//    {
//        $this->index('BUILD_HTML');
//        echo '构建静态化首页成功!';
//    }

    /*
     * 阅读数
     */
    public function getCount()
    {
        if (!$_POST){
            return show(0, 'no data posted');
        }
//        var_dump($_POST);exit;

        // 防止 ID 重复（推荐位和文章列表 ID 可能重复）
        $ids = array_unique($_POST);

        try {
            $ret = D('Article')->getArticlesByIds($ids);
        } catch (Exception $e){
            return show(0, $e->getMessage());
        }

        $data = [];
        foreach ($ret as $k => $v){
            $data[$v['article_id']] = $v['count'];
        }

        return show(1, 'ok', '', $data);

    }

}