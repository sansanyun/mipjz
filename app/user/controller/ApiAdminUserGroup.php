<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.
namespace app\user\controller;

use think\Request;
use app\common\controller\AdminBase;

class ApiAdminUserGroup extends AdminBase
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->itemModelNameSpace = 'app\user\model\Users';
        $this->itemName = $this->siteInfo['userModelName'];
        $this->item = 'Users';
        $this->itemCategory = 'UsersGroup';
        $this->itemType = 'users';
    }
    
    public function itemAdd()
    {
        $name = input('post.name');
        if (!$name) {
          return jsonError('请输入名称');
        }
        $itemCategoryInfo = db($this->itemCategory)->where('name',$name)->find();
        if ($itemCategoryInfo) {
            return jsonError('分类存在');
        }
        if (db($this->itemCategory)->insert(array(
            'name' => $name,
        ))) {
            return jsonSuccess('添加成功');
        } else {
            return  jsonError('添加失败');
        }
    }

    public function itemDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        if (db($this->itemCategory)->where('group_id',$id)->find()) {
            db($this->itemCategory)->where('group_id',$id)->delete();
            return jsonSuccess('删除成功');
        } else {
            return  jsonError('不存在');
        }

    }
    
    public function itemList()
    {
        $orderBy = input('post.orderBy');
        $order = input('post.order');
        if (!$orderBy) {
           $orderBy = 'sort';
        }
        if (!$order) {
            $order = 'asc';
        }
        $itemList = db('UsersGroup')->select();
        return jsonSuccess('',$itemList);
    }

    public function itemEdit()
    {
        $id = input('post.id');
        $name = input('post.name');
        if (!$id) {
            return jsonError('缺少ID');
        }
        if (!$name) {
            return jsonError('请输入名称');
        }

        $categoryInfo = db($this->itemCategory)->where('group_id',$id)->find();
        if(!$categoryInfo){
            return jsonError('分类不存在');
        }
        
        $itemInfo = db($this->itemCategory)->where('group_id','<>',$id)->where('name',$name)->find();
        if ($itemInfo) {
          return jsonError('名称已存在，请重新输入');
        }
        
        if (db($this->itemCategory)->where('group_id',$id)->update([
            'name' => $name,
        ])) {
            return  jsonSuccess('修改成功');
        } else {
            return  jsonError('修改失败');
        }
    }
}