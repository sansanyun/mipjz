<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\tag\model;
use think\Cache;
use app\common\lib\Paginationm;
use app\common\lib\ChinesePinyin;
use think\Db;
use think\Controller;

class TagsCategory extends Controller
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
    public function categoryAdd($data)
    {
        db($this->itemCategory)->insert(array(
            'name' => $data['name'],
            'url_name' =>  $data['url_name'],
            'seo_title' =>  $data['seo_title'],
            'template' => $data['template'],
            'detail_template' => $data['detail_template'],
            'category_url' =>  $data['category_url'],
            'category_page_url' => $data['category_page_url'],
            'detail_url' => $data['detail_url'],
            'keywords' => $data['keywords'],
            'description' => $data['description'],
            'status' => isset($data['status']) ? $data['status'] : 1,
            'pid' => isset($data['pid']) ? $data['pid'] : 0, 
        ));
        return jsonSuccess('添加成功');
    }
    
    public function categoryEdit($data)
    {
        db($this->itemCategory)->where('id',$data['id'])->update(array(
            'name' => $data['name'],
            'url_name' => $data['url_name'],
            'seo_title' => $data['seo_title'],
            'keywords' => $data['keywords'],
            'description' => $data['description'],
            'template' => $data['template'],
            'detail_template' => $data['detail_template'],
            'category_url' => $data['category_url'],
            'category_page_url' => $data['category_page_url'],
            'detail_url' => $data['detail_url'],
            'status' => $data['status'] ? 1 : 0,
            'pid' => $data['pid'] ? $data['pid'] : 0,
            ));
        return true;
    }
     
    public function getCategoryByTag($tag)
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
        $pid = isset($tag['pid']) ? $tag['pid'] : '';
        $orderBy = isset($tag['orderBy']) ? $tag['orderBy'] : 'sort';
        $order = isset($tag['order']) ? $tag['order'] : 'asc';
        $limit = isset($tag['limit']) ? $tag['limit'] : '';
        $type = isset($tag['type']) ? $tag['type'] : '';
        
        return $this->getCategory($pid, $orderBy, $order, $limit, $where,$whereArray);
        
    }
    public function getCategoryInfo($cid)
    {
        if ($cid) {
            $itemCategoryInfo = $this->categoryListData[$cid];
            if ($itemCategoryInfo) {
                if ($itemCategoryInfo['pid'] == 0) {
                    $urlName = $itemCategoryInfo['url_name'];
                } else {
                    $tempCategoryInfo = $this->getCategoryInfo($itemCategoryInfo['pid']);
                    $itemCategoryInfo['parent'] = $tempCategoryInfo;
                    if ($tempCategoryInfo) {
                        $urlName = $tempCategoryInfo['url_name'] . '/' . $itemCategoryInfo['url_name'];
                    }
                }
            }
            $itemCategoryInfo['url'] = config('domain') . '/'.$this->itemType.'/' . $urlName . '.html';
        } else {
            $itemCategoryInfo['url'] = config('domain') . '/'.$this->itemType.'/';
        }
        return $itemCategoryInfo;
    }
    public function getCategory($pid = '', $orderBy = 'sort', $order = 'asc', $limit = null, $where = null,$whereArray = null)
    {
        $itemCategoryList = null;
        if ($pid === '' || $pid == null) {
            $itemCategoryList = db($this->itemCategory)->where($where)->where($whereArray)->limit($limit)->order($orderBy,$order)->select();
        } else {
            $itemCategoryList = db($this->itemCategory)->where('pid',$pid)->where($where)->where($whereArray)->limit($limit)->order($orderBy,$order)->select();
        }
       if($itemCategoryList) {
            foreach ($itemCategoryList as $key => $val) {
                $itemCategoryList[$key] = $this->getCategoryInfo($val['id']);
            }
            foreach ($itemCategoryList as $key => $val) {
                $itemCategoryList[$key]['value'] = $val['id'];
                $itemCategoryList[$key]['label'] = $val['name'];
                $itemCategoryList[$key]['sub'] = db($this->itemCategory)->where('pid',$val['id'])->select();
                if ($itemCategoryList[$key]['sub']) {
                    foreach ($itemCategoryList[$key]['sub'] as $k => $v) {
                    	   $itemCategoryList[$key]['sub'][$k] = $this->getCategoryInfo($v['id']);
                           
                        $itemCategoryList[$key]['sub'][$k]['value'] = $v['id'];
                        $itemCategoryList[$key]['sub'][$k]['label'] = $v['name'];
                    }
                } else {
                    $itemCategoryList[$key]['sub'] = array();
                }
                $itemCategoryList[$key]['children'] = $itemCategoryList[$key]['sub'];
            }
        }

        return $itemCategoryList;
    }
    
}