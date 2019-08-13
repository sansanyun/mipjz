<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\widget\controller;
use app\common\lib\Htmlp;
use app\common\controller\AdminBase;
class ApiAdminWidgetContact extends AdminBase
{
    
    public function itemDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db('WidgetContact')->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('删除项不存在');
        }
        db('WidgetContact')->where('id',$id)->delete();
        return jsonSuccess('成功');
    }
    
    public function itemList()
    {
        $status = input('post.status');
        $orderBy = input('post.orderBy');
        $order = input('post.order');
        $page = input('post.page');
        $limit = input('post.limit');
        $limit ? $limit : 10;
        if(!$orderBy) {
           $orderBy = 'add_time';
        }
        if(!$order) {
            $order = 'desc';
        }
        $itemList = db('WidgetContact')->page($page,$limit)->order($orderBy,$order)->select();
        foreach ($itemList as $k => $v) {
            $itemList[$k]['content'] = htmlspecialchars_decode($itemList[$k]['content']);
        }
        $total = db('WidgetContact')->count();
        return jsonSuccess('',['itemList' => $itemList,'total' => $total]);
    }
 
}