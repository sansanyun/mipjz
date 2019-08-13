<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\tag\controller;
use app\common\controller\Base;

class TagDetail extends Base
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
        $id = input('param.id');
        $page = input('param.page');
        if (config('isTagName')) {
            $itemInfo = db('Tags')->where('name',$id)->find();
        } else {
            $itemInfo = db('Tags')->where('id',$id)->find();
            if ($itemInfo) {
                if ($itemInfo['url_name']) {
                    header('HTTP/1.1 301 Moved Permanently');
                    header('Location: ' . $this->domain .'/' . $this->siteInfo['tagModelUrl'] . '/' . $tagInfo['url_name'] .  '/');
                    exit();
                }
            } else {
                $itemInfo = db('Tags')->where('url_name',$id)->find();
            }
        }
        $itemInfo = model($this->itemModelNameSpace)->getItemInfo($itemInfo['id']);
        if (!$itemInfo) {
            return $this->error('访问的内容不存在','');
        }
        //当前所属分类别名
        $this->assign('categoryUrlName',$itemInfo['categoryInfo']['url_name']);
        
        //详情页面ID
        $this->assign('itemDetailId',$itemInfo['uuid']);
        $page = $page ? $page : 1;
        $itemInfo['page'] = $page;
        
        $this->assign('page',$page);
        
        $this->assign('cid',$itemInfo['categoryInfo']['id']);
         
        //标题
        $pageText = $page == 1 ? "" : $this->siteInfo['titleSeparator'] . "第" . $page . "页";
        $mipTitle = $itemInfo['name'] . $pageText . $this->siteInfo['titleSeparator'] . $this->siteInfo['siteName'];
        $this->assign('mipTitle', $mipTitle);
     
        //关键词
        $mipKeywords = $itemInfo['keywords'];
        $this->assign('mipKeywords',$mipKeywords);
        
        //文本描述
        if (@$itemInfo['description']) {
            $mipDescription = $itemInfo['description'];
        } else {
            $itemInfo['description'] = preg_replace("/(\s|\r|\n|\t|\&nbsp\;|　| |\xc2\xa0)/","",trim(strip_tags($itemInfo['content'])));
            $mipDescription = mb_substr($itemInfo['description'],0,88,'utf-8');
        }
        $this->assign('mipDescription',$mipDescription);
        
        $this->assign('itemInfo',$itemInfo);
        $this->assign('tagInfo',$itemInfo);
        
        $templateName = $itemInfo['categoryInfo']['detail_template'] ? $itemInfo['categoryInfo']['detail_template'] : $this->itemType . 'Detail';
        $templateName = str_replace('.html', '', $templateName);
        
        return $this->mipView($this->itemType . '/'.$templateName);
    }


}