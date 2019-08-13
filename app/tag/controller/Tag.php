<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\tag\controller;

use app\common\controller\Base;

class Tag extends Base
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->item = 'Tags';
        $this->itemType = 'tag';
        $this->itemName = '标签';
        $this->itemContent = 'TagsContent';
        $this->itemCategory = 'TagsCategory';
        $this->itemModelNameSpace = 'app\tag\model\Tags';
        $this->itemCategoryModelNameSpace = 'app\tag\model\TagsCategory';
        $this->categoryListData = config('tagCategoryListData');
        $this->categoryAllListData = config('tagCategoryListData');
    }
    public function index()
    {
        $page = input('param.page');
        $name = input('param.name');
        $params = input('params');
        $this->assign('params',$params);
        if (!empty($params)) {
            $params = explode('__', $params);
            if ($params) {
                foreach ($params as $key => $val) {
                    if (strpos($val, '-') !== false) {
                        $tempVal = explode('-', $val);
                        $this->assign($tempVal[0],$tempVal[1]);
                    }
                }
            }
        }
        $page = $page ? $page : 1;
        $id = db($this->itemCategory)->where('url_name',$name)->find()['id'];
        if ($id) {
            $categoryInfo = model($this->itemCategoryModelNameSpace)->getCategoryInfo($id);
            if (!$categoryInfo) {
                $this->error('分类不存在','');
            }
            $currentCid = $categoryInfo['id'];
        } else {
            $currentCid = 0;
            $categoryInfo['id'] = 0;
            $categoryInfo['name'] = $this->itemName;
            $categoryInfo['seo_title'] = '';
            $categoryInfo['keywords'] = '';
            $categoryInfo['description'] = '';
            $categoryInfo['url_name'] = $this->itemType;
            $categoryInfo['url'] = $this->domain . '/' . $this->itemType . '/';
        }
        //自定义参数
        $categoryInfo['cid'] = $categoryInfo['id'] ? $categoryInfo['id'] : '';
        $categoryInfo['page'] = $page ? $page : '';
        //当前分类别名
        $this->assign('cid',$categoryInfo['id']);
        $this->assign('page',$page);
        $this->assign('categoryUrlName',$categoryInfo['url_name']);
        
        //分页数量
        $pageText = $page == 1 ? "" : $this->siteInfo['titleSeparator'] . "第" . $page . "页";
        
        //标题关键词描述
        $mipTitle = $categoryInfo['seo_title'] ? $categoryInfo['seo_title'] : $categoryInfo['name'] . $pageText . $this->siteInfo['titleSeparator'] . $this->siteInfo['siteName'];
        $this->assign('mipTitle', $mipTitle);
        $this->assign('mipKeywords',$categoryInfo['keywords']);
        $this->assign('mipDescription',$categoryInfo['description']);
      
        $this->assign('categoryInfo',$categoryInfo);
          
        $templateName = $categoryInfo['template'] ? $categoryInfo['template'] : $this->itemType;
        $templateName = str_replace('.html', '', $templateName);
        
        return $this->mipView($this->itemType. '/' .$templateName);
    }

}