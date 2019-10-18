<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;

if (strpos($request->url(),'Api') !== false) {
    Route::rule('/addons/:ctr/:act' ,'addons/:ctr/:act'); 
}
