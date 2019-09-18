<?php

//MIPJZ.COM [Don't forget the beginner's mind]

//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.

namespace app\setting\controller;

use think\Request;

use app\common\controller\AdminBase;

class ApiAdminSetting extends AdminBase
{

    public function settingSelect()
    {
        $settings = db('settings')->select();
        foreach ($settings as $k => $v){
            $this->siteInfo[$v['key']] = $v['val'];
        }
        return jsonSuccess('',$this->siteInfo);
    }
    
    public function settingEdit()
    {
        $settingInfo = json_decode(input('post.setting'),true);
        if ($settingInfo) {
            $settingInfo['mipApiAddress'] = trim($settingInfo['mipApiAddress']);
            $settingInfo['guanfanghaoRealtimeUrl'] = trim($settingInfo['guanfanghaoRealtimeUrl']);
            $settingInfo['guanfanghaoUrl'] = trim($settingInfo['guanfanghaoUrl']);
            $settingInfo['baiduYuanChuangUrl'] = trim($settingInfo['baiduYuanChuangUrl']);
            $settingInfo['baiduTimePcUrl'] = trim($settingInfo['baiduTimePcUrl']);
        }
        foreach ($settingInfo as $key => $val) {
            db('settings')->where( "`key` = '" . $key . "'")->update(array('val' => $val));
        }
        return jsonSuccess('保存成功');
    }

    public function settingSave()
    {
        $domain = input('post.domain');
        $siteName = input('post.siteName');
        $httpType = input('post.httpType');
        db('settings')->where('key','domain')->update(array('val' => $domain));
        db('settings')->where('key','siteName')->update(array('val' => $siteName));
        db('settings')->where('key','httpType')->update(array('val' => $httpType));
        if (input('post.setting')) {
            $settingInfo = json_decode(input('post.setting'));
            foreach ($settingInfo as $key => $val) {
                db('settings')->where( "`key` = '" . $key . "'")->update(array('val' => $val));
            }
        }
        return jsonSuccess('保存成功');
    }

}