<?php
namespace app\route;

use think\Route;
use think\Config;
use think\Db;
use think\Request;

if (!is_file(PUBLIC_PATH . 'install' . DS .'install.lock')) {
    if (BAIDU) {
        Route::rule('/','install/BchInstall/index');
    } else {
        Route::rule('/','install/Install/index');
        Route::rule('/install','install/Install/index');
        Route::rule('/install/installPost','install/Install/installPost');
        Route::rule('/install/installPostOne','install/Install/installPostOne');
    }
} else {
    config('admin','admin'); //如果修改系统管理地址，请修改后一个admin即可
    $request = Request::instance();
	try {
		$zhanqunInfo = Db::connect([], '')->name('zhanqun')->where('domain',$request->server()['HTTP_HOST'])->find();
		if ($zhanqunInfo) {
	    	config('dataId',$zhanqunInfo['id']);
		}
	} catch (\Exception $e) {}
    $settings = db('Settings')->select();
    foreach ($settings as $k => $v) {
        $siteInfo[$v['key']] = $v['val'];
    }
    if (!strpos($request->url(),'Api')) {
        Route::group(Config::get('admin'), [
            '/' => 'admin/Admin/index',
            '/login' => 'admin/Login/index',
            '/componentjs' => 'admin/Admin/component',
            '/componentcss' => 'admin/Admin/componentcss',
            '/addons/addonsjs' => 'admin/Admin/addonsJs',
            '/addons/addonscss' => 'admin/Admin/addonsCss',
        ],[],[]);
    }
    $rewrite = $siteInfo['rewrite'] ? '' : '/index.php?s=';
    $domain = $request->domain() . $rewrite;
    if ($siteInfo['domain']) {
        $domain = $siteInfo['httpType'] . $siteInfo['domain'] . $rewrite;
    }
    if ($siteInfo['articleDomain']) {
        $domain =  $siteInfo['articleDomain'];
        if (strpos($domain{(strlen(trim($domain))-1)},'/') !== false) {
            $domain = substr($domain,0,strlen($domain)-1); 
        }
        $domain =  $domain . $rewrite;
    }
    $tplName = $siteInfo['template'];
    config('siteId','');
    if ($siteInfo['superSites']) {
        $siteDomainInfo = db('domainSites')->where('domain',$request->server()['HTTP_HOST'])->find();
        $siteList = db('domainSites')->select();
        if ($siteList) {
            config('superSiteList',$siteList);
            foreach ($siteList as $k => $v) {
                $siteDomainList[$v['id']] = $v['http_type'] . $v['domain'];
            }
            config('siteDomainList',$siteDomainList);
        }
        if ($siteDomainInfo) {
            $domain = $siteDomainInfo['http_type'] . $siteDomainInfo['domain'];
            $tplName = $siteDomainInfo['template'];
            config('siteId',$siteDomainInfo['id']);
            $domainSettingsInfo = db('domainSettings')->where('id',$siteDomainInfo['id'])->find();
            config('domainSettingsInfo',$domainSettingsInfo);
            if ($domainSettingsInfo) {
                if ($domainSettingsInfo['siteName']) {
                    $siteInfo['siteName'] = $domainSettingsInfo['siteName'];
                }
                if ($domainSettingsInfo['indexTitle']) {
                    $siteInfo['indexTitle'] = $domainSettingsInfo['indexTitle'];
                }
                if ($domainSettingsInfo['keywords']) {
                    $siteInfo['keywords'] = $domainSettingsInfo['keywords'];
                }
                if ($domainSettingsInfo['description']) {
                    $siteInfo['description'] = $domainSettingsInfo['description'];
                }
                if ($domainSettingsInfo['icp']) {
                    $siteInfo['icp'] = $domainSettingsInfo['icp'];
                }
                if ($domainSettingsInfo['statistical']) {
                    $siteInfo['statistical'] = $domainSettingsInfo['statistical'];
                }
                if ($domainSettingsInfo['diySiteName']) {
                    $siteInfo['diySiteName'] = $domainSettingsInfo['diySiteName'];
                }
                if ($domainSettingsInfo['mipApi']) {
                    $siteInfo['mipApiAddress'] = $domainSettingsInfo['mipApi'];
                }
                if ($domainSettingsInfo['mipAutoStatus']) {
                    $siteInfo['mipPostStatus'] = $domainSettingsInfo['mipAutoStatus'];
                }
                if ($domainSettingsInfo['ampApi']) {
                    $siteInfo['ampApi'] = $domainSettingsInfo['ampApi'];
                }
                if ($domainSettingsInfo['ampAutoStatus']) {
                    $siteInfo['ampAutoStatus'] = $domainSettingsInfo['ampAutoStatus'];
                }
                if ($domainSettingsInfo['xiongZhangStatus']) {
                    $siteInfo['guanfanghaoStatus'] = $domainSettingsInfo['xiongZhangStatus'];
                }
                if ($domainSettingsInfo['xiongZhangId']) {
                    $siteInfo['guanfanghaoCambrian'] = $domainSettingsInfo['xiongZhangId'];
                }
                if ($domainSettingsInfo['xiongZhangNewApi']) {
                    $siteInfo['guanfanghaoRealtimeUrl'] = $domainSettingsInfo['xiongZhangNewApi'];
                }
                if ($domainSettingsInfo['xiongZhangNewAutoStatus']) {
                    $siteInfo['guanfanghaoStatusPost'] = $domainSettingsInfo['xiongZhangNewAutoStatus'];
                }
                if ($domainSettingsInfo['xiongZhangOldApi']) {
                    $siteInfo['guanfanghaoUrl'] = $domainSettingsInfo['xiongZhangOldApi'];
                }
                if ($domainSettingsInfo['yuanChuangApi']) {
                    $siteInfo['baiduYuanChuangUrl'] = $domainSettingsInfo['yuanChuangApi'];
                }
                if ($domainSettingsInfo['yuanChuangAutoStatus']) {
                    $siteInfo['baiduYuanChuangStatus'] = $domainSettingsInfo['yuanChuangAutoStatus'];
                }
                if ($domainSettingsInfo['linkApi']) {
                    $siteInfo['baiduTimePcUrl'] = $domainSettingsInfo['linkApi'];
                }
                if ($domainSettingsInfo['linkAutoStatus']) {
                    $siteInfo['baiduTimePcStatus'] = $domainSettingsInfo['linkAutoStatus'];
                }
                if ($domainSettingsInfo['baiduSearchKey']) {
                    $siteInfo['biaduZn'] = $domainSettingsInfo['baiduSearchKey'];
                }
                if ($domainSettingsInfo['baiduSearchSiteMap']) {
                    $siteInfo['baiduSearchPcUrl'] = $domainSettingsInfo['baiduSearchSiteMap'];
                }
            }
        }
    }
    
    $domainStatic = str_replace('/index.php?s=', '', $domain);
    config('domainStatic',$domainStatic);
    $domains = $domainStatic . '/index.php?s=';
    config('domains',$domains);
    config('domain',$domain);
    config('mipauthorization',false);
    config('siteInfo',$siteInfo);
    if ($request->isMobile()) {
        if ($siteInfo['superTpl']) {
            $tplName = 'm';
        }
    }
    $tplName = $tplName ? $tplName : 'default';
    config('tplName',$tplName);
    config('template.view_path',config('template.view_path') . $tplName . DS);
    config('routeStatus',true);
    foreach (fetch_dir(ROOT_PATH . 'addons' . DS) as $key => $dir) {
        if (is_file($dir . DS . 'route.php')) {
            require $dir . DS . 'route.php';
        }
    }
    if (config('routeStatus')) {
        foreach (fetch_dir(APP_PATH) as $key => $dir) {
            if (is_file($dir . DS . 'route.php')) {
                require $dir . DS . 'route.php';
            }
        }
    }
}