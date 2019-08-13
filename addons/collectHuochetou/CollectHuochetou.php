<?php
namespace addons\collectHuochetou;

use think\Addons;
use mip\Init;
class CollectHuochetou extends Addons
{
    public $info = [
        'name' => 'collectHuochetou',
        'title' => '火车头插件',
        'description' => '详情请看管理说明',
        'status' => 0,
        'author' => '团长',
        'version' => '1.1.0',
        'adminUrl' => 'addons/collectHuochetou/AdminCollectHuochetou/collectHuochetou'
    ];
    
    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }
    
    public function collectHuochetouHook($param)
    {
        
        
        return $this->fetch();
    }
    
    
}
