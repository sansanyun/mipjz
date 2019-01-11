<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\article\controller;
use think\Request;
use app\common\controller\Base;
class ApiArticle extends Base
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->itemModelNameSpace = 'app\article\model\Articles';
        $this->item = $this->articles;
        $this->itemCategory = $this->articlesCategory;
        $this->itemContent = $this->articlesContent;
        $this->itemType = 'article';
    }
    
    public function itemList()
    {
      	$page = input('post.page');
		$limit = input('post.limit');
		$orderBy = input('post.orderBy');
		$order = input('post.order');
        $cid = input('post.cid');
        $keywords = input('post.keywords');
        $domain = input('post.domain');
		if (!$page) {
		  $page = 1;
		}
		if (!$limit) {
		  $limit = 10;
		}
		if (!$orderBy) {
	       $orderBy = 'id';
		}
		if (!$order) {
			$order = 'desc';
		}
        $patern = '/^^((https|http|ftp)?:?\/\/)[^\s]+$/';
        $itemList = model($this->itemModelNameSpace)->getItemList($cid,$page,$limit,$orderBy,$order,null,$where,$keywords);
        $itemCount = model($this->itemModelNameSpace)->getCount($cid,'', $keywords);
        if ($domain) {
            if ($itemList) {
                foreach ($itemList as $key => $val) {
                    $itemList[$key]['url'] = model($this->itemModelNameSpace)->getUrlByItemInfo($val,$domain);
                }
            }
        }
        
        if ($itemList) {
            foreach ($itemList as $key => $val) {
                $itemList[$key]['publish_date'] = date('Y-m-d H:i:s',$itemList[$key]['publish_time']);
                if ($itemList[$key]['firstImg']) {
                    if (!preg_match($patern,$value)) {
                        $itemList[$key]['firstImg'] = $this->domain . $itemList[$key]['firstImg'];
                    }
                }
            }
        }
        
	    return jsonSuccess('',['itemList' => $itemList,'total' => $itemCount,'page' => $page]);
    }
    
    
    public function getItemInfo()
    {
        $uuid = input('post.uuid');
        if ($uuid) {
            $itemInfo = db($this->item)->where('uuid',$uuid)->find();
        } else {
            return  jsonError('内容不存在');
        }
        $itemInfo = model($this->itemModelNameSpace)->getItemInfo('',$uuid);
        $itemInfo['publish_date'] = date('Y-m-d H:i:s',$itemInfo['publish_time']);
        $patern = '/^^((https|http|ftp)?:?\/\/)[^\s]+$/';
        preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArrays);
        if ($imagesArrays) {
            foreach($imagesArrays[1] as $k => $v) {
                if (!preg_match($patern,$imagesArrays[1][$k])) {
                    $srcUrl = $this->domain . $imagesArrays[1][$k];
                    $itemInfo['content'] = str_replace($imagesArrays[1][$k],$srcUrl,$itemInfo['content']);
                }
            }
        }
        
        return jsonSuccess('',$itemInfo);
    }
}