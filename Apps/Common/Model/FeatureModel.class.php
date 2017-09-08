<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/8/16
 * Time: 下午6:11
 */

namespace Common\Model;

use Think\Exception;

/*
 * 推荐位表处理
 */
class FeatureModel extends CommonModel
{

    public function __construct()
    {
        parent::__construct('Feature');
    }


    /*
     * 取出所有推荐位
     */
    public function getFeatures($conditions)
    {
        $fields = 'id, name, create_time, update_time, status';   // 需要获取的字段

        return $this->selectItems($fields, $conditions);   // 取得所有推荐位
    }


    /*
     * 取出指定 ID 的推荐位
     */
    public function getFeature($id)
    {
        $fields = 'id, name, create_time, status, description';

        return $this->selectItemById($id, $fields);

    }


    /*
     * 取出所有推荐位名字
     */
    public function getFeatureNames()
    {
        $fields = 'id, name';

        return $this->selectItems($fields);
    }

    /*
     * 取出指定 ID 的推荐位名
     */
    public function getFeatureName($id)
    {
        $field = 'id, name';

        return $this->selectItemById($id, $field);
    }


    /*
     * 变更推荐位信息
     */
//    public function updateFeature($data)
//    {
//        if (!$data || !is_array($data)){
//            throw_exception('变更信息不正确!');
//        }
//
//        return $this->cmsDb->save($data);
//    }


    /*
     * 取出推荐位总数
     */
    public function getFeatureCount()
    {
        $cond['status'] = 1;

        return $this->cmsDb
            ->where($cond)
            ->count();
    }

}
