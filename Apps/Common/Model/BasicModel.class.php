<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/8/23
 * Time: 下午12:19
 */

namespace Common\Model;

use Think\Model;
use Think\Exception;


/*
 * 基本配置
 */
class BasicModel extends Model
{

    public function __construct()
    {

    }

    /*
     * 保存配置
     */
    public function save($data)
    {
        if (empty($data) || !is_array($data)){
            throw new Exception('数据不合法!');
        }

//        if (!empty(F('basicConfig'))){
//            //清空
//            F('basicConfig', null);
//        }

        // 返回 ID 值
        return F('basicConfig', $data);
    }


    /*
     * 读取配置
     */
    public function select()
    {
        return F('basicConfig');
    }
}

