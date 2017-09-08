<?php
/**
 * Created by PhpStorm.
 * User: llos
 * Date: 17/7/13
 * Time: 下午7:37
 */

namespace Admin\Controller;

use Think\Controller;


/*
 * 上传图片的处理
 */
class ImageController extends Controller
{

    /*
     * 缩略图上传
     */
    public function upload()
    {
        $ob = D('UploadImage');
        $ret = $ob->upload();

        if(!$ret[0]){
            return show(0, '上传图片失败!', '', $ret[1]);
        } else {
            //var_dump($ret[1]);exit;
            return show(1, '上传图片成功!', '', $ret[1]);
        }
    }

    /*
     * kindeditor 编辑器上传处理
     */
    public function kindupload()
    {
        $ob = D('UploadImage');
        $ret = $ob->upload();

        if(!$ret[0]){
            return showKind(0, '图片上传失败: ' . $ret[1]);
        } else {
            return showKind(1, $ret[1]);
        }
    }

}