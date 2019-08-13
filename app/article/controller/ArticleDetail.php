<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\article\controller;
use app\common\controller\Base;

class ArticleDetail extends Base
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->item = 'Articles';
        $this->itemType = 'article';
        $this->itemName = '文章';
        $this->itemContent = 'ArticlesContent';
        $this->itemCategory = 'ArticlesCategory';
        $this->itemModelNameSpace = 'app\article\model\Articles';
        $this->itemCategoryModelNameSpace = 'app\article\model\ArticlesCategory';
        $this->categoryListData = config('articleCategoryListData');
        $this->categoryAllListData = config('articleCategoryListData');
    }
    public function index()
    {
        $id = input('param.id');
        $page = input('param.page');
        if ($this->siteInfo['idStatus']) {
            $itemInfo = model($this->itemModelNameSpace)->getItemInfo(null,$id);
        } else {
            $itemInfo = model($this->itemModelNameSpace)->getItemInfo($id);
        }
        if (!$itemInfo) {
            if ($this->siteInfo['diyUrlStatus']) {
                $itemInfo = db($this->item)->where('url_name',$id)->find();
                if (!$itemInfo) {
                    return $this->error('访问的内容不存在','');
                }
            }
        }
        if (!$itemInfo) {
            return $this->error('访问的内容不存在','');
        }
        $itemInfo = model($this->itemModelNameSpace)->getItemInfo($itemInfo['id']);
        //当前所属分类别名
        $this->assign('categoryUrlName',$itemInfo['categoryInfo']['url_name']);
        
        //更新当前页面浏览次数
        model($this->itemModelNameSpace)->updateViews($itemInfo['id'], $itemInfo['uid']);
        
        //详情页面ID
        $this->assign('itemDetailId',$itemInfo['uuid']);
        
        $this->assign('page',$page ? $page : 1);
        
        $this->assign('cid',$itemInfo['categoryInfo']['id']);
         
        //标题
        $mipTitle = $itemInfo['title'] . $this->siteInfo['titleSeparator'] . $this->siteInfo['siteName'];
        $this->assign('mipTitle', $mipTitle);
     
        //关键词
        if ($itemInfo['keywords']) {
            $mipKeywords = $itemInfo['keywords'];
        } else {
	        $itemTagsList = model('app.tag.model.Tags')->getItemList( null, 1, 20, 'add_time', 'desc', $where = null, null, null,$itemInfo['uuid'], null, null);
	        $this->assign('tags',$itemTagsList);
	        $itemInfo['tagsListString'] = '';
	        if ($itemTagsList) {
	          	foreach ($itemTagsList as $k => $v) {
	                $tempTagsName[] = $v['name'];
	            }
	            $tagsListString = implode(',',$tempTagsName);
	            $itemInfo['tagsListString'] = $tagsListString;
	        }
            $mipKeywords = $itemInfo['tagsListString'];
        }
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
        
        $templateName = $itemInfo['categoryInfo']['detail_template'] ? $itemInfo['categoryInfo']['detail_template'] : $this->itemType . 'Detail';
        $templateName = str_replace('.html', '', $templateName);
        
        //兼容老版本
        $itemTagsList = model('app.tag.model.Tags')->getItemList('',1,10,'add_time','asc','','','',$itemInfo['uuid']);
        if ($itemTagsList) {
            foreach ($itemTagsList as $key => $value) {
                if ($value) {
                   $itemTagsList[$key]['tags']['name'] = $value['name'];
                }
            }
        }
        $this->assign('tags',$itemTagsList);
        
        
        return $this->mipView($this->itemType . '/'.$templateName);
    }


}