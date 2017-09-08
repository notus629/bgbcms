<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/7/31
 * Time: 下午2:32
 */

namespace Common\Model;

use Think\Model;


//class ArticleModel extends Model
class ArticleModel extends CommonModel
{

    public function __construct()
    {

        parent::__construct('article');

    }


    /*
     * 插入文章/新闻数据库中
     */
    public function insert($data)
    {
        $data['username'] = getLoginName();     // 插入当前用户名

        return parent::insert($data);
    }


    /*
     * 获取文章内容列表
     */
    public function getArticles($data='', $p=1, $pagesize=2)
    {
        $data['status'] = ['neq', -1];
        $offset = ($p - 1) * $pagesize;

        return $this->cmsDb->where($data)
            ->field(['listorder', 'article_id', 'title', 'catid', 'copyfrom', 'thumb', 'create_time', 'status', 'keywords'])
            ->limit($offset, $pagesize)
            ->order('listorder asc, article_id desc')
            ->select();
    }


    /*
     * 获取文章列表。条件：缩略图不为空，状态为正常
     */
//    public function getArticleList($num = 10, $catId = 0)
    public function getArticleList($data)
    {
        $fields = 'article_id, thumb, title, description, keywords, create_time, count';

        $conditions = [
            'status' => 1,
            'p' => $data['p'],
            'pagesize' => $data['pagesize'],
            'thumb' => ['neq', ''],
            'limit' => $data['limit'],
        ];

        //var_dump($conditions);exit;
        // 文章栏目
        if (isset($data['catid'])){
            $conditions['catid'] = $data['catid'];
        }

        return $this->selectItems($fields, $conditions);

    }

    /*
     * 获取文章排行。
     * @param $num 获取的排行文章数
     */
    public function getArticleRank($num = 10)
    {
        $fields = 'article_id, title, description, small_title';

        $conditions = [
            'status' => 1,
            'p' => 1,
            'pagesize' => $num,
            'order' => '`count` desc, `article_id` desc',   // 排序
        ];

//        $conditions['p'] = 1;
//        $conditions['pagesize'] = $num;
//        $conditions['order'] = '`count` desc, `article_id` desc';

        return $this->selectItems($fields, $conditions);
    }


    /*
     * 获取单条文章信息
     */
    public function getArticle($id='')
    {
        $data['article_id'] = $id;

        return $this->cmsDb->where($data)
            ->field('title, small_title, thumb, title_font_color, catid, copyfrom, description, keywords')
            ->find();
    }

    /*
     * 获取单条文章信息
     */
    public function getArticleById($conditions)
    {
        $data['article_id'] = $conditions['id'];
        $data['status'] = $conditions['status'];

        return $this->cmsDb->where($data)
            ->field('title, small_title, thumb, title_font_color, catid, copyfrom, description, keywords')
            ->find();
    }


    /*
     * 获取多条文章的阅读数信息
     */
    public function getArticlesByIds($ids)
    {
        $data = [];

        foreach ($ids as $v){
            $where['article_id'] = $v;
            $data[] = $this->cmsDb->where($where)->field('article_id, count')->find();
        }

        return $data;
    }



    /*
     * 更新排序
     */
    public function renewOrder($data='')
    {
        if (!$data || !is_array($data)) {
            return false;
        }

        // 更新行数
        $ret = 0;

        foreach ($data as $dt) {
            if ($this->cmsDb->save($dt)) {
                $ret++;
            }
        }

        return $ret;
    }

    /*
     * 获取总文章数 (status != -1, 默认关闭的文章也显示，便于后台管理）
     */
    public function getArticleCount($conditions=[])
    {
        if(isset($conditions['status'])){
            $data['status'] = $conditions['status'];
        } else {
            $data['status'] = ['neq', -1];
        }

        if (isset($conditions['thumb'])){
            $data['thumb'] = $conditions['thumb'];
        }

        if (isset($conditions['catid'])){
            $data['catid'] = $conditions['catid'];
        }

        return $this->cmsDb->where($data)->count();
    }


    /*
     * 获取阅读量最高的文章
     */
    public function getReadMostArticle()
    {
        $cond['status'] = 1;

        return $this->cmsDb
            ->where($cond)
            ->field('article_id, title, count')
            ->order('count desc')
            ->find();
    }


    /*
     * 更新状态
     */
    public function changeArticleStatus($data)
    {
        return $this->cmsDb->save($data);
    }

    /*
     * 删除指定 id 的文章
     */
    public function removeArticle($id='')
    {
        return $this->cmsDb->where(['article_id' => $id])->save(['status' => -1]);
    }

    /*
     * 更新单篇文章信息
     */
    public function update($data)
    {
        if (!$data || !is_array($data) || !$data['article_id']){
            return false;
        }

        // 保存条件: $article_id, 保存数据 $save
        $article_id = $data['article_id'];
        $save['update_time'] = time();

        // 取出要保存的数据
        $name_array = ['title', 'small_title', 'thumb', 'title_font_color', 'catid', 'copyfrom', 'description', 'keywords'];
        foreach ($data as $key=>$item){
            if ($item && in_array($key, $name_array)){
                $save[$key] = $item;
            }
        }

        //var_dump($save);var_dump($article_id);exit;

        return $this->cmsDb->where('`article_id`=' . $article_id)->save($save);
    }


    /*
     * 读文章：更新文章阅读次数
     */
    public function readArticle($id)
    {
        $cond['article_id'] = $id;
        $cond['status'] = 1;

//        // 当前阅读次数
//        $count = $this->cmsDb->where($cond)->field('count')->find()['count'];
//
//        if (false === $count){
//            return false;
//        }
//
//        $data['count'] = $count + 1;
//        dump($cond);
//        return $this->cmsDb->where($cond)->save($data);

        return $this->cmsDb->where($cond)->setInc('count');
    }
}