<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\tag\model;
use think\Cache;
use app\common\lib\Paginationm;
use app\common\lib\ChinesePinyin;
use think\Db;
use think\Controller;

class ItemTags extends Controller
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
    
    public function innerTags($tags, $itemType, $itemInfo)
    {

        $itemId = $itemInfo['uuid'];
        $itemUid = $itemInfo['uid'];
        $publish_time = $itemInfo['publish_time'] ? $itemInfo['publish_time'] : time();
        if (!is_array($tags)) {
            return false;
        }
        if (is_array($tags)) {
            db('ItemTags')->where('item_id',$itemId)->delete();
            foreach ($tags as $name) {
                if ($name) {
                    $tagInfo = db('Tags')->where('name',$name)->find();
                    if (!$tagInfo) {
                        $result =  getPinyin($name);
                        if ($result) {
                            if (!db('Tags')->where('url_name',$result)->find()) {
                                db('Tags')->insert(array(
                                    'id' => uuid(),
                                    'name' => $name,
                                    'url_name' => $result,
                                ));
                            } else {
                                 db('Tags')->insert(array(
                                    'id' => uuid(),
                                    'name' => $name,
                                ));
                            }
                        } else {
                            db('Tags')->insert(array(
                                'id' => uuid(),
                                'name' => $name,
                            ));
                        }
                        $tagInfo = db('Tags')->where('name',$name)->find();
                    }
                    db('itemTags')->insert(array(
                        'id' => uuid(),
                        'tags_id'=>$tagInfo['id'],
                        'item_type' => $itemType,
                        'item_id' => $itemId,
                        'item_add_time' => $publish_time,
                    ));
                    $tagsCount = db('itemTags')->where('tags_id',$tagInfo['id'])->count();
                    if ($tagsCount) {
                        db('Tags')->where('id',$tagInfo['id'])->update(array(
                            'relevance_num' => $tagsCount,
                            'creator_uid' => $itemUid,
                            'add_time' => time(),
                        ));
                    }
                }
            }
        }
        return true;
    }

}