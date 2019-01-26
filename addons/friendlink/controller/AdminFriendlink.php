<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.Com All rights reserved.
namespace addons\friendlink\controller;

use app\common\controller\AdminBase;
class AdminFriendlink extends AdminBase
{
    protected $beforeActionList = ['start'];
    protected $addonsName = '';
    public function start()
    {
        $addonsName = 'friendlink'; //配置当前插件名称
        $this->addonsName = $addonsName;
        $itemInfo = db('Addons')->where('name',$addonsName)->find();
        if (!$itemInfo || $itemInfo['status'] != 1) {
            $this->error('当前插件未启用','');
        }
    }
    
    public function friendlink()
    {
        return $this->addonsFetch('admin/friendlink',$this->addonsName);
    }
}
