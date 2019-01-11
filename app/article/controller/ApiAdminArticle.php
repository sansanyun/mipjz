<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\article\controller;

use app\common\controller\AdminBase;

class ApiAdminArticle extends AdminBase
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->item = 'Articles';
        $this->itemType = 'article';
        $this->itemName = '文章';
        $this->itemContent = 'ArticlesContent';
        $this->itemCategory = 'ArticlesCategory';
        $this->itemModelNameSpace = 'app\article\model\Articles';
        $this->itemCategoryModelNameSpace = 'app\article\model\ArticlesCategory';
        $this->categoryListData = config('articleCategoryListData');
        $this->categoryAllListData = config('articleCategoryListData');
    }
    
    public function itemAdd()
    {
        $data = $this->request->post();
        if (!isset($data['title']) || !$data['title']) {
            return jsonError('请输入标题');
        }
        $itemInfo = db($this->item)->where('title',$data['title'])->find();
        if ($itemInfo) {
            return jsonError('标题已存在');
        }
        if (isset($data['url_name']) && $data['url_name']) {
            $itemInfoByUrlName = db($this->item)->where('url_name',$url_name)->find();
            if ($itemInfoByUrlName) {
                return jsonError('自定义的Url已存在');
            }
        }
        if (!isset($data['content']) || !$data['content']) {
            return jsonError('请输入内容');
        }
        $fieldList = input('post.fieldList');
        $fieldList = json_decode($fieldList,true);
        $res = model($this->itemModelNameSpace)->itemAdd($data,$fieldList);
        if ($res) {
            return jsonSuccess('操作成功');
        } else {
            return jsonError('操作失败');
        }
    }
    
    public function itemEdit()
    {
		$data = $this->request->post();
        if (!isset($data['title']) || !$data['title']) {
            return jsonError('请输入标题');
        }
        $itemInfo = db($this->item)->where('uuid',$data['uuid'])->find();
        if (!$itemInfo) {
            return jsonError('不存在');
        }
        $itemInfo = db($this->item)->where('uuid','<>',$data['uuid'])->where('title',$data['title'])->find();
        if ($itemInfo) {
            return jsonError('标题已存在');
        }
        if (isset($data['url_name']) && $data['url_name']) {
            $itemInfoByUrlName = db($this->item)->where('uuid','<>',$data['uuid'])->where('url_name',$data['url_name'])->find();
            if ($itemInfoByUrlName) {
                return jsonError('自定义的Url已存在');
            }
        }
        if (!isset($data['content']) || !$data['content']) {
            return jsonError('请输入内容');
        }
        $fieldList = input('post.fieldList');
        $fieldList = json_decode($fieldList,true);
        $res = model($this->itemModelNameSpace)->itemEdit($data,$fieldList);
        if ($res) {
            return jsonSuccess('操作成功');
        } else {
            return jsonError('操作失败');
        }
    }
	
    public function itemDel()
    {
        $uuid = input('post.uuid');
        if (!$uuid) {
          return jsonError('缺少参数');
        }
        $itemInfo = db($this->item)->where('uuid',$uuid)->find();
        if (!$itemInfo) {
            return jsonError('删除项不存在');
        }
        $res = model($this->itemModelNameSpace)->itemDel($uuid);
        if ($res) {
            return jsonSuccess('操作成功');
        } else {
            return jsonError('操作失败');
        }
    }

    public function itemsDel()
    {
        $uuids = input('post.uuids');
        if (!$uuids) {
          return jsonError('缺少参数');
        }
        $uuids = explode(',',$uuids);
        foreach ($uuids as $uuid) {
            model($this->itemModelNameSpace)->itemDel($uuid);
        }
        return jsonSuccess('操作成功');
    }
    
    public function itemTransferAll()
    {
        $cid = input('post.cid');
        $ids = input('post.ids');
        if (!$ids) {
          return jsonError('缺少参数');
        }
        if ($cid == '') {
            $cid = 0;
        }
        $ids = explode(',',$ids);
        if (is_array($ids)) {
            foreach ($ids as $id){
                db($this->item)->where('id',$id)->update(['cid' => $cid]);
            }
            return jsonSuccess('操作成功');
        } else {
            return  jsonError('参数错误');
        }
    }
    
    public function itemFind()
    {
        $uuid = input('post.uuid');
        if (!$uuid) {
          return jsonError('缺少参数');
        }
        $itemInfo = model($this->itemModelNameSpace)->getItemInfo(null,$uuid);
        if (!$itemInfo) {
          return jsonError('不存在');
        }
        return jsonSuccess('',$itemInfo);
    }

    public function itemList()
    {
        $data = $this->request->post();
        $type = $data['type'];
        $where = null;
        if ($type) {
            if ($type == 'mipOk') {
                $where['mip_push_num'] = 1;
            }
            if ($type == 'mipNo') {
                $where['mip_push_num'] = null;
            }
            if ($type == 'xzhOk') {
                $where['xzh_push_num'] = 1;
            }
            if ($type == 'xzhNo') {
                $where['xzh_push_num'] = null;
            }
            if ($type == 'linkOk') {
                $where['link_push_num'] = 1;
            }
            if ($type == 'linkNo') {
                $where['link_push_num'] = null;
            }
            if ($type == 'collectOk') {
                $where['baidu_spider_num'] = 1;
            }
            if ($type == 'collectNo') {
                $where['baidu_spider_num'] = null;
            }
            
            
            if ($type == 'isPcOk') {
                $where['is_pc'] = 1;
            }
            if ($type == 'isPcNo') {
                $where['is_pc'] = 2;
            }
            if ($type == 'isPcEmpty') {
                $where['is_pc'] = null;
            }
            
            if ($type == 'isMOk') {
                $where['is_m'] = 1;
            }
            if ($type == 'isMNo') {
                $where['is_m'] = 2;
            }
            if ($type == 'isMEmpty') {
                $where['is_m'] = null;
            }
            
            if ($type == 'isMipOk') {
                $where['is_mip'] = 1;
            }
            if ($type == 'isMipNo') {
                $where['is_mip'] = 2;
            }
            if ($type == 'isMipEmpty') {
                $where['is_mip'] = null;
            }
            if ($type == 'isXzOk') {
                $where['is_xz'] = 1;
            }
            if ($type == 'isXzNo') {
                $where['is_xz'] = 2;
            }
            if ($type == 'isXzEmpty') {
                $where['is_xz'] = null;
            }
        }
        $itemList = model($this->itemModelNameSpace)->getItemList($data['cid'], $data['page'], $data['limit'], $data['orderBy'], $data['order'], $where, $data['keywords']);
        $itemCount = model($this->itemModelNameSpace)->getCount($data['cid'],null, $data['keywords']);
        if ($data['domain']) {
            if ($itemList) {
                foreach ($itemList as $key => $val) {
                    $itemList[$key]['url'] = model($this->itemModelNameSpace)->getUrlByItemInfo($val,$data['domain']);
                }
            }
        }
	    return jsonSuccess('',['itemList' => $itemList,'total' => $itemCount,'page' => $data['page']]);
    }
    
    public function itemRecomment()
    {
        $id = input('post.id');
        $itemInfo = db($this->item)->where('id',$id)->find();
        if (!$itemInfo) {
            return jsonError('不存在');
        }
        if ($itemInfo['is_recommend'] == 1) {
            $type = 0;
        } else {
            $type = 1;
        }
        db($this->item)->where('id',$id)->update([
            'is_recommend' => $type,
        ]);
        return  jsonSuccess('操作成功');
    }
    
}