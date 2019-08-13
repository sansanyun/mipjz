<?php
namespace app\route;
use think\Route;

if (!strpos($request->url(),'Api')) {
    try {
        $pages = db('WidgetPages')->select();
        if ($pages) {
            foreach ($pages as $key => $val) {
                Route::rule([$val['url_name'] => ['widget/Pages/index?url_name='.$val['url_name'],['ext'=>'html'],[]]]);
            }
        }
    } catch (\Exception $e) {}
}