<?php

namespace Home\Controller;

use Think\Exception;


/*
 * 文章详情页
 */

class DetailController extends CommonController
{
    public function index()
    {
        // 文章 ID
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

        // 获取当前文章
        $article = D('Article')->getArticleById([
            'id' => $id,
            'status' => 1,
        ]);

        if (!$article){
            return $this->error('获取文章信息失败!');
        }

        // 获取当前文章内容
        $content = D('ArticleContent')->getContent($id);

        if (!$content){
            return $this->error('获取文章内容失败!');
        }

        // 阅读文章：文章阅读次数 + 1
        try {
            if (!D('Article')->readArticle($id)){
                return $this->error('文章阅读次数更新失败');
            }
        } catch (Exception $e){
            return $this->error($e->getMessage());
        }


        $text = htmlspecialchars_decode($content['content']);


        // 返回前端的数据
        $result = [
            'navs' => $navs,                    // 前台导航
            'title' => $article['title'],       // 文章标题
            'titleColor' => $article['title_font_color'], // 文章标题颜色
            'content' => $text,                 // 文章内容
            'ranks' => $ranks,                  // 文章排行表
            'advs' => $advs,                    // 广告位文章
            'catId' => $article['catid'],       // 当前栏目ID
        ];


        $this->assign('result', $result);

        $this->display('Detail/index');
    }

    /*
     * 后台的预览
     */
    public function view()
    {
        if (!checkLogin()){
            return $this->error('您没有权限访问该页面!');
        }

        $this->index();
    }
}