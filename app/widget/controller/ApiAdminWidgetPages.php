<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\widget\controller;
use think\Request;

use app\common\controller\AdminBase;

class ApiAdminWidgetPages extends AdminBase
{
    public function itemAdd()
    {
        $title = input('post.title');
        $url_name = input('post.url_name');
        $template = input('post.template');
        $content = input('post.content');
        $keywords = input('post.keywords');
        $description = input('post.description');
        if (!$title) {
          return jsonError('请输入名称');
        }
        if (!$url_name) {
          return jsonError('请输入别名');
        }
        $itemInfo = db('WidgetPages')->where('title',$title)->find();
        if ($itemInfo) {
          return jsonError('名称已存在，请重新输入');
        }
        $itemInfo = db('WidgetPages')->where('url_name',$url_name)->find();
        if ($itemInfo) {
          return jsonError('别名已存在，请重新输入');
        }
        db('WidgetPages')->insert(array(
            'id' => uuid(),
            'title' => $title,
            'url_name' => $url_name,
            'template' => $template,
            'keywords' => $keywords,
            'description' => $description,
            'content' => htmlspecialchars($content),
        ));
        return jsonSuccess('成功');
    }
    
    public function itemEdit()
    {
        $id = input('post.id');
        $title = input('post.title');
        $url_name = input('post.url_name');
        $template = input('post.template');
        $content = input('post.content');
        $keywords = input('post.keywords');
        $description = input('post.description');
        if (!$id) {
          return jsonError('缺少参数');
        }
        if (!$title) {
          return jsonError('请输入名称');
        }
        if (!$url_name) {
          return jsonError('请输入别名');
        }
        $itemInfo = db('WidgetPages')->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('修改项不存在');
        }
        $itemInfo = db('WidgetPages')->where('id','<>',$id)->where('title',$title)->find();
        if ($itemInfo) {
          return jsonError('名称已存在，请重新输入');
        }
        $itemInfo = db('WidgetPages')->where('id','<>',$id)->where('url_name',$url_name)->find();
        if ($itemInfo) {
          return jsonError('别名已存在，请重新输入');
        }
        
        db('WidgetPages')->where('id',$id)->update(array(
                'title' => $title,
                'url_name' => $url_name,
                'keywords' => $keywords,
                'template' => $template,
                'description' => $description,
                'content' => htmlspecialchars($content),
        ));
        return jsonSuccess('成功');
    }
    
    public function itemDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db('WidgetPages')->where('id',$id)->find();
        if (!$itemInfo) {
          return jsonError('删除项不存在');
        }
        db('WidgetPages')->where('id',$id)->delete();
        return jsonSuccess('成功');
    }
    
    public function itemList()
    {
        $itemList = db('WidgetPages')->select();
        foreach ($itemList as $k => $v) {
            $itemList[$k]['content'] = htmlspecialchars_decode($itemList[$k]['content']);
            $itemList[$k]['url'] = '/' . $v['url_name'] . '.html';
        }
        $total = db('WidgetPages')->count();
        return jsonSuccess('',['itemList' => $itemList]);
    }
  
    
}