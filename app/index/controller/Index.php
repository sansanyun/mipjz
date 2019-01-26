<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\index\controller;
use think\Request;
use think\Response;
use think\Cache;
use app\common\controller\Base;

class Index extends Base
{
    public function index()
    {
        if ($this->domainSettingsInfo && $this->domainSettingsInfo['diySiteName']) {
            $this->assign('mipTitle',$this->domainSettingsInfo['diySiteName']);
        } else {
            $this->assign('mipTitle',$this->siteInfo['siteName'].$this->siteInfo['indexTitle']);
        }
        
        $jsonFile = config('template.view_path') . 'index/index.json';
        if (is_file($jsonFile)) {
            $jsonData = file_get_contents($jsonFile);
            $siteBlockListData = json_decode($jsonData,true);
            if ($siteBlockListData) {
                foreach ($siteBlockListData as $key => $value) {
                    $this->assign($key,$value);
                }
            }
        }
        
        $index = $this->siteView('index/index');
        return $index;
    }
    
    
    public function link()
    {
        $mipTitle = '站内链接' . $this->siteInfo['titleSeparator'] . $this->siteInfo['siteName'];
        $this->assign('mipTitle', $mipTitle);
        return $this->siteView('index/link');
    }
    
    
    
    
   function sitemap() {
        $count = model('app\article\model\Articles')->getCount(0);
        $tagsCount = db('Tags')->count();
        $pageNum = ceil($count / 200)+1;
        $tagPageNum = ceil($tagsCount / 200)+1;
        $sitemap = '<?xml version="1.0" encoding="utf-8"?>';
        $sitemap .= '<sitemapindex>';
        for ($i=1; $i < $pageNum; $i++) {
        $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . $this->domain . '/xml/' . $i . '.xml' . '</loc>';
            $sitemap .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
        $sitemap .= '</sitemap>';
        }
        for ($i=1; $i < $tagPageNum; $i++) {
        $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . $this->domain . '/tagXml/' . $i . '.xml' . '</loc>';
            $sitemap .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
        $sitemap .= '</sitemap>';
        }
        $sitemap .= '</sitemapindex>';
        return Response::create($sitemap)->contentType('text/xml');
    }
    
    function xml() {
        $page = input('param.id');
        $page = $page ? $page : 1;
        $itemList = model('app\article\model\Articles')->getItemList('', $page, 200, 'publish_time', 'desc');

        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<urlset>';
        if ($page == 1) {
            $xml .= '<url>';
            $xml .= '<loc>' . $this->domain . '/' . '</loc>';
            $xml .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>1.0</priority>';
            $xml .= '</url>';
            if ($this->itemCategoryList = model('app\article\model\ArticlesCategory')->getCategory()) {
                foreach($this->itemCategoryList as $k => $v) {
                    $xml .= '<url>';
                    $xml .= '<loc>' . $v["url"] . '</loc>';
                    $xml .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
                    $xml .= '<changefreq>daily</changefreq>';
                    $xml .= '<priority>0.9</priority>';
                    $xml .= '</url>';
                }
            }
        }
        foreach($itemList as $k => $v) {
            $xml .= '<url>';
            $xml .= '<loc>' . $v["url"] . '</loc>';
            $xml .= '<lastmod>' . date("Y-m-d", $v["publish_time"]) . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.6</priority>';
            $xml .= '</url>';
        }
        $xml .= '</urlset>';
        return Response::create($xml)->contentType('text/xml');;
    }


    function tagXml() {
        $page = input('param.id');
        $page = $page ? $page : 1;
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<urlset>';
        $tagsList = db('Tags')->page($page,200)->select();
        if ($tagsList) {
            foreach ($tagsList as $key => $val) {
                $tagsList[$key]['url'] = model('app\tag\model\Tags')->getUrlByItemInfo($val);
                $tagsList[$key]['time'] = $val['add_time'] ? date("Y-m-d", $val["add_time"]) : date("Y-m-d");
            }
            foreach ($tagsList as $key => $val) {
                $xml .= '<url>';
                $xml .= '<loc>' . $val["url"] . '</loc>';
                $xml .= '<lastmod>' . $tagsList[$key]['time'] . '</lastmod>';
                $xml .= '<changefreq>daily</changefreq>';
                $xml .= '<priority>0.9</priority>';
                $xml .= '</url>';
            }
        }
        $xml .= '</urlset>';
        return Response::create($xml)->contentType('text/xml');;
    }
    
     function baiduSitemapPc() {
        $count = model('app\article\model\Articles')->getCount(0);
        $pageNum = ceil($count / 200)+1;
        $sitemap = '<?xml version="1.0" encoding="utf-8"?>';
        $sitemap .= '<sitemapindex>';
        for ($i=1; $i < $pageNum; $i++) {
        $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . $this->domain . '/pcXml/' . $i . '.xml' . '</loc>';
            $sitemap .= '<lastmod>' . date("Y-m-d") . '</lastmod>';
        $sitemap .= '</sitemap>';
        }
        $sitemap .= '</sitemapindex>';
        return Response::create($sitemap)->contentType('text/xml');;
    }
    function pcXml() {
        $page = input('param.id');
        $page = $page ? $page : 1;
        $itemList = model('app\article\model\Articles')->getItemList('', $page, 200, 'publish_time', 'desc');

        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        $xml .= '<urlset>';
        foreach($itemList as $k => $v) {
            $xml .= '<url>';
            $xml .= '<loc>' . $v["url"] . '</loc>';
            $xml .= '<lastmod>' . date("Y-m-d", $v["publish_time"]) . '</lastmod>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.6</priority>';
            $xml .= '<data>';
            $xml .= '<display>';
            $xml .= '<title>' . $v['title'] . '</title>';
            $xml .= '</display>';
            $xml .= '</data>';
            $xml .= '</url>';
        }
        $xml .= '</urlset>';
        return Response::create($xml)->contentType('text/xml');;
    }
    
}
