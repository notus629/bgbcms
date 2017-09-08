<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/8/17
 * Time: 下午6:31
 */

namespace Common\Model;

use Think\Exception;


class FeatureContentModel extends CommonModel
{

    /*
     * 待添加的推荐位内容检查
     */
    private function checkInsertFeatureContent($data)
    {
        if (!$data || !is_array($data)) {
            throw new Exception('推荐位内容数据不合法!');
        }

        if (!$data['title']) {
            throw new Exception('标题不能为空');
        }

        if (!$data['thumb']) {
            throw new Exception('缩略图不能为空!');
        }

        if (!$data['url'] && !$data['article_id']) {
            throw new Exception('url 和文章 ID 不能都为空');
        }

        if ($data['article_id'] && !is_numeric($data['article_id'])) {
            throw new Exception('文章 ID 值不合法!');
        }

        if ($data['status'] != 0 && $data['status'] != 1) {
            throw new Exception('推荐位内容状态不合法!');
        }
    }


    /*
     * 待更新的推荐位内容检查
     */
    private function checkUpdateFeatureContent($data)
    {
        $this->checkInsertFeatureContent($data);

        // 比待添加的推荐位内容检查多了个 ID 检查
        if (!$data['id'] || !is_numeric($data['id'])) {
            throw new Exception('推荐位 ID 不合法!');
        }

    }


    public function __construct()
    {
        parent::__construct('feature_content');

    }


    /*
     * 取得所有推荐位内容
     */
    public function getFeatureContents($conditions)
    {

        $fields = 'order, id, feature_id, title, create_time, update_time, thumb, status';

//        $conditions['order'] = '`order` desc, `id` desc';      // 排序


        return $this->selectItems($fields, $conditions);
    }

    /*
     * 取得广告位
     * @param $num 广告数
     */
    public function getAdvs($num = 2)
    {
        $fields = 'title, url, thumb';

        $conditions = [
            'feature_id' => 6,      // 右侧广告位 ID
            'status' => 1,
            'thumb' => ['neq', ''],
            'p' => 1,
            'page_size' => $num,
        ];

        return $this->selectItems($fields, $conditions);
    }


    /*
     * 获取大图推荐
     * @param $num 想要获取的大图推荐的数量，默认为 1
     */
    public function getBigFeatures($num = 1)
    {
        $fields = 'id, title, thumb, create_time';
        $conditions = [
            'status' => 1,
            'feature_id' => 2,                           // 大图推荐, 即 feature_id = 2
            'thumb' => ['neq', ''],                      // 缩略图不为空
            'p' => 1,
            'page_size' => $num,                         // 大图推荐数量，默认1个
//            'order' => '`order` desc, `id` desc',        // 排序
        ];

        return $this->selectItems($fields, $conditions);
    }


    /*
     * 获取小图推荐
     * @param $num 想要获取的小图推荐的数量，默认为 3
     */
    public function getSmallFeatures($num = 3)
    {
        $fields = 'id, title, thumb, create_time';
        $conditions = [
            'status' => 1,
            'feature_id' => 1,                          // 小图推荐, 即 feature_id = 1
            'thumb' => ['neq', ''],                     // 缩略图不为空
            'p' => 1,
            'page_size' => $num,                        // 小图推荐数量，默认 3 个
//            'order' => '`order` desc, `id` desc',       // 排序
        ];

        return $this->selectItems($fields, $conditions);
    }


    /*
     * 取得特定推荐位内容-根据 ID
     */
    public function selectFeatureContent($id='')
    {
        $data['status'] = ['neq', -1];
        $data['id'] = $id;

        return $this->cmsDb
            ->where($data)
            ->field('id, title, feature_id, thumb, url, article_id, status')
            ->find();
    }


    /*
     *  新增推荐位内容
     */
    public function insert($data = [])
    {
        // 推荐位内容数据检查
        $this->checkInsertFeatureContent($data);

        $data['create_time'] = time();
        $data['update_time'] = time();

        return $this->cmsDb->add($data);
    }

    /*
     * 更新推荐位内容
     */
    public function update($data = [])
    {
        // 推荐位内容数据检查
        $this->checkUpdateFeatureContent($data);

        $data['update_time'] = time();

        return $this->cmsDb->save($data);
    }


}