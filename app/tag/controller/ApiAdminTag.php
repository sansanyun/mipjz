<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\tag\controller;

use app\common\controller\AdminBase;

class ApiAdminTag extends AdminBase
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->item = 'Tags';
        $this->itemType = 'tag';
        $this->itemName = '标签';
        $this->itemContent = 'TagsContent';
        $this->itemCategory = 'TagsCategory';
        $this->itemModelNameSpace = 'app\tag\model\Tags';
        $this->itemCategoryModelNameSpace = 'app\tag\model\TagsCategory';
        $this->categoryListData = config('tagCategoryListData');
        $this->categoryAllListData = config('tagCategoryListData');
    }
    
    public function itemAdd()
    {
        $data = $this->request->post();
        if (!isset($data['name']) || !$data['name']) {
            return jsonError('请输入标题');
        }
        $itemInfo = db($this->item)->where('id',$data['id'])->find();
        if ($itemInfo) {
            return jsonError('标题已存在');
        }
        if (isset($data['url_name']) && $data['url_name']) {
            $itemInfoByUrlName = db($this->item)->where('url_name',$data['url_name'])->find();
            if ($itemInfoByUrlName) {
                return jsonError('自定义的Url已存在');
            }
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
        if (!isset($data['name']) || !$data['name']) {
            return jsonError('请输入标题');
        }
        $itemInfo = db($this->item)->where('id',$data['id'])->find();
        if (!$itemInfo) {
            return jsonError('不存在');
        }
        $itemInfo = db($this->item)->where('id','<>',$data['id'])->where('name',$data['name'])->find();
        if ($itemInfo) {
            return jsonError('标题已存在');
        }
        if (isset($data['url_name']) && $data['url_name']) {
            $itemInfoByUrlName = db($this->item)->where('id','<>',$data['id'])->where('url_name',$data['url_name'])->find();
            if ($itemInfoByUrlName) {
                return jsonError('自定义的Url已存在');
            }
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
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = db($this->item)->where('id',$id)->find();
        if (!$itemInfo) {
            return jsonError('删除项不存在');
        }
        $res = model($this->itemModelNameSpace)->itemDel($id);
        if ($res) {
            return jsonSuccess('操作成功');
        } else {
            return jsonError('操作失败');
        }
    }

    public function itemsDel()
    {
        $ids = input('post.ids');
        if (!$ids) {
          return jsonError('缺少参数');
        }
        $ids = explode(',',$ids);
        foreach ($ids as $id) {
            model($this->itemModelNameSpace)->itemDel($id);
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
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        $itemInfo = model($this->itemModelNameSpace)->getItemInfo(null,$id);
        if (!$itemInfo) {
          return jsonError('不存在');
        }
        return jsonSuccess('',$itemInfo);
    }

    public function itemList()
    {
        $data = $this->request->post();
        $itemList = model($this->itemModelNameSpace)->getItemList($data['cid'], $data['page'], $data['limit'], $data['orderBy'], $data['order'], null, $data['keywords']);
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
    
    public function push()
    {
        $postAddress = input('post.postAddress');
        if (!$postAddress) {
            return jsonError('请先去设置推送的接口');
        }
        $urls = input('post.urls');
        if (!$urls) {
            return jsonError('没有检测到你推送的页面地址');
        }
        $urls = explode(',',$urls);
        if (is_array($urls)) {
            $api = $postAddress;
            $ch = curl_init();
            $options =  array(
                CURLOPT_URL => $api,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => implode("\n", $urls),
                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            curl_close($ch);
            return jsonSuccess($result);
        } else {
            return jsonError('数据格式错误');
        }
    }
}