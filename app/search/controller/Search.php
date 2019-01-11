<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\search\controller;
use think\Request;
use think\Response;
use app\common\lib\Htmlp;
use app\common\lib\Paginations;
use app\common\controller\Base;
class Search extends Base
{
    public function index()
    {
        $keywords = Htmlp::htmlp(trim(input('param.q')," \t\n\r\0\x0B"));
        $page = Htmlp::htmlp(trim(input('param.page')," \t\n\r\0\x0B"));
        $page = $page ? $page : 1;
        if (!$keywords) {
            return $this->error('请输入搜索的关键词','');
        }
        $limit = 10;
        $postData = array(
            "data"  => $keywords,
            'respond' => 'json',
        );
        set_time_limit(30);
        $result = linkClient('http://www.xunsearch.com/scws/api.php',$postData);
        if ($result) {
            $res = json_decode($result,true);
            if ($res['status'] == 'ok') {
                $data = $res['words'];
                if ($data) {
                    $likeWhere = null;
                    foreach ($data as $key => $value) {
                        $tempKeywords = $value['word'];
                        $likeWhere[] = ['like','%'.$tempKeywords.'%'];
                    }
                    $tempList = db('Articles')->whereOr('title',$likeWhere)->select();
                    $count = db('Articles')->whereOr('title',$likeWhere)->count();
                    if ($tempList) {
                        $tempArray = [];
                        foreach ($tempList as $key => $value) {
                            $num = 0;
                            foreach ($data as $k => $v) {
                                if (strpos($value['title'], $v['word']) !== false) {
                                    $num = $num + 1;
                                }
                            }
                            $tempArray[$value['uuid']] = $num;
                        }
                        arsort($tempArray);
                        $tempIdsArray = [];
                        if ($tempArray) {
                            foreach ($tempArray as $key => $value) {
                                $tempIdsArray[] = $key;
                            }
                        }
                    }
                }
            }
        }

        $this->assign('keywords',$keywords);
        if ($tempIdsArray) {
            $allPageNum = ceil(count($tempIdsArray) / $limit);
            if ($page <= $allPageNum) {
                $newIds = array_slice($tempIdsArray,($page - 1) * $limit,$limit);
                if ($newIds) {
                    foreach ($newIds as $uuid) {
                        $itemList[] =  model('app\article\model\Articles')->getItemInfo('',$uuid);
                    }
                    $itemCount = count($tempIdsArray);
                }
            }
            $this->assign('itemList',$itemList);
        } else {
            $this->assign('itemList','');
        }
      
        $pageText = ($page == 1) ? "" : $this->siteInfo['titleSeparator'] . "第" . $page . "页";
        //标题关键词描述
        $mipTitle = $keywords . $pageText . $this->siteInfo['titleSeparator'] . $this->siteInfo['siteName'];
        $this->assign('mipTitle', $mipTitle);
        $this->assign('mipKeywords',$keywords);
        $this->assign('mipDescription',$keywords);
        
        $baseUrl = $this->domain . '/search?q=' . $keywords;
        $pagination_array = array(
            'base_url' => $baseUrl,
            'total_rows' => $itemCount,
            'per_page' => $limit,
            'page_break' => '='
        );
        $pagination = new Paginations($pagination_array);
        $pagination = $pagination->create_links();
        $this->assign('pagination',$pagination);
          
        return $this->mipView('search/search');
    }
 
    
}
