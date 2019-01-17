<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.Com All rights reserved.
namespace addons\collectHuochetou\controller;
use app\common\controller\Init;
class AdminCollectHuochetou extends Init
{
    protected $beforeActionList = ['start'];
    protected $addonsName = '';
    public function start()
    {
        $addonsName = 'collectHuochetou'; //配置当前插件名称
        $this->addonsName = $addonsName;
        $itemInfo = db('Addons')->where('name',$addonsName)->find();
        if (!$itemInfo || $itemInfo['status'] != 1) {
            $this->error('当前插件未启用','');
        }
    }
    
    public function collectHuochetou()
    {
        return $this->addonsFetch('admin/AdminCollectHuochetou',$this->addonsName);
    }
}
