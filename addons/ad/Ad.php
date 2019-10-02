<?php
namespace addons\ad;

use think\Controller;
class Ad extends Controller
{
    public $info = [
        'name' => 'ad',
        'title' => '广告管理',
        'description' => '广告插件',
        'status' => 0,
        'author' => '团长',
        'version' => '1.0.0',
        'adminUrl' => 'addons/ad/AdminAd/adList'
    ];
    
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }
    
    
}
