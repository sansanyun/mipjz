<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\tag\model;
use think\Cache;
use app\common\lib\Paginationm;
use app\common\lib\ChinesePinyin;
use think\Db;
use think\Controller;

class Tags extends Controller
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
    public function itemAdd($data,$fieldList = [])
    {
        $uuid = uuid();
        $resArray = array (
            'id' => $uuid,
            'cid' => $data['cid'] ? $data['cid'] : 0,
            'name' => htmlspecialchars($data['name']),
            'keywords' => $data['keywords'],
            'description' => $data['description'],
            'url_name' => $data['url_name'],
        );
        if (is_array($fieldList)) {
            for ($i=0; $i < count($fieldList); $i++) { 
                $resArray[$fieldList[$i]['key']] = $fieldList[$i]['value'];
            }
        }
        db($this->item)->insert($resArray);
        $itemInfo = db($this->item)->where('id',$uuid)->find();
        return true;
    }

    public function itemEdit($data,$fieldList = [])
    {
        $resArray = array (
            'cid' => $data['cid'] ? $data['cid'] : 0,
            'name' => htmlspecialchars($data['name']),
            'keywords' => $data['keywords'],
            'description' => $data['description'],
            'url_name' => $data['url_name'],
        );
        $itemInfo = db($this->item)->where('id',$data['id'])->update($resArray);
        return true;
    }
    
    public function itemDel($uuid)
    {
        $itemInfo = db($this->item)->where('id',$uuid)->find();
        if (!$itemInfo) {
            return false;
        }
        db($this->item)->where('id',$uuid)->delete();
        return true;
    }
    
    
    public function getItemInfo($id = null,$uuid = null)
    {
        if (!$id && !$uuid) {
               return false;
        }
        if ($id) {
            $itemInfo = db($this->item)->where('id',$id)->find();
        }
        if ($uuid) {
            $itemInfo = db($this->item)->where('id',$uuid)->find();
        }
        if ($itemInfo) {
            $itemInfo['userInfo'] = null;
            $itemInfo['categoryInfo'] = model($this->itemCategoryModelNameSpace)->getCategoryInfo($itemInfo['cid']);
            $itemInfo['url'] = $this->getUrlByItemInfo($itemInfo);
            return $itemInfo;
        } else {
            return false;
        }
    }
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
        $orderBy = isset($tag['orderBy']) ? $tag['orderBy'] : 'add_time';
        $order = isset($tag['order']) ? $tag['order'] : 'desc';
        $limit = isset($tag['limit']) ? $tag['limit'] : 10;
        $where = isset($tag['where']) ? $tag['where'] : null;
        $type = isset($tag['type']) ? $tag['type'] : '';
        $itemIds = isset($tag['itemIds']) ? $tag['itemIds'] : '';
        $ids = isset($tag['ids']) ? $tag['ids'] : '';
        
        if ($listType == 'list') {
            return $this->getItemList($cid, $page, $limit, $orderBy, $order, $where,$keywords,$whereArray,$itemIds,$type,$ids);
        }
        if ($listType == 'pagination') {
            return $this->getPagination($cid, $page, $limit, $orderBy, $order, $where,$keywords,$whereArray);
        }
        
    }
    public function getItemList($cid = null, $page = 1, $perPage = 10, $orderBy = 'add_time', $order = 'desc', $where = null,$keywords = null,$whereArray = null,$itemIds = null,$type = null,$ids = null)
    {
        $orderBy = $orderBy ? $orderBy : 'add_time';
        $order = $order ? $order : 'desc';
        $keywordsWhere = null;
        if ($keywords) {
            $keywords = explode(',',$keywords);
            foreach ($keywords as $key => $val) {
                if ($val) {
                    $sq[] = "%".$val."%";
                }
            }
            $keywordsWhere['name']  = ['like',$sq,'OR'];
        }
        $itemList = [];
        if ($type == 'about') {
            if ($itemIds) {
                $itemArray = db('ItemTags')->where('item_id',$itemIds)->field('tags_id')->select();
                $tempList = [];
                if ($itemArray) {
                    foreach ($itemArray as $key => $value) {
                        if ($value['tags_id']) {
                            $tempList[] = $value['tags_id'];
                        }
                    }
                }
                $tempList = implode(',', $tempList);
            } else {
                $tempList = $ids;
            }
            $itemTags = db('ItemTags')->where('tags_id','in',$tempList)->field('item_id')->select();
            if ($itemTags) {
                $tempItemList = [];
                foreach ($itemTags as $key => $value) {
                    if ($value['item_id']) {
                        $tempItemList[] = $value['item_id'];
                    }
                }
            }
            if ($tempItemList) {
                $tempItemList = array_unique($tempItemList);
                $itemIds = implode(',', $tempItemList);
                $tempItemTags = db('itemTags')->where('item_id','in',$itemIds)->field('tags_id')->select();
                if ($tempItemTags) {
                    $tempSubItemList = [];
                    foreach ($tempItemTags as $key => $value) {
                        if ($value['tags_id']) {
                            $tempSubItemList[] = $value['tags_id'];
                        }
                    }
                    
                    $tempSubItemList = array_count_values($tempSubItemList);
                    arsort($tempSubItemList);
                    $ids = [];
                    foreach ($tempSubItemList as $key => $val) {
                        $ids[] = $key;
                    }
                   
                    $allPageNum = ceil(count($ids) / $perPage);
                    if ($page <= $allPageNum) {
                        $newIds = array_slice($ids,($page - 1) * $perPage,$perPage);
                        if ($newIds) {
                            foreach ($newIds as $uuid) {
                                $itemList[] = db($this->item)->where('id',$uuid)->find();
                            }
                        }
                    }
                }
                
            }
        } else {
            if ($itemIds) {
                $itemList = [];
                $itemTags = db('ItemTags')->where('item_id','in',$itemIds)->select();
                if ($itemTags) {
                    foreach ($itemTags as $k => $v) {
                        $tagInfo = db($this->item)->where('id',$v['tags_id'])->find();
                        if ($tagInfo) {
                            $itemList[] = $tagInfo;
                        } 
                    }
                }
            } else {
                if ($cid == '' || $cid == null) {
                    $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($whereArray)->page($page,$perPage)->order($orderBy,$order)->select();
                } else {
                    $itemCategoryList = db($this->itemCategory)->where('pid',$cid)->select();
                    if ($itemCategoryList) {
                        foreach ($itemCategoryList as $key => $value) {
                            $cids[] = $value['id'];
                        }
                    }
                    if ($itemCategoryList) {
                        $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($whereArray)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->order($orderBy,$order)->select();
                    } else {
                        $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($whereArray)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
                    }
                }
            }
            
        }
        if ($itemList) {
            foreach($itemList as $k => $v) {
                $itemList[$k]['tempId'] = config('siteInfo')['idStatus'] ? $v['uuid'] : $v['id'];
                $itemList[$k]['categoryInfo'] = model($this->itemCategoryModelNameSpace)->getCategoryInfo($v['cid']);
                $itemList[$k]['url'] = $this->getUrlByItemInfo($v);
            }
        } else {
            $itemList = [];
        }
        return $itemList;
    }

    public function getPagination($cid = null, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $where = null,$keywords = null,$whereArray = null)
    {
        $count = $this->getCount($cid,$where, $keywords,$whereArray);
        $categoryInfo = model($this->itemCategoryModelNameSpace)->getCategoryInfo($cid);
        $baseUrl = $categoryInfo['url'];
        $pagination_array = array(
            'base_url' => $baseUrl,
            'total_rows' => $count,
            'per_page' => $perPage,
            'page_break' => '_'
        );
        $pagination = new Paginationm($pagination_array);
        return $pagination->create_links();
    }
    
    public function getCount($cid = null,$where = null, $keywords = null,$whereArray = null)
    {
        $count = 0;
        if (empty($keywords)) {
            $keywordsWhere = null;
        } else {
            $keywords = explode(',',$keywords);
            foreach ($keywords as $key => $val) {
                if ($val) {
                    $sq[] = "%".$val."%";
                }
            }
            $keywordsWhere['name']  = ['like',$sq,'OR'];
        }
        if ($cid == '' || $cid == null) {
            $count = db($this->item)->where($where)->where($keywordsWhere)->where($whereArray)->page($page,$perPage)->count();
        } else {
            $itemCategoryList = db($this->itemCategory)->where('pid',$cid)->select();
            if ($itemCategoryList) {
                foreach ($itemCategoryList as $key => $value) {
                    $cids[] = $value['id'];
                }
            }
            if ($itemCategoryList) {
                $count = db($this->item)->where($where)->where($keywordsWhere)->where($whereArray)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->count();
            } else {
                $count = db($this->item)->where($where)->where($keywordsWhere)->where($whereArray)->where('cid',$cid)->page($page,$perPage)->count();
            }
        }
        return $count;
    }

 
    public function getCrumb($tag)
    {
        $tag = json_decode($tag,true);
        $cid = isset($tag['cid']) ? $tag['cid'] : '';
        $ulClass = isset($tag['ulClass']) ? $tag['ulClass'] : 'site-crumb';
        $liClass = isset($tag['liClass']) ? $tag['liClass'] : '';
        $isHome = isset($tag['isHome']) ? $tag['isHome'] : 1;
        $separator = isset($tag['separator']) ? $tag['$separator'] : '';
        if ($cid) {
            $categoryInfo = model($this->itemCategoryModelNameSpace)->getCategoryInfo($cid);
            if ($categoryInfo['pid'] == 0) {
                $html = '<ul class="list-unstyled d-flex ' . $ulClass . '">';
                $html .= intval($isHome) === 1 ? '<li class="' . $liClass .'"><a href="'. $this->domain .'" title="'. config('siteInfo')['siteName'] .'">首页</a>'.$separator.'</li>' : '';
                $html .= '<li class="' . $liClass .'">';
                $html .= '<a href="'. $categoryInfo['url'] .'" title="'. $categoryInfo['name'] .'">';
                $html .= $categoryInfo['name'];
                $html .= '</a>';
                $html .= '</li>';
                $html .= '</ul>';
                return $html;
            } else {
                $html = '<ul class="list-unstyled d-flex ' . $ulClass . '">';
                $html .= intval($isHome) === 1 ? '<li class="' . $liClass .'"><a href="'. $this->domain .'" title="'. config('siteInfo')['siteName'] .'">首页</a>'.$separator.'</li>' : '';
                $html .= '<li class="' . $liClass .'">';
                $html .= '<a href="'. $categoryInfo['parent']['url'] .'" title="'. $categoryInfo['parent']['name'] .'">';
                $html .= $categoryInfo['parent']['name'];
                $html .= '</a>'.$separator;
                $html .= '</li>';
                $html .= '<li class="' . $liClass .'">';
                $html .= '<a href="'. $categoryInfo['url'] .'" title="'. $categoryInfo['name'] .'">';
                $html .= $categoryInfo['name'];
                $html .= '</a>';
                $html .= '</li>';
                $html .= '</ul>';
                return $html;
            }
        } else {
            $html = '<ul class="list-unstyled d-flex ' . $ulClass . '">';
            $html .= intval($isHome) === 1 ? '<li class="' . $liClass .'"><a href="'. $this->domain .'" title="'. config('siteInfo')['siteName'] .'">首页</a>'.$separator.'</li>' : '';
            $html .= '<li class="' . $liClass .'">';
            $html .= '<a href="'. config('domain') . '/' . config('siteInfo')['tagModelUrl'] . '/' .'" title="'. $this->itemName .'">';
            $html .= $this->itemName;
            $html .= '</a>';
            $html .= '</li>';
            $html .= '</ul>';
            return $html;
        }
    }
    public function getPage($tag)
    {
        $tag = json_decode($tag,true);
        $itemId = isset($tag['itemId']) ? $tag['itemId'] : null;
        $limit = isset($tag['limit']) ? $tag['limit'] : 1;
        $page = isset($tag['page']) ? $tag['page'] : 1;
        $type = isset($tag['type']) ? $tag['type'] : 'detail';
        $itemType = isset($tag['itemType']) ? $tag['itemType'] : '';
        
        if (!$itemId) {
            return false;
        }
        if ($type == 'detail') {
            if ($itemType == 'upPage') {
                $itemList = db($this->item)->where('id','<',$itemId)->limit($limit)->order('id','DESC')->select();
            }
            if ($itemType == 'downPage') {
                $itemList = db($this->item)->where('id','>',$itemId)->limit($limit)->order('id','ASC')->select();
            }
            if ($itemList) {
                foreach ($itemList as $k => $v) {
                    $itemList[$k]['categoryInfo'] = model($this->itemCategoryModelNameSpace)->getCategoryInfo($v['cid']);
                }
                
                foreach ($itemList as $k => $v) {
                    $itemList[$k]['url'] = $this->getUrlByItemInfo($v); 
                }
                
                return $itemList;
            } else {
                return false;
            }
        }

        if ($type == 'category') {
            $count = $this->getCount($itemId);
            $pageNum = ceil($count / $prePage);
            if ($itemType == 'upPage') {
                if ($page == 1) {
                    return false;
                } else {
                    $page = $page - 1;
                    $url = $this->getCategoryInfo($itemId,$page)['url'];
                    $tempArray = [];
                    $tempArray[0]['url'] = $url;
                    $tempArray[0]['num'] = $page;
                    return $tempArray;
                }
            }
            if ($itemType == 'downPage') {
                if ($pageNum <= $page) {
                    return false;
                } else {
                    $page = $page + 1;
                    $url = $this->getCategoryInfo($itemId,$page)['url'];
                    $tempArray = [];
                    $tempArray[0]['url'] = $url;
                    $tempArray[0]['num'] = $page;
                    return $tempArray;
                }
            }
        }
    }

    
    public function getUrlByItemInfo($item,$domain = null)
    {
        $tempId = config('siteInfo')['idStatus'] ? $item['uuid'] : $item['id'];
        $tempId = $item['url_name'] ? $item['url_name'] : $tempId;
        if (config('isTagName')) {
            $tempId = $item['name'];
        }
        $domain = $domain ? $domain : config('domain');
        $res = $domain . '/' . config('siteInfo')['tagModelUrl'] . '/' . $tempId . '/';
        return $res;
    }

    public function getContentByItemContentId($uuid)
    {
        if (!$uuid) {
            return false;
        }
        return htmlspecialchars_decode(db($this->itemContent)->where('id',$uuid)->find()['content']);
    }
    
    
    public function getContentFilterByTagInfo($itemInfo)
    {
        if (!$itemInfo) {
            return false;
        }       
        if (!isset($itemInfo['content']) || !$itemInfo['content']) {
            $itemInfo['content'] = db($this->itemContent)->where('id',$itemInfo['uuid'])->find()['content'];
        }
        $content = model('app\common\model\Common')->getContentFilterByContent($itemInfo['content']);
        return $content;
    }

}