<?php

namespace Admin\Controller;

/*
 * 后台首页
 */
class IndexController extends CommonController {

    public function index(){

        try {
            // 今日登录用户数
            if (false === ($users = D('Admin')->getTodayLogins())){
                return $this->error('获取用户数错误！');
            }

            // 文章数量
            if (false === ($articles = D('Article')->getArticleCount(['status' => 1]))){
                return $this->error('获取文章数量错误!');
            }

            // 获取最大阅读数文章信息
            if (false === ($articleInfo = D('Article')->getReadMostArticle())){
                return $this->error('获取最大阅读数文章信息失败!');
            }

            // 推荐位数
            if (false === ($features = D('Feature')->getFeatureCount())){
                return $this->error('获取推荐位数错误!');
            }


        } catch (Exception $e){
            $this->error($e->getMessage());
        }


        // 返回前端的数据
        $result = [
            'users' => $users,
            'articles' => $articles,
            'reads' => $articleInfo['count'],
            'articleTitle' => $articleInfo['title'],
            'articleId' => $articleInfo['article_id'],
            'features' => $features,
        ];

        $this->assign('result', $result);

        $this->display();
    }
}