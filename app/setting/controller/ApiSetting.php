<?php
//MIPCMS.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPCMS.Com All rights reserved.
namespace app\setting\controller;
use think\Request;
use think\Controller;
class ApiSetting extends Controller
{
    public function status()
    {
        return jsonSuccess('MIPCMS');
    }
}