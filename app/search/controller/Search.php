<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\search\controller;
use think\Request;
use think\Response;
use app\common\lib\Htmlp;
use app\common\controller\Base;
class Search extends Base
{
    public function index()
    {
        $keywords = Htmlp::htmlp(trim(input('param.q')," \t\n\r\0\x0B"));
        if (!$keywords) {
            return $this->error('请输入搜索的关键词','');
        }
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
            foreach ($tempIdsArray as $key => $value) {
                if ($key < 20) {
                    $itemList[] = model('app\article\model\Articles')->getItemInfo('',$value);
                }
            }
            $this->assign('itemList',$itemList);
        } else {
            $this->assign('itemList','');
        }
      
        //标题关键词描述
        $mipTitle = $keywords . $this->siteInfo['titleSeparator'] . $this->siteInfo['siteName'];
        $this->assign('mipTitle', $mipTitle);
        $this->assign('mipKeywords',$keywords);
        $this->assign('mipDescription',$keywords);
          
        return $this->mipView('search/search');
    }
 
    
}
