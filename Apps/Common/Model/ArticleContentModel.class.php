<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/7/31
 * Time: 下午3:03
 */

namespace Common\Model;

use \Think\Model;


class ArticleContentModel extends Model
{
    private $articleDb = '';

    public function __construct()
    {
        $this->articleDb = M('article_content');
    }

    /*
     * 文章内容插入
     */
    public function insert($data)
    {
        if (!$data || !is_array($data)){
            return false;
        }

        $data['create_time'] = time();
        $res = $this->articleDb->add($data);

        return $res;
    }

    /*
     * 获取某篇文章的内容，根据文章 ID
     */
    public function getContent($id='')
    {
        $data['article_id'] = $id;

        return $this->articleDb->where($data)
            ->field('content')
            ->find();
    }

    /*
     * 更新单篇文章内容
     */
    public function update($data)
    {
        if (!$data || !is_array($data) || !$data['article_id']){
            return false;
        }

        // 保存条件: $article_id, 保存数据 $save
        $article_id = $data['article_id'];
        $save['update_time'] = time();
        $save['content'] = $data['content'];

        return $this->articleDb->where('`article_id`=' . $article_id)->save($save);
    }
}