<?php
namespace addons\friendlink;

use think\Addons;
use think\Request;
use mip\Init;
class Friendlink extends Addons
{
    public $info = [
        'name' => 'friendlink',
        'title' => '友情链接',
        'description' => '模板调用代码 {:hook("friendlinkHook")}',
        'status' => 0,
        'author' => '团长',
        'version' => '1.0.1',
        'adminUrl' => 'addons/friendlink/AdminFriendlink/friendlink'
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
