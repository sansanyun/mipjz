<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\widget\controller;
use think\Request;
use app\common\controller\Base;
class Pages extends Base
{
    public function index()
    {
        $urlName = input('param.url_name');
        $itemInfo = db('WidgetPages')->where('url_name',$urlName)->find();
        if (!$itemInfo) {
            return $this->error('页面不存在');
        }
        
        $itemInfo['mipContent'] = model('app\common\model\Common')->getContentFilterByContent(htmlspecialchars_decode($itemInfo['content']));
        $itemInfo['content'] = htmlspecialchars_decode($itemInfo['content']);
        
        $itemInfo['cid'] = 0;
        $itemInfo['url'] = $this->domain . '/' . $itemInfo['url_name'] . '.html';
        $itemInfo['publish_time'] = time();
        $itemInfo['views'] = 0;
        $itemInfo['img_url'] = '';
        
        $mipTitle = $itemInfo['title'] . $this->mipInfo['titleSeparator'] . $this->mipInfo['siteName'];
        $this->assign('mipTitle',$mipTitle);
        $this->assign('mipKeywords',$itemInfo['keywords']);
        $this->assign('mipDescription',$itemInfo['description']);
        
        $this->assign('itemInfo',$itemInfo);
        
        return $this->siteView('page/page');
    }
 
    
}
