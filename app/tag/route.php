<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;

$request = Request::instance();
$categoryListData = [];
$categoryAllListData = [];
$categoryAllListData = db('TagsCategory')->order('sort asc')->select();
if ($categoryAllListData) {
    foreach ($categoryAllListData as $key => $value) {
        $categoryListData[$value['id']] = $value;
    }
}
config('tagCategoryAllListData',$categoryAllListData);
config('tagCategoryListData',$categoryListData);
$tag = config('siteInfo')['tagModelUrl'];
if (!strpos($request->url(),'Api')) {
    Route::rule(['/' . $tag . '/:id/index_<page>' => ['tag/TagDetail/index',['ext'=>'html'],['page'=>'[a-zA-Z0-9_-]+']]]);
    Route::rule(['/' . $tag . '/index_<page>' => ['tag/Tag/index',['ext'=>'html'],['page'=>'[a-zA-Z0-9_-]+']]]);
    Route::rule(['/' . $tag . '/:name' => ['tag/Tag/index',['ext'=>'html'],['name'=>'[a-zA-Z0-9_-]+'],[]]]);
    Route::rule(['/' . $tag . '/:id' => ['tag/TagDetail/index',[],[]]]);
    Route::rule('/' . $tag . '/','tag/Tag/index');
}

if (strpos($request->url(),'Api') !== false) {
    Route::rule('/tag/:ctr/:act' ,'tag/:ctr/:act'); 
}
