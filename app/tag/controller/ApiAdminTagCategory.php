<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\tag\controller;

use app\common\controller\AdminBase;

class ApiAdminTagCategory extends AdminBase
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
    public function categoryAdd()
    {
        $data = $this->request->post();
        if (!$data['name']) {
            return jsonError('请输入名称');
        }
        if (!$data['url_name']) {
          return jsonError('请输入URL别名');
        }
        $itemCategoryInfo = db($this->itemCategory)->where('name',$data['name'])->find();
        if ($itemCategoryInfo) {
            return jsonError('添加项已存在');
        }
        $itemInfo = db($this->itemCategory)->where('url_name',$data['url_name'])->find();
        if ($itemInfo) {
            return jsonError('别名已存在，请重新输入');
        }
        $res = model($this->itemCategoryModelNameSpace)->categoryAdd($data);
        if ($res) {
            return jsonSuccess('操作成功');
        } else {
            return jsonError('操作失败');
        }
    }
    
    public function categoryEdit() {
        $data = $this->request->post();
        $itemInfo = db($this->itemCategory)->where('id',$data['id'])->find();
        if (!$itemInfo) {
            return jsonError('操作项不存在');
        }
        if (!$data['name']) {
            return jsonError('请输入名称');
        }
        if (!$data['url_name']) {
          return jsonError('请输入URL别名');
        }
        $itemCategoryInfo = db($this->itemCategory)->where('id','<>',$data['id'])->where('name',$data['name'])->find();
        if ($itemCategoryInfo) {
            return jsonError('名称已存在');
        }
        $itemInfo = db($this->itemCategory)->where('id','<>',$data['id'])->where('url_name',$data['url_name'])->find();
        if ($itemInfo) {
            return jsonError('别名已存在，请重新输入');
        }
        $res = model($this->itemCategoryModelNameSpace)->categoryEdit($data);
        if ($res) {
            return jsonSuccess('操作成功');
        } else {
            return jsonError('操作失败');
        }
    }

    public function categoryDel()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        if ($categoryInfo = db($this->itemCategory)->where('id',$id)->find()) {
            $itemInfo = db($this->item)->where('cid',$categoryInfo['id'])->find();
            if ($itemInfo) {
                return jsonError('删除的项中含有已发布的内容，系统无法删除');
            }
            db($this->itemCategory)->where('id',$id)->delete();
            return jsonSuccess('删除成功');
        } else {
            return  jsonError('不存在');
        }

    }
    
    public function categoryList()
    {
        $data = $this->request->post();
        $categoryList = model($this->itemCategoryModelNameSpace)->getCategory($data['pid'],$data['orderBy'],$data['order'],$data['limit']);
        return jsonSuccess('操作成功',['categoryList' => $categoryList]);
    }

    public function categorySortSave()
    {
        $itemList = input('post.itemList/a');
        if ($itemList) {
            foreach ($itemList as $key => $val) {
                if ($itemListInfo = db($this->itemCategory)->where('id',$val['id'])->find()) {
                    db($this->itemCategory)->where('id',$val['id'])->update(array('sort' => $val['sort']));
                }
                if ($itemList[$key]['children']) {
                    foreach ($itemList[$key]['children'] as $k => $v) {
                        if (db($this->itemCategory)->where('id',$v['id'])->find()) {
                            db($this->itemCategory)->where('id',$v['id'])->update(array('sort' => $v['sort']));
                        }
                    }
                }
            }
            return jsonSuccess('保存成功');
        }
        
    }

    public function getTemplate()
    {
        $pages = [];
        $template = config('template.view_path') . DS . $this->itemType;
        if (is_dir($template)) {
            $templateFile = opendir($template);
            if ($templateFile) {
                while (false !== ($file = readdir($templateFile))) {
                    if (substr($file, 0, 1) != '.' AND is_file($template . DS . $file)) {
                        $pages[] = $file;
                    }
                }
                closedir($templateFile);
            }
        }
        return jsonSuccess('',$pages);
    }
    
    public function switchStatus()
    {
        $id = input('post.id');
        if (!$id) {
          return jsonError('缺少参数');
        }
        if ($categoryInfo = db($this->itemCategory)->where('id',$id)->find()) {
            if ($categoryInfo['status'] == 2) {
                db($this->itemCategory)->where('id',$id)->update(array(
                    'status' => '',
                ));
            } else {
                db($this->itemCategory)->where('id',$id)->update(array(
                    'status' => 2,
                ));
            }
            return jsonSuccess('删除成功');
        } else {
            return  jsonError('操作失败');
        }
    }
}