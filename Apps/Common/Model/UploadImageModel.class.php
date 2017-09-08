<?php
/**
 * Created by PhpStorm.
 * User: Notus
 * Date: 17/7/26
 * Time: 下午9:07
 */

namespace Common\Model;

use \Think\Model;


class UploadImageModel extends Model
{
    // tp 文件上传对象
    private $upObj = '';

    const  UPLOAD = 'Upload/';

    public function __construct()
    {

        $this->upObj = new \Think\Upload();
        $this->upObj->rootPath = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . self::UPLOAD;
        $this->upObj->subName = date('Y') . '/' . date('m') . '/' . date('d');
    }

    // image & kindeditor image upload
    public function upload()
    {
        $info = $this->upObj->upload();

        if (!$info){
            return [0, $this->upObj->getError()];
        } else {
            // 以 http: 开头的绝对路径返回和存储图片地址
            return [1, $_SERVER['HTTP_ORIGIN'] . dirname($_SERVER['SCRIPT_NAME']) . '/' . self::UPLOAD . $info['article_img']['savepath'] . $info['article_img']['savename'] ];
        }
    }

}