<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.Com All rights reserved.
namespace app\article\controller;
use think\Request;
use app\common\controller\Base;
class ApiArticle extends Base
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->itemModelNameSpace = 'app\article\model\Articles';
        $this->itemCategoryModelNameSpace = 'app\article\model\ArticlesCategory';
        $this->item = 'Articles';
        $this->itemType = 'article';
    }
    
    public function getCategoryList()
    {
        $data = $this->request->param();
        $data['orderBy'] = $data['orderBy'] ? $data['orderBy'] : 'sort';
        $data['pid'] = $data['cid'] ? $data['pid'] : '0';
        $categoryList = model($this->itemCategoryModelNameSpace)->getCategory($data['pid'],$data['orderBy'],$data['order'],$data['limit']);
        $html = '<ul>';
        foreach ($categoryList as $key => $value) {
            $html .= "<li>" . $value['id'] . "---" . $value['name'] .  "</li>";
        }
        $html .= '</ul>';
        return $html;
    }

    public function categoryList()
    {
        $data = $this->request->param();
        $data['orderBy'] = $data['orderBy'] ? $data['orderBy'] : 'sort';
        $categoryList = model($this->itemCategoryModelNameSpace)->getCategory(0,$data['orderBy'],$data['order'],$data['limit']);
        if ($categoryList) {
        	foreach ($categoryList as $key => $val) {
        		$categoryList[$key]['title'] = isset($val['name']) ? $val['name'] : $val['title'];
        		$categoryList[$key]['model_name'] = $val['is_page'] ? 'page' : '';
        	}
        }
        return jsonSuccess('操作成功',['categoryList' => $categoryList]);
    }
    public function itemList()
    {
      	$data = $this->request->param();
        $patern = '/^^((https|http|ftp)?:?\/\/)[^\s]+$/';
        $type = $data['type'];
        $itemList = model($this->itemModelNameSpace)->getItemList($data['cid'], $data['page'], $data['limit'], $data['orderBy'], $data['order'], $where, $data['keywords']);
        $itemCount = model($this->itemModelNameSpace)->getCount($data['cid'], $data['keywords']);
        if ($data['domain']) {
            if ($itemList) {
                foreach ($itemList as $key => $val) {
                    $itemList[$key]['url'] = model($this->itemModelNameSpace)->getUrlByItemInfo($val,$data['domain']);
                }
            }
        }
        if ($itemList) {
            foreach ($itemList as $key => $val) {
                $itemList[$key]['publish_date'] = date('Y-m-d H:i:s',$itemList[$key]['publish_time']);
                if ($itemList[$key]['firstImg']) {
                    if (!preg_match($patern,$itemList[$key]['firstImg'])) {
                        $itemList[$key]['firstImg'] = $this->domain . $itemList[$key]['firstImg'];
                    }
                }
            }
        }
        
	    return jsonSuccess('',['itemList' => $itemList,'total' => $itemCount,'page' => $data['page']]);
    }
    
    
     public function getItemInfo()
    {
        $id = input('param.id');
        if ($id) {
            $itemInfo = db($this->item)->where('id',$id)->find();
        } else {
            return  jsonError('内容不存在');
        }
        $itemInfo = model($this->itemModelNameSpace)->getItemInfo($id);
        $itemInfo['content'] = model($this->itemModelNameSpace)->getContentByArticleInfo($itemInfo);
        $itemInfo['publish_date'] = date('Y-m-d H:i:s',$itemInfo['publish_time']);
        $patern = '/^^((https|http|ftp)?:?\/\/)[^\s]+$/';
        preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArrays);
        if ($imagesArrays) {
            foreach($imagesArrays[1] as $k => $v) {
                if (!preg_match($patern,$imagesArrays[1][$k])) {
                    $srcUrl = $this->domain . $imagesArrays[1][$k];
                    $itemInfo['content'] = str_replace($imagesArrays[1][$k],$srcUrl,$itemInfo['content']);
                }
            }
        }
        
        return jsonSuccess('',$itemInfo);
    }
}