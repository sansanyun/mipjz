<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\article\model;
use think\Cache;
use app\common\lib\Paginationm;
use app\common\lib\ChinesePinyin;
use think\Db;
use think\Controller;

class ArticlesCategory extends Controller
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
        
        $categoryListData = [];
        $categoryAllListData = [];
        $categoryAllListData = db('ArticlesCategory')->order('sort asc')->select();
        if ($categoryAllListData) {
            foreach ($categoryAllListData as $key => $value) {
                $categoryListData[$value['id']] = $value;
            }
        }
        config('articleCategoryAllListData',$categoryAllListData);
        config('articleCategoryListData',$categoryListData);
        
        $this->categoryListData = config('articleCategoryListData');
        $this->categoryAllListData = config('articleCategoryAllListData');
        $this->siteInfo = config('siteInfo');
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
            'is_page' => $data['is_page'] ? 1 : 0,
            'content' => htmlspecialchars($data['content']),
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
            'is_page' => $data['is_page'] ? 1 : 0,
            'content' => htmlspecialchars($data['content']),
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
        $pid = isset($tag['pid']) ? $tag['pid'] : 0;
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
            $categoryUrl = $itemCategoryInfo['category_url'];
            if ($categoryUrl) {
                $categoryUrl = str_replace('<url_name>',$urlName,$categoryUrl);
                $categoryUrl = str_replace('<id>',$itemCategoryInfo['id'],$categoryUrl);
            } else {
                $categoryUrl = '/'.$this->itemType.'/' . $urlName . '/';
            }
            $itemCategoryInfo['url'] = config('domain') . $categoryUrl;
            $itemCategoryInfo['rule'] = $categoryUrl;
            $categoryPageUrl = $itemCategoryInfo['category_page_url'];
            if ($categoryPageUrl) {
                $categoryPageUrl = str_replace('<category_url>', $categoryUrl,$categoryPageUrl);
                $categoryPageUrl = str_replace('<url_name>',$itemCategoryInfo['url_name'],$categoryPageUrl);
                $categoryPageUrl = str_replace('<id>',$itemCategoryInfo['id'],$categoryPageUrl);
            } else {
                $categoryPageUrl = $categoryUrl . 'index_<page>.html';
            }
            $itemCategoryInfo['pageTempRule'] = $categoryPageUrl;
            if (strpos($categoryPageUrl,'.html')) {
                $categoryPageUrl = str_replace('.html','',$categoryPageUrl);
            }
            $itemCategoryInfo['pageRule'] = $categoryPageUrl;
            $detailUrl = $itemCategoryInfo['detail_url'];
            if ($detailUrl) {
                if (strpos($categoryUrl,'.html')) {
                    $categoryUrl = str_replace('.html','/',$categoryUrl);
                }
                $detailUrl = str_replace('<category_url>', $categoryUrl,$detailUrl);
            } else {
                $detailUrl = $categoryUrl . '<id>.html';
            }
            $itemCategoryInfo['detailRule'] = $detailUrl;
            $detailUrl = str_replace('.html','',$detailUrl);
            $tempDetailUrl = substr($detailUrl, 0, 1) == '/' ? substr($detailUrl, 1) : $detailUrl;
            $itemCategoryInfo['detail__url__'] = str_replace('/','\/',str_replace('<id>','[a-zA-Z0-9_-]+$',$tempDetailUrl));
            $itemCategoryInfo['content'] = htmlspecialchars_decode($itemCategoryInfo['content']);
        } else {
            $itemCategoryInfo['url'] = config('domain') . '/'.$this->itemType.'/';
            $itemCategoryInfo['id'] = 0;
            $itemCategoryInfo['name'] = $this->itemName;
            $itemCategoryInfo['url_name'] = $this->itemType;
        }
        return $itemCategoryInfo;
    }
    public function getCategory($pid = '', $orderBy = 'sort', $order = 'asc', $limit = null, $where = null,$whereArray = null)
    {
        $itemCategoryList = null;
        if ($pid === '' || $pid === null) {
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
                $itemCategoryList[$key]['content'] = htmlspecialchars_decode($val['content']);
                if ($val['pid'] == 0) {
                    $itemCategoryList[$key]['sub'] = db($this->itemCategory)->where('pid',$val['id'])->order($orderBy,$order)->select();
                    if ($itemCategoryList[$key]['sub']) {
                        foreach ($itemCategoryList[$key]['sub'] as $k => $v) {
                            $itemCategoryList[$key]['sub'][$k] = $this->getCategoryInfo($v['id']);
                            $itemCategoryList[$key]['sub'][$k]['value'] = $v['id'];
                            $itemCategoryList[$key]['sub'][$k]['label'] = $v['name'];
                            $itemCategoryList[$key]['sub'][$k]['content'] = htmlspecialchars_decode($v['content']);
                        }
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