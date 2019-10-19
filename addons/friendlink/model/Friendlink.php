<?php
namespace addons\friendlink\model;
use think\Db;
use think\Controller;

class Friendlink extends Controller
{
    public function getItemListByTag($tag)
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
        $orderBy = isset($tag['orderBy']) ? $tag['orderBy'] : 'sort';
        $order = isset($tag['order']) ? $tag['order'] : 'asc';
        $limit = isset($tag['limit']) ? $tag['limit'] : 10;
        $where = isset($tag['where']) ? $tag['where'] : null;
        
        $type = isset($tag['type']) ? $tag['type'] : '';
        
        return $this->getItemList($cid, $page, $limit, $orderBy, $order, $where,$keywords,$whereArray);
        
    }

    public function getItemList($cid = null, $page = 1, $perPage = 10, $orderBy = 'sort', $order = 'desc', $where = null,$keywords = null,$whereArray = null,$uuids = null)
    {
        $orderBy = $orderBy ? $orderBy : 'sort';
        $order = $order ? $order : 'asc';
        $itemList = [];
        $typeWhere = null;
        if ($this->request->module() == 'index') {
            $typeWhere['type']  = ['=','index'];
        } else {
            $typeWhere['type']  = ['=','notIndex'];
        }
        try {
	        $itemList = db('friendlink')->where($typeWhere)->whereOr('type','all')->where($where)->where($whereArray)->order($orderBy,$order)->select();
	        if ($itemList) {
	        } else {
	            $itemList = [];
	        }
        } catch(\Exception $e) {}
        return $itemList;
    }

}