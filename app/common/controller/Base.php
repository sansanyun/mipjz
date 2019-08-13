<?php
namespace app\common\controller;

use think\Session;
use think\Request;
use app\common\controller\Init;

class Base extends Init
{
    public $domainSettingsInfo;
    public function _initialize()
    {
        parent::_initialize();
        $this->assign('siteTitle',$this->siteInfo['siteName']);
        $this->assign('mipTitle',$this->siteInfo['siteName']);

        $this->assign('siteKeywords',$this->siteInfo['keywords']);
        $this->assign('mipKeywords',$this->siteInfo['keywords']);

        $this->assign('siteDescription',$this->siteInfo['description']);
        $this->assign('mipDescription',$this->siteInfo['description']);
    }
    
    public function siteInfoInit()
    {
        $this->domainSettingsInfo = config('domainSettingsInfo');
        if ($this->domainSettingsInfo) {
            if ($this->domainSettingsInfo['siteName']) {
                $this->mipInfo['siteName'] = $this->domainSettingsInfo['siteName'];
            }
            if ($this->domainSettingsInfo['indexTitle']) {
                $this->mipInfo['indexTitle'] = $this->domainSettingsInfo['indexTitle'];
            }
            if ($this->domainSettingsInfo['keywords']) {
                $this->mipInfo['keywords'] = $this->domainSettingsInfo['keywords'];
            }
            if ($this->domainSettingsInfo['description']) {
                $this->mipInfo['description'] = $this->domainSettingsInfo['description'];
            }
            if ($this->domainSettingsInfo['icp']) {
                $this->mipInfo['icp'] = $this->domainSettingsInfo['icp'];
            }
            if ($this->domainSettingsInfo['statistical']) {
                $this->mipInfo['statistical'] = $this->domainSettingsInfo['statistical'];
            }
            if ($this->domainSettingsInfo['diySiteName']) {
                $this->mipInfo['diySiteName'] = $this->domainSettingsInfo['diySiteName'];
            }
            if ($this->domainSettingsInfo['mipApi']) {
                $this->mipInfo['mipApiAddress'] = $this->domainSettingsInfo['mipApi'];
            }
            if ($this->domainSettingsInfo['mipAutoStatus']) {
                $this->mipInfo['mipPostStatus'] = $this->domainSettingsInfo['mipAutoStatus'];
            }
            if ($this->domainSettingsInfo['ampApi']) {
                $this->mipInfo['ampApi'] = $this->domainSettingsInfo['ampApi'];
            }
            if ($this->domainSettingsInfo['ampAutoStatus']) {
                $this->mipInfo['ampAutoStatus'] = $this->domainSettingsInfo['ampAutoStatus'];
            }
            if ($this->domainSettingsInfo['xiongZhangStatus']) {
                $this->mipInfo['guanfanghaoStatus'] = $this->domainSettingsInfo['xiongZhangStatus'];
            }
            if ($this->domainSettingsInfo['xiongZhangId']) {
                $this->mipInfo['guanfanghaoCambrian'] = $this->domainSettingsInfo['xiongZhangId'];
            }
            if ($this->domainSettingsInfo['xiongZhangNewApi']) {
                $this->mipInfo['guanfanghaoRealtimeUrl'] = $this->domainSettingsInfo['xiongZhangNewApi'];
            }
            if ($this->domainSettingsInfo['xiongZhangNewAutoStatus']) {
                $this->mipInfo['guanfanghaoStatusPost'] = $this->domainSettingsInfo['xiongZhangNewAutoStatus'];
            }
            if ($this->domainSettingsInfo['xiongZhangOldApi']) {
                $this->mipInfo['guanfanghaoUrl'] = $this->domainSettingsInfo['xiongZhangOldApi'];
            }
            if ($this->domainSettingsInfo['yuanChuangApi']) {
                $this->mipInfo['baiduYuanChuangUrl'] = $this->domainSettingsInfo['yuanChuangApi'];
            }
            if ($this->domainSettingsInfo['yuanChuangAutoStatus']) {
                $this->mipInfo['baiduYuanChuangStatus'] = $this->domainSettingsInfo['yuanChuangAutoStatus'];
            }
            if ($this->domainSettingsInfo['linkApi']) {
                $this->mipInfo['baiduTimePcUrl'] = $this->domainSettingsInfo['linkApi'];
            }
            if ($this->domainSettingsInfo['linkAutoStatus']) {
                $this->mipInfo['baiduTimePcStatus'] = $this->domainSettingsInfo['linkAutoStatus'];
            }
            if ($this->domainSettingsInfo['baiduSearchKey']) {
                $this->mipInfo['biaduZn'] = $this->domainSettingsInfo['baiduSearchKey'];
            }
            if ($this->domainSettingsInfo['baiduSearchSiteMap']) {
                $this->mipInfo['baiduSearchPcUrl'] = $this->domainSettingsInfo['baiduSearchSiteMap'];
            }

            $siteInfo = $this->mipInfo;
            $this->siteInfo = $siteInfo;
            $this->assign('siteInfo',$siteInfo);
            config('siteInfo',$siteInfo);
            
            $mipInfo = $this->mipInfo;
            $this->mipInfo = $mipInfo;
            $this->assign('mipInfo',$mipInfo);
            config('mipInfo',$mipInfo);
        }

    }
    
    public function mipView($parent, $vars = [] ,$replace = [], $config = [])
    {
        return $this->siteView($parent, $vars ,$replace, $config);
    }
    
    public function siteView($parent, $vars = [] ,$replace = [], $config = [])
    {
        try {
            $diyPageList = db('DiyPage')->order('publish_time','desc')->limit(10)->select();
            if ($diyPageList) {
                foreach($diyPageList as $key => $val) {
                    $diyPageList[$key]['url'] = model('addons\diyPage\model\DiyPages')->getItemUrl($val['uuid']);
                }
            }
            $this->assign('diyPageList',$diyPageList);
        } catch (\Exception $e) {}
        
        $globalActionList = config('globalActionList');
        if ($globalActionList) {
            foreach ($globalActionList as $key => $val) {
                $addonsName = $val['name'];
                if (strpos($addonsName,"mipinit") === false) {
                    $addonsNameSpace = "addons" . "\\" . $addonsName . "\\" . "controller" . "\\" . "GlobalAction";
                    try {
                        model($addonsNameSpace)->$addonsName();
                    } catch (\Exception $e) {}
                }
            }
        }
        
        
        if ($this->siteInfo['codeCompression']) {
            return mipfilter(compress_html($this->fetch('/' .$parent,$vars, $config)));
        } else {
           return mipfilter($this->fetch('/' .$parent,$vars,$replace, $config));
        }
    }

}
