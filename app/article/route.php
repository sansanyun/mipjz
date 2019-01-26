<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;

if (!strpos($request->url(),'Api')) {
    $categoryList = model('app\\article\\model\\ArticlesCategory')->getCategory();
    if ($categoryList) {
        foreach ($categoryList as $key => $value) {
            Route::rule([$value['pageRule'] => ['article/Article/index?id=' . $value["id"] . '&cid=' . $value["id"],[],[]]]);
            
            Route::rule([str_replace('.html','',$value['detailRule']).$siteInfo['urlPageBreak'].'<page>' => ['article/ArticleDetail/index',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);

            Route::rule([str_replace('.html','',$value['detailRule']) => ['article/ArticleDetail/index',['__url__' => $value['detail__url__']],[]]]);
            
            if (strpos($value['rule'],'.html') !== false) {
                Route::rule([str_replace('.html','',$value['rule']) => ['article/Article/index?id=' . $value["id"] . '&cid=' . $value["id"],['ext'=>'html'],[]]]);
            } else {
                Route::rule([$value['rule'] => ['article/Article/index?id=' . $value["id"] . '&cid=' . $value["id"],[],[]]]);
            }

        }
        
    }
    Route::rule(['/article/index'. $siteInfo['urlPageBreak'].'<page>' => ['article/Article/index',['ext'=>'html'],['id'=>'[a-zA-Z0-9_-]+']]]);
    Route::rule(['/article/:id' => ['article/ArticleDetail/index',['ext'=>'html'],[],[]]]);
    Route::rule('article','article/Article/index');
}