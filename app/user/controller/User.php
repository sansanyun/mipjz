<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.
namespace app\user\controller;use think\Request;use app\common\controller\Base;class User extends Base
{
    protected $beforeActionList = ['start'];    public function start()    {        $request = Request::instance();        if(!$this->userId){            $this->error('你尚未登录','/login/');        }    }
    public function account()    {        $name = input('param.name');        return $this->mipView('user/'.$name);
    }    
}