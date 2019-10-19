<?php
namespace addons\ad\model;
use think\Db;
use think\Controller;

class Ad extends Controller
{
    public function getItemListByTag($tag)
    {
        $tag = json_decode($tag,true);
        $name = isset($tag['name']) ? $tag['name'] : '';
        try {
        	$itemInfo = db('Ad')->where('name',$name)->find();
        } catch(\Exception $e) {}
        return htmlspecialchars_decode($itemInfo['content']);
    }

}