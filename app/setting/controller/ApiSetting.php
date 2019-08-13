<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.Com All rights reserved.
namespace app\setting\controller;
use think\Request;
use think\Controller;
class ApiSetting extends Controller
{
    public function itemInfo()
    {
        $settings = db('settings')->select();
        foreach ($settings as $k => $v){
            $this->siteInfo[$v['key']] = $v['val'];
        }
        return jsonSuccess('',$this->siteInfo);
    }
    public function status()
    {
    	header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range,access-token, secret-key,access-key,dataId,uid,sid,terminal,X-File-Name,Content-Disposition, Content-Description');
		
        return jsonSuccess('MIPCMS');
    }
}