<?php
namespace addons\spider;use think\Addons;
class Spider extends Addons{    public $info = [        'name' => 'spider',        'title' => '蜘蛛统计',        'description' => '蜘蛛统计（百度）',        'status' => 0,        'author' => '团长',        'version' => '1.1.0',        'adminUrl' => 'addons/spider/AdminSpider/spider',        'isGlobalAction' => 1,    ];
    public function install()    {        return true;    }
    public function uninstall()    {        return true;    }
}
