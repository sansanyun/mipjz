<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.
namespace app\common\model;
use think\Cache;
use app\common\lib\Paginationm;
use app\common\lib\ChinesePinyin;
use think\Db;
use think\Controller;

class Posts extends Controller
{
    
    public function getItemListByTag($tag,$listType)
    {
        $tag = json_decode($tag,true);
        $whereArray = [];
        foreach ($tag as $key => $val) {
            if (strpos($key, 'where') !== false) {
                if (strpos($val, '=') !== false) {
                    $tempVal = explode('=', $val);
                    $whereArray[trim($tempVal[0])] = trim($tempVal[1]);
                }
                if (strpos($val, 'like') !== false) {
                    $tempVal = explode('like', $val);
                    $whereArray[trim($tempVal[0])] = ['like',trim($tempVal[1])];
                }
                if (strpos($val, '>') !== false) {
                    $tempVal = explode('>', $val);
                    $whereArray[trim($tempVal[0])] = ['>',trim($tempVal[1])];
                }
                if (strpos($val, '<') !== false) {
                    $tempVal = explode('<', $val);
                    $whereArray[trim($tempVal[0])] = ['<',trim($tempVal[1])];
                }
            }
        }
        $cid = isset($tag['cid']) ? $tag['cid'] : null;
        $page = isset($tag['page']) ? $tag['page'] : 1;
        $orderBy = isset($tag['orderBy']) ? $tag['orderBy'] : 'publish_time';
        $order = isset($tag['order']) ? $tag['order'] : 'desc';
        $limit = isset($tag['limit']) ? $tag['limit'] : 10;
        $where = isset($tag['where']) ? $tag['where'] : null;
        
        $type = isset($tag['type']) ? $tag['type'] : '';
        
        return $this->getItemList($cid, $page, $limit, $orderBy, $order, $where,$keywords,$whereArray);
        
    }

    public function getItemList($cid = null, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $where = null,$keywords = null,$whereArray = null,$uuids = null, $publishType = null)
    {
        $orderBy = $orderBy ? $orderBy : 'publish_time';
        $order = $order ? $order : 'desc';
        $keywordsWhere = null;
        if ($keywords) {
            $keywords = explode(',',$keywords);
            foreach ($keywords as $key => $val) {
                if ($val) {
                    $sq[] = "%".$val."%";
                }
            }
            $keywordsWhere['title']  = ['like',$sq,'OR'];
        }
        
        $itemList = [];
        $itemArticleList = model('app\article\model\Articles')->getItemList($data['cid'], $data['page'], $data['limit'], $data['orderBy'], $data['order'], $where, $data['keywords'], null,null,null);
        if ($itemArticleList) {
            foreach ($itemArticleList as $key => $value) {
                   $itemArticleList[$key]['postType'] = 'article';
            }
        }
        $itemAskList = model('app\ask\model\Asks')->getItemList($data['cid'], $data['page'], $data['limit'], $data['orderBy'], $data['order'], $where, $data['keywords'], null,null,null);
        if ($itemAskList) {
            foreach ($itemAskList as $key => $value) {
            	   $itemAskList[$key]['postType'] = 'ask';
            }
        }
        $itemAllList = array_merge($itemArticleList,$itemAskList);
        if ($itemAllList) {
            array_multisort(array_column($itemAllList,'publish_time'),SORT_DESC,$itemAllList);
            $itemAllList = array_slice($itemAllList,0,10);
            if ($itemAllList) {
                foreach ($itemAllList as $key => $value) {
                    if ($itemAllList[$key]['postType'] == 'article') {
                        $itemAllList[$key] = model('app\article\model\Articles')->getImgList($value);
                    }
                    if ($itemAllList[$key]['postType'] == 'ask') {
                        $itemAllList[$key] = model('app\ask\model\Asks')->getImgList($value);
                    }
                }
            }
        }
        if ($itemAllList) {
            
        } else {
            $itemAllList = [];
        }
        return $itemAllList;
    }

}