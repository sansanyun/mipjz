<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.
namespace app\user\model;
use think\Cache;
use think\Request;
use app\common\lib\Paginationm;
use app\common\lib\ChinesePinyin;
use think\Db;
use think\Controller;

class Users extends Controller
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->item = 'Users';
        $this->itemType = 'user';
        $this->itemName = '用户';
        $this->itemContent = 'UsersContent';
        $this->itemCategory = 'UsersGroup';
        $this->itemModelNameSpace = 'app\user\model\Users';
        $this->itemCategoryModelNameSpace = 'app\user\model\UsersGroup';
        $this->categoryListData = config('userCategoryListData');
        $this->categoryAllListData = config('userCategoryListData');
        
        $this->siteInfo = config('siteInfo');
    }
    public function itemAdd($data,$fieldList = [])
    {
        $uuid = uuid();
        $salt = create_salt(8);
        $resArray = array (
            'uid' => $uuid,
            'username' => $data['username'],
            'password' => create_md5($data['password'],$salt),
            'salt' => $salt,
            'reg_time' => time(),
            'reg_ip' => Request::instance()->ip(),
            'email' => $data['email'],
            'group_id' => 2,
            'mobile' => $data['mobile'],
            'rank' => 1,
            'terminal' => (isset($data['terminal']) && $data['terminal']) ? $data['terminal'] : 'pc',
        );
        db($this->item)->insert($resArray);
        return true;
    }
    
    public function itemLogin($data,$fieldList = [])
    {
        $request = Request::instance();
        if (!$data['username']) {
            return false;
        }
        if (!$data['password']) {
            return false;
        }
        if (preg_match("/^1[34578]{1}\d{9}$/",$data['username'])) {
            $userInfo = db($this->item)->where('mobile',$data['username'])->find();
            if (!$userInfo) {
                $userInfo = db($this->item)->where('username',$data['username'])->find();
            }
        } else {
            $userInfo = db($this->item)->where('username',$data['username'])->find();
        }
        if ($userInfo && $userInfo['password'] == create_md5($data['password'],$userInfo['salt'])) {
            $userInfo = db($this->item)->where('uid',$userInfo['uid'])->find();
            db($this->item)->where('uid',$userInfo['uid'])->update(array(
                'last_login_time' => time(),
                'last_login_ip' => Request::instance()->ip(),
                'login_num' => $userInfo['login_num'] + 1,
            ));
            return $userInfo;
        } else {
            return false;
        }
    }
    
    public function itemDel($uuid)
    {
        $itemInfo = db($this->item)->where('uid',$uuid)->find();
        if (!$itemInfo) {
            return false;
        }
        db($this->item)->where('uid',$uuid)->delete();
        return true;
    }
    
    public function getItemInfo($id = null,$userName = null)
    {
        if ($id) {
            $itemInfo = db($this->item)->where('uid',$id)->find();
        }
        if ($userName) {
            $itemInfo = db($this->item)->where('username',$userName)->find();
        }
        if ($itemInfo) {
            $itemInfo['groupInfo'] = model($this->itemCategoryModelNameSpace)->getCategoryInfo($itemInfo['cid']);
            $itemInfo['nickname'] = $itemInfo['nickname'] ? $itemInfo['nickname'] : $itemInfo['username'];
            $itemInfo['url'] = $this->getUrlByItemInfo($itemInfo);
            if (SITE_HOST) {
                $itemInfo['avatar'] =  $itemInfo['avatar'] ? $itemInfo['avatar'] : '/public/assets/common/images/avatar.jpg';
            } else {
                $itemInfo['avatar'] =  $itemInfo['avatar'] ? $itemInfo['avatar'] : '/assets/common/images/avatar.jpg';
            }
            $userInfo['password'] = null;
            $userInfo['salt'] = null;
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
        $orderBy = isset($tag['orderBy']) ? $tag['orderBy'] : 'publish_time';
        $order = isset($tag['order']) ? $tag['order'] : 'desc';
        $limit = isset($tag['limit']) ? $tag['limit'] : 10;
        $where = isset($tag['where']) ? $tag['where'] : null;
        
        $type = isset($tag['type']) ? $tag['type'] : '';
        
        if ($listType == 'list') {
            return $this->getItemList($cid, $page, $limit, $orderBy, $order, $where,$keywords,$whereArray);
        }
        if ($listType == 'pagination') {
            return $this->getPagination($cid, $page, $limit, $orderBy, $order, $where,$keywords,$whereArray,$tag['tagNames'],$tag['tagIds']);
        }
        
    }

    public function getItemList($cid = null, $page = 1, $perPage = 10, $orderBy = 'publish_time', $order = 'desc', $where = null,$keywords = null,$whereArray = null,$uuids = null)
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
            $keywordsWhere['username']  = ['like',$sq,'OR'];
        }
        if (empty($uuids)) {
            $uuidsWhere = null;
        } else {
            $uuids = explode(',',$uuids);
            $uuidsWhere['uuid']  = ['in',$uuids];
        }
        
        $itemList = [];
        if ($cid == '' || $cid == null) {
            $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($whereArray)->page($page,$perPage)->order($orderBy,$order)->select();
        } else {
            $itemList = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($whereArray)->where('group_id',$cid)->page($page,$perPage)->order($orderBy,$order)->select();
        }
        if ($itemList) {
            foreach($itemList as $k => $v) {
                $itemList[$k]['groupInfo'] = model($this->itemCategoryModelNameSpace)->getCategoryInfo($v['group_id']);
                $itemList[$k]['nickname'] = $itemList[$k]['nickname'] ? $itemList[$k]['nickname'] : $itemList[$k]['username'];
                if (SITE_HOST) {
                    $itemList[$k]['avatar'] =  $itemList[$k]['avatar'] ? $itemList[$k]['avatar'] : '/public/assets/common/images/avatar.jpg';
                } else {
                    $itemList[$k]['avatar'] =  $itemList[$k]['avatar'] ? $itemList[$k]['avatar'] : '/assets/common/images/avatar.jpg';
                }
                
            }
            foreach($itemList as $k => $v) {
                $itemList[$k]['url'] = $this->getUrlByItemInfo($v);
                $itemList[$k]['password'] = null;
                $itemList[$k]['salt'] = null;
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
            $keywordsWhere['username']  = ['like',$sq,'OR'];
        }
        if (empty($uuids)) {
            $uuidsWhere = null;
        } else {
            $uuids = explode(',',$uuids);
            $uuidsWhere['uuid']  = ['in',$uuids];
        }
        if ($cid == '' || $cid == null) {
            $count = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($whereArray)->page($page,$perPage)->count();
        } else {
            $itemCategoryList = db($this->itemCategory)->where('group_id',$cid)->select();
            if ($itemCategoryList) {
                foreach ($itemCategoryList as $key => $value) {
                    $cids[] = $value['id'];
                }
            }
            if ($itemCategoryList) {
                $count = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($whereArray)->whereOr('group_id',$cid)->whereOr('group_id','in',$cids)->page($page,$perPage)->count();
            } else {
                $count = db($this->item)->where($where)->where($keywordsWhere)->where($uuidsWhere)->where($whereArray)->where('group_id',$cid)->page($page,$perPage)->count();
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
        $domain = $domain ? $domain : config('domain');
        $res = $domain . '/user/' . $item['uid'] . '.html';
        return $res;
    }
    
    public function updateViews($id, $uid)
    {
        $tempCache = Cache::get('updateViews' . $this->itemType . md5(session_id()) . intval($id));
        if ($tempCache) {
            return false;
        }
        Cache::set('updateViews' . $this->itemType . md5(session_id()) . intval($id), time(), 60);
        db($this->item)->where('id',$id)->update([
            'views' => db($this->item)->where('id',$id)->find()['views'] + 1,
        ]);
        return true;
    }
}