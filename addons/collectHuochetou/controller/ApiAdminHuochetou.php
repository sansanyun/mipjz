<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.Com All rights reserved.
namespace addons\collectHuochetou\controller;
use think\Request;
class ApiAdminHuochetou extends AdminBase
{
    
    public function setKeywords()
    {
        if (!$keywords) {
            $keywords = 'www.mipjz.com';
        }
        db('key')->where('key','collect_huochetou')->update(array('val' => $keywords));
        return jsonSuccess('操作成功');
    }
    
    public function getKeywords()
    {
         
        return jsonSuccess('',db('key')->where('key','collect_huochetou')->find()['val']);
    }
 
}