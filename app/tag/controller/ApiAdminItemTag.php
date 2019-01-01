<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\tag\controller;

use app\common\controller\AdminBase;

class ApiAdminItemTag extends AdminBase
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
        $this->itemTags = 'ItemTags';
        $this->categoryListData = config('tagCategoryListData');
        $this->categoryAllListData = config('tagCategoryListData');
    }
    
    public function itemTagsSelectByItem()
    {
        $itemId = input('post.itemId');
        if (!$itemId) {
            return jsonError('缺少类型Id');
        }
        $tagsList = db('ItemTags')->where('item_id',$itemId)->select();

        if ($tagsList) {
            foreach ($tagsList as $k => $v) {
                $tagsList[$k]['tags'] = db('Tags')->where('id',$v['tags_id'])->find();
            }
        }
        return jsonSuccess('',['itemList' => $tagsList]);
    }

    
}