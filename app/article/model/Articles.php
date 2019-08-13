<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.
namespace app\article\model;
use think\Cache;
use app\common\lib\Paginationm;
use app\common\lib\ChinesePinyin;
use think\Db;
use think\Controller;

class Articles extends Controller
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
        
        $this->siteInfo = config('siteInfo');
        $this->domain = config('domain');
    }
    public function itemAdd($data,$fieldList = [])
    {
        $uuid = uuid();
        $resArray = array (
            'uuid' => $uuid,
            'cid' => $data['cid'] ? $data['cid'] : 0,
            'uid' => config('userId'),
            'title' => htmlspecialchars($data['title']),
            'keywords' => $data['keywords'],
            'description' => $data['description'],
            'url_name' => $data['url_name'],
            'link_tags' => $data['link_tags'],
            'publish_time' => isset($data['publish_time']) && $data['publish_time'] ? $data['publish_time'] : time(),
            'is_recommend' => isset($data['is_recommend']) ? $data['is_recommend'] : 0,
        );
        if (isset($data['img_url'])) {
            $resArray['img_url'] = $data['img_url'];
        }
        if (isset($data['down_url'])) {
            $resArray['down_url'] = $data['down_url'];
        }
        if (isset($data['stock_num'])) {
            $resArray['stock_num'] = $data['stock_num'];
        }
        if (isset($data['money'])) {
            $resArray['money'] = $data['money'];
        }
        if (is_array($fieldList)) {
            for ($i=0; $i < count($fieldList); $i++) { 
                $resArray[$fieldList[$i]['key']] = $fieldList[$i]['value'];
            }
        }
        db($this->item)->insert($resArray);
        if (db('Addons')->where('name','articleImgLocal')->find()) {
            $data['content'] = model('addons\articleImgLocal\model\ArticleImgLocal')->index($data['content']);
        }
        db($this->itemContent)->insert(array(
           'id' => $uuid,
           'content' => htmlspecialchars($data['content']),
        ));
        $itemInfo = db($this->item)->where('uuid',$uuid)->find();
        if ($data['tags']) {
            $itemType = 'article';
            $tags = explode(',',$data['tags']);
            model('app\tag\model\ItemTags')->innerTags($tags, $itemType, $itemInfo);
        }
        if ($itemInfo) {
            model($this->itemModelNameSpace)->itemPushUrl($itemInfo);
        }
        return true;
    }

    public function itemEdit($data,$fieldList = [])
    {
        $resArray = array (
            'cid' => $data['cid'] ? $data['cid'] : 0,
            'uid' => config('userId'),
            'title' => htmlspecialchars($data['title']),
            'keywords' => $data['keywords'],
            'description' => $data['description'],
            'url_name' => $data['url_name'],
            'link_tags' => $data['link_tags'],
            'publish_time' => isset($data['publish_time']) && $data['publish_time'] ? $data['publish_time'] : time(),
            'is_recommend' => isset($data['is_recommend']) ? $data['is_recommend'] : 0,
        );
        if (isset($data['img_url'])) {
            $resArray['img_url'] = $data['img_url'];
        }
        if (is_array($fieldList)) {
            for ($i=0; $i < count($fieldList); $i++) { 
                $resArray[$fieldList[$i]['key']] = $fieldList[$i]['value'];
            }
        }
        db($this->item)->where('uuid',$data['uuid'])->update($resArray);
        if (db('Addons')->where('name','articleImgLocal')->find()) {
            $data['content'] = model('addons\articleImgLocal\model\ArticleImgLocal')->index($data['content']);
        }
        db($this->itemContent)->where('id',$data['uuid'])->update(array(
           'content' => htmlspecialchars($data['content']),
        ));
        $itemInfo = db($this->item)->where('uuid',$data['uuid'])->find();
        if (isset($data['tags']) && $data['tags']) {
            $itemType = 'article';
            $tags = explode(',',$data['tags']);
            model('app\tag\model\ItemTags')->innerTags($tags, $itemType, $itemInfo);
        } else {
            db('ItemTags')->where('item_id',$data['uuid'])->delete();
        }
        return true;
    }
    
    public function itemDel($uuid)
    {
        $itemInfo = db($this->item)->where('uuid',$uuid)->find();
        if (!$itemInfo) {
            return false;
        }
        db($this->itemContent)->where('id',$uuid)->delete();
        db($this->item)->where('uuid',$uuid)->delete();
        return true;
    }
    
    
    public function getItemInfo($id = null,$uuid = null,$publishType = null)
    {
        if (!$id && !$uuid) {
               return false;
        }
        
        if (config('keyInfo')['articlePublishType'] == "verify") {
            $hideWhere['is_hide'] = 0;
        } else {
            $hideWhere = null;
        }
        if ($publishType == 'all')  {
            $hideWhere = null;
        }
        
        if ($id) {
            $itemInfo = db($this->item)->where($hideWhere)->where('id',$id)->find();
        }
        if ($uuid) {
            $itemInfo = db($this->item)->where($hideWhere)->where('uuid',$uuid)->find();
        }
        
        if ($itemInfo) {
            $itemInfo['userInfo'] = model('app\user\model\Users')->getItemInfo($itemInfo['uid']);;
            $itemInfo['content'] = $this->getContentByArticleInfo($itemInfo);
            $itemInfo['description'] = $itemInfo['description'] ? $itemInfo['description'] : mb_substr(deleteHtml($itemInfo['content']),0,88,'utf-8');
            $itemInfo['mipContent'] = $this->getContentFilterByArticleInfo($itemInfo);
            if ($itemInfo['img_url']) {
                $itemInfo['firstImg'] = $itemInfo['img_url'];
            } else {
                $itemInfo = $this->getImgList($itemInfo);
            }
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
        if (!$tag) {
            return false;
        }
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
        $keywords = isset($tag['keywords']) ? $tag['keywords'] : null;
        $tag['itemType'] = $this->itemType;
        
        if ($listType == 'list') {
            if ((isset($tag['tagNames']) && $tag['tagNames']) || (isset($tag['tagIds']) && $tag['tagIds'])) {
                return $this->getItemListbyTagIds($cid, $page, $limit, $orderBy, $order, $where,$keywords,$whereArray,$tag['tagNames'],$tag['tagIds'],$tag['itemType']);                
            } else {
                return $this->getItemList($cid, $page, $limit, $orderBy, $order, $where,$keywords,$whereArray);
            }
        }
        if ($listType == 'pagination') {
            if ((isset($tag['tagNames']) && $tag['tagNames']) || (isset($tag['tagIds']) && $tag['tagIds'])) {
                return $this->getPaginationbyTagIds($cid, $page, $limit, $orderBy, $order, $where,$keywords,$whereArray,$tag['tagNames'],$tag['tagIds'],$tag['itemType']);                
            } else {
                return $this->getPagination($cid, $page, $limit, $orderBy, $order, $where,$keywords,$whereArray);
            }
        }
        
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
        
        if (empty($uuids)) {
            $uuidsWhere = null;
        } else {
            $uuids = explode(',',$uuids);
            $uuidsWhere['uuid']  = ['in',$uuids];
        }
        
        if (config('keyInfo')['articlePublishType'] == "verify") {
            $hideWhere['is_hide'] = 0;
        } else {
            $hideWhere = null;
        }
        if ($publishType == 'all')  {
            $hideWhere = null;
        }
        
        $itemList = [];
        if ($cid == '' || $cid == null) {
            $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($hideWhere)->where($uuidsWhere)->where($whereArray)->page($page,$perPage)->order($orderBy,$order)->select();
        } else {
            $itemCategoryList = db($this->itemCategory)->where('pid',$cid)->select();
            if ($itemCategoryList) {
                foreach ($itemCategoryList as $key => $value) {
                    $cids[] = $value['id'];
                }
            }
            if ($itemCategoryList) {
                $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($hideWhere)->where($uuidsWhere)->where($whereArray)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->order($orderBy,$order)->select();
            } else {
                $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($hideWhere)->where($uuidsWhere)->where($whereArray)->where('cid',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
            }
        }
        if ($itemList) {
            foreach($itemList as $k => $v) {
                $itemList[$k]['tempId'] = $this->siteInfo['idStatus'] ? $v['uuid'] : $v['id'];
                $itemList[$k]['userInfo'] = null;
                $itemList[$k]['categoryInfo'] = model($this->itemCategoryModelNameSpace)->getCategoryInfo($v['cid']);
                $itemList[$k]['is_recommend'] = intval($itemList[$k]['is_recommend']);
                $itemList[$k]['description'] = $itemList[$k]['description'] ? $itemList[$k]['description'] : mb_substr(deleteHtml(htmlspecialchars_decode(db($this->itemContent)->where('id',$v['uuid'])->find()['content'])),0,88,'utf-8');
            }
            foreach($itemList as $k => $v) {
                $itemList[$k]['url'] = $this->getUrlByItemInfo($v);
            }
            foreach($itemList as $k => $v) {
                if ($itemList[$k]['img_url']) {
                    $itemList[$k]['firstImg'] = $itemList[$k]['img_url'];
                } else {
                    $itemList[$k] = $this->getImgList($v);
                }
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
            'cur_page' => $page,
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
            $keywordsWhere['title']  = ['like',$sq,'OR'];
        }
        if (empty($uuids)) {
            $uuidsWhere = null;
        } else {
            $uuids = explode(',',$uuids);
            $uuidsWhere['uuid']  = ['in',$uuids];
        }
        
        if (config('keyInfo')['articlePublishType'] == "verify") {
            $hideWhere['is_hide'] = 0;
        } else {
            $hideWhere = null;
        }
        if ($publishType == 'all')  {
            $hideWhere = null;
        }
        
        if ($cid == '' || $cid == null) {
            $count = db($this->item)->where($where)->where($keywordsWhere)->where($hideWhere)->where($uuidsWhere)->where($whereArray)->page($page,$perPage)->count();
        } else {
            $itemCategoryList = db($this->itemCategory)->where('pid',$cid)->select();
            if ($itemCategoryList) {
                foreach ($itemCategoryList as $key => $value) {
                    $cids[] = $value['id'];
                }
            }
            if ($itemCategoryList) {
                $count = db($this->item)->where($where)->where($keywordsWhere)->where($hideWhere)->where($uuidsWhere)->where($whereArray)->whereOr('cid',$cid)->whereOr('cid','in',$cids)->page($page,$perPage)->count();
            } else {
                $count = db($this->item)->where($where)->where($keywordsWhere)->where($hideWhere)->where($uuidsWhere)->where($whereArray)->where('cid',$cid)->page($page,$perPage)->count();
            }
        }
        return $count;
    }
    //
    
    public function getItemListbyTagIds($cid = null, $page = 1, $limit = 10, $orderBy = 'publish_time', $order = 'desc', $where = null,$keywords = null, $whereArray = null,$tagNames = null,$tagIds = null, $itemType = null)
    {
        if ($tagNames) {
            $tagNames = explode(',',$tagNames);
            foreach ($tagNames as $val) {
                $tagInfo = db('Tags')->where('name',$val)->find();
                if ($tagInfo) {
                    $tempTagIds[] = $tagInfo['id'];
                }
            }
            $tagIdsWhere['tags_id']  = ['in',$tempTagIds];
        }
        if ($tagIds) {
            $tagIds = explode(',',$tagIds);
            $tagIdsWhere['tags_id']  = ['in',$tagIds];
        }
         
        $itemTagsList = db('ItemTags')->where($tagIdsWhere)->order('item_add_time',$order)->page($page,$limit)->select();
        if ($itemTagsList) {
            foreach ($itemTagsList as $k => $v) {
                $itemTagsListIds[] = $v['item_id'];
            }
            $itemTagsListIds = implode(',', $itemTagsListIds);
            return $this->getItemList($cid, 1, $limit, $orderBy, $order, $where, $keywords,$whereArray, $itemTagsListIds);
        }
    }
    public function getPaginationbyTagIds($cid = null, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $where = null,$keywords = null,$whereArray = null,$tagNames = null,$tagIds = null,$itemType = null)
    {
        if ($tagNames) {
            $tagNames = explode(',',$tagNames);
            foreach ($tagNames as $val) {
                $tagInfo = db('Tags')->where('name',$val)->find();
                if ($tagInfo) {
                    $tempTagIds[] = $tagInfo['id'];
                }
            }
            $tagIdsWhere['tags_id']  = ['in',$tempTagIds];
        }
        if ($tagIds) {
            $tagArray = explode(',',$tagIds);
            $tagIdsWhere['tags_id']  = ['in',$tagArray];
        }
        $count = db('ItemTags')->where($tagIdsWhere)->count();
        
        $tagInfo = null;
        if ($tagIds) {
            $tempTagIds = explode(',', $tagIds);
            if (count($tempTagIds) == 1) {
                $tagInfo = db('Tags')->where('id',$tagIds)->find();
            } else {
                return false;
            }
        }
        if ($tagNames) {
            $tempTagNames = explode(',', $tagNames);
            if (count($tempTagNames) == 1) {
                $tagInfo = db('Tags')->where('name',$tagNames)->find();
            } else {
                return false;
            }
        }
        
        if ($tagInfo) {
            $baseUrl = model('app\tag\model\Tags')->getitemInfo($tagInfo['id'])['url'];
        }
        
        $pagination_array = array(
            'base_url' => $baseUrl,
            'total_rows' => $count,
            'per_page' => $perPage,
            'cur_page' => $page,
            'page_break' => '_'
        );
        $pagination = new Paginationm($pagination_array);
        return $pagination->create_links();
    }
    
    public function getCrumb($tag)
    {
        $tag = json_decode($tag,true);
        $cid = isset($tag['cid']) ? $tag['cid'] : '';
        $ulClass = isset($tag['ulClass']) ? $tag['ulClass'] : 'site-crumb';
        $liClass = isset($tag['liClass']) ? $tag['liClass'] : '';
        $isHome = isset($tag['isHome']) ? $tag['isHome'] : 1;
        $separator = isset($tag['separator']) ? $tag['separator'] : '';
        if ($cid) {
            $categoryInfo = model($this->itemCategoryModelNameSpace)->getCategoryInfo($cid);
            if ($categoryInfo['pid'] == 0) {
                $html = '<ul class="list-unstyled d-flex ' . $ulClass . '">';
                $html .= intval($isHome) === 1 ? '<li class="' . $liClass .'"><a href="'. $this->domain .'" title="'. $this->siteInfo['siteName'] .'">首页</a>'.$separator.'</li>' : '';
                $html .= '<li class="' . $liClass .'">';
                $html .= '<a href="'. $categoryInfo['url'] .'" title="'. $categoryInfo['name'] .'">';
                $html .= $categoryInfo['name'];
                $html .= '</a>';
                $html .= '</li>';
                $html .= '</ul>';
                return $html;
            } else {
                $html = '<ul class="list-unstyled d-flex ' . $ulClass . '">';
                $html .= intval($isHome) === 1 ? '<li class="' . $liClass .'"><a href="'. $this->domain .'" title="'. $this->siteInfo['siteName'] .'">首页</a>'.$separator.'</li>' : '';
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
            $html .= intval($isHome) === 1 ? '<li class="' . $liClass .'"><a href="'. $this->domain .'" title="'. $this->siteInfo['siteName'] .'">首页</a>'.$separator.'</li>' : '';
            $html .= '<li class="' . $liClass .'">';
            $html .= '<a href="'. config('domain') . '/' . $this->itemType . '/' .'" title="'. $this->itemName .'">';
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
        $tempId = $this->siteInfo['idStatus'] ? $item['uuid'] : $item['id'];
        $tempId = $this->siteInfo['diyUrlStatus'] ? $item['url_name'] ? $item['url_name'] : $tempId : $tempId;
        $domain = $domain ? $domain : config('domain');
        if (!isset($item['categoryInfo'])) {
            $item['categoryInfo'] = model($this->itemCategoryModelNameSpace)->getCategoryInfo($item['cid']);
        }
        if ($item['categoryInfo'] && $item['categoryInfo']['id'] != 0) {
            $detailUrl = str_replace('<id>',$tempId,$item['categoryInfo']['detailRule']);
            $res = $domain . $detailUrl;
        } else {
            $res = $domain . '/article/' . $tempId . '.html';
        }
        return $res;
    }
    
    
    public function getImgList($item)
    {
        if (!$item) {
            return false;
        }
        $patern = '/^^((https|http|ftp)?:?\/\/)[^\s]+$/';
        if (!isset($item['content']) || !$item['content']) {
            $item['content'] = $this->getContentByArticleInfo($item);
        }
        if (preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $item['content'], $imgs)) {
            $item['imgCount'] = count($imgs[1]);
            foreach ($imgs[1] as $key => $value) {
               if (@preg_match($patern,$value)) {
                   $imgs[1][$key] = $value;
                } else {
                   $imgs[1][$key] = $this->domainStatic . $value;
                }
            }
            $item['imgList'] = $imgs[1];
            $item['firstImg'] = $item['img_url'] ? $item['img_url'] : $imgs[1][0];
        } else {
            if (config('addonsInfo')['randShowImg']) {
                $item['firstImg'] = config('domainStatic') . model('addons\\randShowImg\\model\\RandShowImg')->index($item);
            } else {
                $item['firstImg'] = config('domainStatic') . '/' . config('assets') . '/common/images/no-images.jpg';
            }
            $item['imgCount'] = 0;
            $item['imgList'] = [];
        }
        return $item;
    }
    
    
     
    public function updateViews($id, $uid)
    {
        $tempCache = Cache::get('updateViewsArticle' . md5(session_id()) . intval($id));
        if ($tempCache) {
            return false;
        }
        Cache::set('updateViewsArticle' . md5(session_id()) . intval($id), time(), 60);
        db($this->item)->where('id',$id)->update([
            'views' => db($this->item)->where('id',$id)->find()['views'] + 1,
        ]);
        return true;
    }

    public function getContentByArticleInfo($itemInfo)
    {
        if (!$itemInfo) {
            return false;
        }       
        if (!isset($itemInfo['content']) || !$itemInfo['content']) {
            $itemInfo['content'] = db($this->itemContent)->where('id',$itemInfo['content_id'] ? $itemInfo['content_id'] : $itemInfo['uuid'])->find()['content'];
        }
        return htmlspecialchars_decode($itemInfo['content']);
    }
    
    
    public function getContentFilterByArticleInfo($itemInfo)
    {
        if (!$itemInfo) {
            return false;
        }       
        if (!isset($itemInfo['content']) || !$itemInfo['content']) {
            $itemInfo['content'] = db($this->itemContent)->where('id',$itemInfo['content_id'] ? $itemInfo['content_id'] : $itemInfo['uuid'])->find()['content'];
        }
        $content = model('app\common\model\Common')->getContentFilterByContent($itemInfo['content']);
        return $content;
    }

    public function itemPushUrl($createInfo)
    {
        
        $domainSitesList = db('domainSites')->select();
        if ($this->siteInfo['superSites'] && $domainSitesList) {
        foreach ($domainSitesList as $key => $val) {
                $domainSettingsInfo = db('domainSettings')->where('id',$val['id'])->find();
                $urls = $this->getUrlByItemInfo($createInfo,$val['http_type'].$val['domain']);
                $urls = explode(',',$urls);
                if ($domainSettingsInfo['mipAutoStatus'] && $domainSettingsInfo['mipApi']) {
                    $result = pushData($domainSettingsInfo['mipApi'],$urls);
                }
                
                if ($domainSettingsInfo['ampAutoStatus'] && $domainSettingsInfo['ampApi']) {
                    $result = pushData($domainSettingsInfo['ampApi'],$urls);
                }
                
                if ($domainSettingsInfo['xiongZhangStatus'] && $domainSettingsInfo['xiongZhangNewAutoStatus'] && $domainSettingsInfo['xiongZhangNewApi']) {
                    $result = pushData($domainSettingsInfo['xiongZhangNewApi'],$urls);
                }
                
                if ($domainSettingsInfo['yuanChuangAutoStatus'] && $domainSettingsInfo['yuanChuangApi']) {
                    $result = pushData($domainSettingsInfo['yuanChuangApi'],$urls);
                }
                
                if ($domainSettingsInfo['linkAutoStatus'] && $domainSettingsInfo['linkApi']) {
                    $result = pushData($domainSettingsInfo['linkApi'],$urls);
                }
            }
        } else {
            $urls = $this->getUrlByItemInfo($createInfo);
            $urls = explode(',',$urls);
            if (is_array($urls)) {
                if ($this->siteInfo['baiduYuanChuangStatus']) {
                    $api = $this->siteInfo['baiduYuanChuangUrl'];
                    $result = pushData($api,$urls);
                }
                if ($this->siteInfo['baiduTimePcStatus']) {
                    $api = $this->siteInfo['baiduTimePcUrl'];
                    $result = pushData($api,$urls);
                }
                if ($this->siteInfo['guanfanghaoStatus']) {
                    if ($this->siteInfo['guanfanghaoStatusPost']) {
                        $api = $this->siteInfo['guanfanghaoRealtimeUrl'];
                        $result = pushData($api,$urls);
                    }
                }
                if ($this->siteInfo['mipPostStatus']) {
                    $api = $this->siteInfo['mipApiAddress'];
                    $result = pushData($api,$urls);
                }
            }
        }
        return $result;

    }

}