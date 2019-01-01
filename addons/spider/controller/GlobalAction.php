<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace addons\spider\controller;
use think\Request;
use think\Controller;
class GlobalAction extends Controller{
    public function spider()    {        
        $addonsInfo = config('addonsInfo');        if (!$addonsInfo || !isset($addonsInfo['spider'])) {            return false;        }        $userAgent = @Request::instance()->header()['user-agent'];        if (strpos($userAgent,"Baiduspider") !== false) {
            if (strpos($userAgent,"Mobile") !== false) {
                if (strpos($userAgent,"render") !== false) {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'mobileRender','pageUrl' => $this->view->siteUrl, 'ua' => Request::instance()->ip(), 'vendor' => 'baidu'));
                } else {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'mobile','pageUrl' => $this->view->siteUrl, 'ua' => Request::instance()->ip(), 'vendor' => 'baidu'));
                }
            } else {                
                if (strpos($userAgent,"render") !== false) {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'pcRender','pageUrl' => $this->view->siteUrl, 'ua' => Request::instance()->ip(), 'vendor' => 'baidu'));
                } else {
                    db('spiders')->insert(array('uuid' => uuid(),'add_time' => time(),'type' => 'pc','pageUrl' => $this->view->siteUrl, 'ua' => Request::instance()->ip(), 'vendor' => 'baidu'));
                }
            }        	$request = Request::instance();			 			if ($request->routeInfo()['route'] == 'article/ArticleDetail/index') { 				                $itemInfo = $this->view->itemInfo;								db('Articles')->where('id',$itemInfo['id'])->update(array(					'baidu_spider_num' => $itemInfo['baidu_spider_num'] + 1,					'baidu_spider_time' => time(),				));							}
        }

    }
}
