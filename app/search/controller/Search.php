<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\search\controller;
use think\Request;
use think\Response;
use app\common\lib\Htmlp;
use app\common\lib\Paginations;
use app\search\PSCWS4;
use app\common\controller\Base;
class Search extends Base
{
    public function index()
    {
        $keywords = Htmlp::htmlp(trim(input('param.q')," \t\n\r\0\x0B"));
        $page = Htmlp::htmlp(trim(input('param.page')," \t\n\r\0\x0B"));
        $limit = 10;
        if (!$keywords) {
            return $this->error('请输入搜索的关键词','');
        }
        $page = $page ? $page : 1;
        set_time_limit(30);
        $pscws = new PSCWS4();
        $pscws->set_dict(ROOT_PATH . 'app/search/scws/dict.utf8.xdb');
        $pscws->set_rule(ROOT_PATH . 'app/search/scws/rules.utf8.ini');
        $pscws->set_ignore(true);
        $pscws->send_text($keywords);
        $keywordsData = $pscws->get_tops();
        $pscws->close();
        if (!$keywordsData) {
            $keywordsData[] = ['word' => $keywords];
        }
        if ($keywordsData) {
            $likeWhere = null;
            foreach ($keywordsData as $key => $value) {
                $tempKeywords = $value['word'];
                $likeWhere[] = ['like','%'.$tempKeywords.'%'];
            }
            $tempArticlesList = [];
            $tempAsksList = [];
            $tempPostsList = [];
            try {
                $tempArticlesList = db('Articles')->whereOr('title',$likeWhere)->select();
                if ($tempArticlesList) {
                    foreach ($tempArticlesList as $key => $value) {
                        $num = 0;
                        foreach ($keywordsData as $k => $v) {
                            if (strpos($value['title'], $v['word']) !== false) {
                                $num = $num + 1;
                            }
                        }
                        $tempArticlesList[$key]['num'] = $num;
                        $tempArticlesList[$key]['type'] = 'article';
                    }
                }
            } catch (\Exception $e) {}
            try {
                $tempAsksList = db('Asks')->whereOr('title',$likeWhere)->select();
                if ($tempAsksList) {
                    foreach ($tempAsksList as $key => $value) {
                        $num = 0;
                        foreach ($keywordsData as $k => $v) {
                            if (strpos($value['title'], $v['word']) !== false) {
                                $num = $num + 1;
                            }
                        }
                        $tempAsksList[$key]['num'] = $num;
                        $tempAsksList[$key]['type'] = 'ask';
                    }
                }
            } catch (\Exception $e) {}
            try {
                $tempPostsList = db('Posts')->whereOr('title',$likeWhere)->select();
                if ($tempPostsList) {
                    foreach ($tempPostsList as $key => $value) {
                        $num = 0;
                        foreach ($keywordsData as $k => $v) {
                            if (strpos($value['title'], $v['word']) !== false) {
                                $num = $num + 1;
                            }
                        }
                        $tempPostsList[$key]['num'] = $num;
                        $tempPostsList[$key]['type'] = 'post';
                    }
                }
            } catch (\Exception $e) {}
            $tempList = array_merge($tempArticlesList,$tempAsksList,$tempPostsList);
        }
        if ($tempList) {
            $indexArray = [];
            foreach ($tempList as $key => $value) {
                   $indexArray[$key] = $value['num'];
            }
            arsort($indexArray);
            foreach ($indexArray as $key => $value) {
                $tempAllList[] = $tempList[$key];
            }
        }
        $tempList = $tempAllList;
        if ($tempList) {
            $allPageNum = ceil(count($tempList) / $limit);
            if ($page <= $allPageNum) {
                $tempList = array_slice($tempList,($page - 1) * $limit,$limit);
                if ($tempList) {
                    foreach ($tempList as $k => $v) {
                        if ($v['type'] == 'article') {
                            $itemList[] =  model('app\article\model\Articles')->getItemInfo('',$v['uuid']);
                        }
                        if ($v['type'] == 'ask') {
                            $itemList[] =  model('mod\ask\model\Asks')->getItemInfo('',$v['uuid']);
                        }
                        if ($v['type'] == 'post') {
                            $itemList[] =  model('mod\post\model\Posts')->getItemInfo('',$v['uuid']);
                        }
                    }
                    $count = count($tempAllList);
                }
            }
        }

        $this->assign('itemList',$itemList);
        
        $this->assign('keywords',$keywords);
        $pageText = ($page == 1) ? "" : $this->siteInfo['titleSeparator'] . "第" . $page . "页";
        //标题关键词描述
        $mipTitle = $keywords . $pageText . $this->siteInfo['titleSeparator'] . $this->siteInfo['siteName'];
        $this->assign('mipTitle', $mipTitle);
        $this->assign('mipKeywords',$keywords);
        $this->assign('mipDescription',$keywords);
        
        $baseUrl = $this->domain . '/search?q=' . $keywords . '&type=' . $type;
        $pagination_array = array(
            'base_url' => $baseUrl,
            'total_rows' => $count,
            'per_page' => $limit,
            'page_break' => '='
        );
        $pagination = new Paginations($pagination_array);
        $pagination = $pagination->create_links();
        $this->assign('pagination',$pagination);
          
        return $this->mipView('search/search');
    }
    
}
