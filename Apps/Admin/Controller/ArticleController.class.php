<?php

namespace Admin\Controller;

use Think\Controller;
use Think\Exception;

class ArticleController extends CommonController
{
    /*
     * 文章管理页
     */


    public function __construct()
    {
        parent::__construct();

        // 一级页面屑导航
        parent::setFirstBreadCrumb([
            'name' => '文章管理',
            'url' => U('Admin/Article/index')
        ]);

    }


    /*
     * 获取文章来源数组
     */
    private function getCopyfromArr()
    {
        return C('COPY_FROM');
    }

    /*
     * 获取文章标题颜色数组
     */
    private function getTitleColorArr()
    {
        return C('TITLE_FONT_COLOR');
    }

    /*
     * 获取文章所属栏目数组
     */
    private function getArticleBarArr()
    {
        // 得到所有前台栏目项，用于页面栏目过滤下拉框
        $mdb = D('Menu');
        return $mdb->getBarMenus();
    }

    /*
     * 获取某篇文章所属栏目名称
     */
    private function getArticleBarName($barId)
    {
        $mdb = D('Menu');
        return $mdb->getBarMenu($barId);
    }

    /*
     * 文章编辑页面提交数据检查
     * @ $data 要检查的数据
     * @ return 成功返回 0, 失败返回 string 类型的 <error message>
     */
    private function checkEditData($data)
    {
        //var_dump($data);exit;

        if (!isset($data['title']) || empty($data['title'])) {
            return '标题不能为空';
        }

        if (!isset($data['small_title']) || empty($data['small_title'])) {
            return '短标题不能为空';
        }

//            if (!isset($data['thumb']) || empty($data['thumb'])) {
//                return [0, '缩略图不能为空'];
//            }

        if (!isset($data['catid']) || empty($data['catid'])) {
            return '栏目不能为空';
        }

        if (!isset($data['copyfrom']) || empty($data['copyfrom'])) {
            return '来源不能为空';
        }

        if (!isset($data['content']) || empty($data['content'])) {
            return '内容不能为空';
        }

        if (!isset($data['description']) || empty($data['description'])) {
            return '描述不能为空';
        }

        if (!isset($data['keywords']) || empty($data['keywords'])) {
            return '关键字不能为空';
        }

        return 0;

    }

    /*
     * 对表单数据中，某些字段的 html 特殊字符进行编码
     * @param &$data 表单数据, 以引用方式传递，需要对输入的数据进行修改
     * @return 无返回值
     */
    private function encodeEditData(&$data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, ['title', 'small_title', 'content', 'description', 'keywords'])) {
                $data[$key] = htmlspecialchars($data[$key]);
            }
        }
    }


    public function index()
    {
        parent::setSecondBreadCrumb([
            'name' => '文章列表',
            'url' => ''
        ]);

        // 得到文章所属栏目数组（前台栏目），用于文章列表页栏目过滤下拉框
        $bars = $this->getArticleBarArr();
        $this->assign('bars', $bars);


        // 查询条件1：当前选中栏目
        $barID = $_REQUEST['type'];
        if ($barID && $barID != -1) {
            if (!is_numeric($barID) || $barID < 0) {
                return show(0, '栏目 ID 不合法!');
            }
            $data['catid'] = $barID;
            $this->assign('currentBar', $barID);
        }


        // 查询条件2：文章标题模糊搜索条件
        if ($_REQUEST['title']) {
            $data['title'] = ['like', '%' . $_REQUEST['title'] . '%'];
            $this->assign('title', $_REQUEST['title']);
        }


        // 查询条件3、4：要获取的页，页的大小
        $p = $_GET['p'] ?: 1;   // 当前页，起始页从1开始计数
        $pageSize = $_GET['pageSize'] ?: C('PAGE_SIZE');      // 每页显示几条

        // 要获取的页，页的大小检验
        if (!is_numeric($p) || !is_numeric($pageSize)) {
            return $this->show('非法的 p 或 pageSize 参数!');
        }


        // 进行查询: 得到文章数组
        $adb = D('Article');
        $articles = $adb->getArticles($data, $p, $pageSize);
        if ($articles === false) {
            return show(0, '无法获取文章信息!');
        }


        // 根据栏目 ID 得到栏目名, 插入到文章结果数组中
        foreach ($articles as $key => $article) {
            $cat = $this->getArticleBarName($article['catid']);
            $articles[$key]['catname'] = $cat['name'];
        }


        // 得到推荐位数组
        try {
            if (false === ($features = D('Feature')->getFeatureNames())){
                return $this->error('获取推荐位失败!');
            }
        } catch (Exception $e){
            return $this->error($e->getMessage());
        }
        $this->assign('features', $features);

        /*
         * 分页处理：页数 = 文章总数 / 每页文章数
         */
        $articleCount = $adb->getArticleCount($data);     // 文章总数
        $pageCount = intval(ceil(floatval($articleCount) / floatval($pageSize))); // 总页数

        // 页数数组，用于循环条件
        $pageNoArray = (!$pageCount) ? [] : range(1, $pageCount);
        $this->assign('pagecnt', $pageNoArray);

        $this->assign('currentPage', $p);           // 当前分页
        $this->assign('totalPage', $pageCount);     // 总页数
        $this->assign('articles', $articles);       // 文章数组
        $this->display();
    }

    /*
     * 文章列表页
     */
    public function item()
    {
        // 二级面包屑导航
//        $this->breadNav['second'] = '文章列表';
//        $this->assign('nav', $this->breadNav);
//        parent::setSecondBreadCrumb([
//            'name' => '文章列表',
//            'url' => ''
//        ]);
//
//        // 得到文章所属栏目数组（前台栏目），用于文章列表页栏目过滤下拉框
//        $bars = $this->getArticleBarArr();
//        $this->assign('bars', $bars);
//
//
//        // 查询条件1：当前选中栏目
//        $barID = $_REQUEST['type'];
//        if ($barID && $barID != -1) {
//            if (!is_numeric($barID) || $barID < 0) {
//                return show(0, '栏目 ID 不合法!');
//            }
//            $data['catid'] = $barID;
//            $this->assign('currentBar', $barID);
//        }
//
//
//        // 查询条件2：文章标题模糊搜索条件
//        if ($_REQUEST['title']) {
//            $data['title'] = ['like', '%' . $_REQUEST['title'] . '%'];
//            $this->assign('title', $_REQUEST['title']);
//        }
//
//
//        // 查询条件3、4：要获取的页，页的大小
//        $p = $_GET['p'] ?: 1;   // 当前页，起始页从1开始计数
//        $pageSize = $_GET['pageSize'] ?: C('PAGE_SIZE');      // 每页显示几条
//
//        // 要获取的页，页的大小检验
//        if (!is_numeric($p) || !is_numeric($pageSize)) {
//            return $this->show('非法的 p 或 pageSize 参数!');
//        }
//
//
//        // 进行查询: 得到文章数组
//        $adb = D('Article');
//        $articles = $adb->getArticles($data, $p, $pageSize);
//        if ($articles === false) {
//            return show(0, '无法获取文章信息!');
//        }
//
//
//        // 根据栏目 ID 得到栏目名, 插入到文章结果数组中
//        foreach ($articles as $key => $article) {
//            $cat = $this->getArticleBarName($article['catid']);
//            $articles[$key]['catname'] = $cat['name'];
//        }
//
//
//        /*
//         * 分页处理：页数 = 文章总数 / 每页文章数
//         */
//        $articleCount = $adb->getArticleCount($data);     // 文章总数
//        $pageCount = intval(ceil(floatval($articleCount) / floatval($pageSize))); // 总页数
//
//        // 页数数组，用于循环条件
//        $pageNoArray = (!$pageCount) ? [] : range(1, $pageCount);
//        $this->assign('pagecnt', $pageNoArray);
//
//        $this->assign('currentPage', $p);           // 当前分页
//        $this->assign('totalPage', $pageCount);     // 总页数
//        $this->assign('articles', $articles);       // 文章数组
//        $this->display();
    }

    /*
     * 添加文章表单
     */
    public function add()
    {
        if ($_POST) { // "添加文章" 页面提交

            // 表单数据检查
            $check = $this->checkEditData($_POST);
            if ($check !== 0) { // 检查结果失败
                return show(0, $check);
            }

            // 对输入字符串中的 html 特殊字符进行编码
            $this->encodeEditData($_POST);

            //var_dump($_POST);exit;

            $articleId = D('Article')->insert($_POST);

            if (!$articleId) {
                return show(0, '文章插入失败!');
            }

            $article['article_id'] = $articleId;
            $article['content'] = $_POST['content'];

            $contentId = D('ArticleContent')->insert($article);

            if (!$contentId) {
                return show(0, "文章插入成功，内容插入失败");
            }

            return show(1, '文章插入成功');

        } else { // 添加文章页面显示
            // 面包屑导航
            parent::setSecondBreadCrumb([
                'name' => '添加文章',
                'url' => ''
            ]);

            // 得到前台栏目项
            $bars = $this->getArticleBarArr();

            // 得到标题颜色数组
            $title_color = $this->getTitleColorArr();

            // 得到文章来源数组
            $copy_from = $this->getCopyfromArr();

            $this->assign('bars', $bars);
            $this->assign('titleFontColor', $title_color);
            $this->assign('copyFrom', $copy_from);

            $this->display();
        }
    }

    /*
     * 编辑文章之前的判断
     */
    public function preEdit()
    {
        $id = $_GET['id'];
        if (!$id || !is_numeric($id)) {
            return show(0, '文章 id 不合法');
        }

        return show(1, '文章编辑成功!', U('Admin/Article/edit') . '?id=' . $_GET['id']);

    }

    /*
     * "编辑文章" 表单
     */
    public function edit()
    {
        // 面包屑导航
//        $this->breadNav['second'] = '编辑';
//        $this->assign('nav', $this->breadNav);

        parent::setSecondBreadCrumb([
            'name' => '编辑文章',
            'url' => ''
        ]);

        // 文章 ID
        $id = $_GET['id'];

        // 取得当前文章信息
        if (!($article = D('Article')->getArticle($id))) {
            $this->error('获取文章信息失败！');
        }

        // 取得当前文章内容
        if (!($content = D('ArticleContent')->getContent($id))) {
            $this->error('获取文章内容失败!');
        }

        //var_dump($article);exit;

        $this->assign('article', $article);
        $this->assign('content', $content);

        // 取得文章标题颜色数组
        $titleColors = $this->getTitleColorArr();
        $this->assign('titleColors', $titleColors);

        // 取得文章来源数组
        $copyfroms = $this->getCopyfromArr();
        $this->assign('copyfroms', $copyfroms);

        // 取得文章栏目数组
        $bars = $this->getArticleBarArr();
        $this->assign('bars', $bars);

        // 向前端返回文章 id
        $this->assign('id', $id);

        $this->display();

    }

    /*
     * 文章编辑页面的保存处理
     */
    public function save()
    {
        // 是否是 POST 请求
        if (!IS_POST) {
            return show(0, '非法请求');
        }

        // 表单数据检查
        $check = $this->checkEditData($_POST);
        if ($check !== 0) { // 检查结果失败
            return show(0, $check);
        }

        // 对输入字符串中的 html 特殊字符进行编码
        $this->encodeEditData($_POST);

        //var_dump($_POST);exit;

        /*
         * 1. 更新文章信息（文章表，不包含内容）
         */
        if (!D('Article')->update($_POST)) {
            return show(0, '文章更新失败!');
        }

        //var_dump('保存信息成功!');exit;

        /*
         * 2. 更新文章内容（文章内容表）
         */
        $articleContent['article_id'] = $_POST['article_id'];
        $articleContent['content'] = $_POST['content'];

        if (!D('ArticleContent')->update($articleContent)) {
            return show(0, '文章内容更新失败!');
        }

        return show(1, '文章更新成功');

    }


    /*
     * 移除文章
     */
    public function remove()
    {
        $id = $_GET['id'];

        if (!id || !is_numeric($id)) {
            return show(0, '文章 id 不合法!');
        }

        if (!D('Article')->removeArticle($id)) {
            return show(0, '删除文章不成功');
        }

        return show(1, '删除文章成功', U('Admin/Article/index'));
    }

    /*
    * 更新排序
    */
    public function renewOrder()
    {
        //dump($_POST);
        foreach ($_POST as $key => $post) {
            $data[$key]['listorder'] = $_POST[$key]['order'];
            $data[$key]['article_id'] = $_POST[$key]['id'];

        }

        if (D('Article')->renewOrder($data)) {
            return show(1, "更新排序成功!", U('Admin/Article/index'));
        }

        return show(0, '排序无变化!');

    }

    /*
     * 更新状态
     */
    public function statusChange()
    {
        $data['article_id'] = $_GET['id'];
        $data['status'] = $_GET['status'];

        if (is_null($data['article_id']) || !is_numeric($data['article_id'])) {
            return show(0, 'id 不合法');
        }

        if (is_null($data['status']) || !is_numeric($data['status'])) {
            return show(0, '状态值不合法');
        }

        if (D('Article')->changeArticleStatus($data) === false) {
            return show(0, '变更状态失败!');
        }
        return show(1, '变更状态成功!', U('Admin/Article/index'));

    }

    /*
     * 文章推送
     */
    public function pushArticle()
    {
//        var_dump($_POST);exit;

        $articleIds = $_POST['articleIds'];
        $featureId = $_POST['featureId'];


        // 推送数据
        $pushData = [];
        foreach ($articleIds as $articleId){

            // 得到文章信息
//            if (false === ($article = D('Article')->getArticle($articleId))) {
            if (!($article = D('Article')->getArticle($articleId))) {
                return show(0, '无法获取文章信息!');
            }

            $pushData = [
                'feature_id' => $featureId,
                'title' => $article['title'],
                'thumb' => $article['thumb'],
//                'url' =>'',
                'article_id' => $articleId,
//                'status' => $article['status'],
            ];

            // 保存推送的文章信息
            try{
                if (!D('FeatureContent')->insert($pushData)){
                    return show(0, '文章推送失败!');
                }
            } catch(Exception $e) {
                return show(0, $e->getMessage());
            }

        }

        return show(1, '文章推送成功!');


    }


}
