<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\setting\controller;
use think\Request;
use app\common\controller\AdminBase;class ApiAdminDomainSettings extends AdminBase{
    public function index() {

    }
    
    public function urlPost(Request $request) {
        $postAddress = input('post.postAddress');
        if (!$postAddress) {
            return jsonError('请先去设置推送的接口');
        }     	$api = trim($postAddress);				if (strpos($api,'type=realtime') !== false || strpos($api,'type=batch') !== false) {			if (!config('siteInfo')['guanfanghaoStatus']) {				return jsonError('检测到您未开启熊掌号，请开启后再推送');			}		}		        $url = input('post.url');        $id = input('post.id');
        if (!$url) {
            return jsonError('没有检测到你推送的页面地址');
        }			
        $urls[] = $url;        $ch = curl_init();        $options =  array(            CURLOPT_URL => $api,            CURLOPT_POST => true,            CURLOPT_RETURNTRANSFER => true,            CURLOPT_POSTFIELDS => implode("\n", $urls),            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),        );	        curl_setopt_array($ch, $options);	        $result = curl_exec($ch);	        curl_close($ch);		if ($result) {			$res = json_decode($result,true);			if (!isset($res['error'])) {				$itemInfo = db('Articles')->where('id',$id)->find();								if (strpos($api,'type=mip') !== false) {					db('Articles')->where('id',$id)->update(array(						'mip_push_num' => $itemInfo['mip_push_num'] + 1,					));				}				if (strpos($api,'type=realtime') !== false || strpos($api,'type=batch') !== false) {					db('Articles')->where('id',$id)->update(array(						'xzh_push_num' => $itemInfo['xzh_push_num'] + 1,					));				}								if (strpos($api,'type=realtime') === false && strpos($api,'type=batch') === false && strpos($api,'type=mip') === false && strpos($api,'type=amp') === false) {					db('Articles')->where('id',$id)->update(array(						'link_push_num' => $itemInfo['link_push_num'] + 1,					));				}							}		}				
     	return jsonSuccess($result);

    }
    
    public function itemEdit()
    {
        $id = input('post.id');
        $settingInfo = json_decode(input('post.setting'));
        foreach ($settingInfo as $key => $val) {
            db('domainSettings')->where('id',$id)->update(array($key => $val));
        }
        return jsonSuccess('成功');
    }
  
    public function itemFind()
    {
        $id = input('post.id');
        $itemInfo = db('domainSettings')->where('id',$id)->find();
        return jsonSuccess('成功',$itemInfo);
    }
      
}