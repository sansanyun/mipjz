<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\widget\controller;

use app\common\controller\AdminBase;

class ApiAdminWidgetNav extends AdminBase
{
    protected $beforeActionList = ['start'];
    public function start()
    {
        
    }
    
    public function itemAdd()
    {
        $data = input('post.itemLink/a');
        if (!isset($data['name']) || !$data['name']) {
            return jsonError('请输入名称');
        }
        db('WidgetNav')->insert(array(
            'name' => $data['name'],
            'url' => $data['url'],
            'target' => $data['target'],
            'type' => $data['type'],
            'sort' => 99,
        ));
        return jsonSuccess('操作成功');
    }
    
    public function itemEdit()
    {
        $data = input('post.itemLink/a');
        if (!isset($data['name']) || !$data['name']) {
            return jsonError('请输入标题');
        }
        db('WidgetNav')->where('id',$data['id'])->update(array(
            'name' => $data['name'],
            'url' => $data['url'],
            'target' => $data['target'],
            'type' => $data['type'],
        ));
        return jsonSuccess('操作成功');
    }
	
    public function itemDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db('WidgetNav')->where('id',$id)->find();
        if (!$itemInfo) {
            return jsonError('删除项不存在');
        }
        db('WidgetNav')->where('id',$id)->delete();
        return jsonSuccess('操作成功');
    }
      
    public function itemList()
    {
        $data = $this->request->post();
        $itemList = db('WidgetNav')->order('sort','asc')->select();
	    return jsonSuccess('',$itemList);
    }
    
    public function itemSortSave()
    {
        $itemList = input('post.itemList/a');
        if ($itemList) {
            foreach ($itemList as $key => $val) {
                if ($itemListInfo = db('WidgetNav')->where('id',$val['id'])->find()) {
                    db('WidgetNav')->where('id',$val['id'])->update(array('sort' => $val['sort']));
                }
            }
        }
        return jsonSuccess('保存成功');
    }
    
}