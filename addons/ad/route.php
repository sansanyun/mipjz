<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;
    
$taglib_pre_load = config('template')['taglib_pre_load'];
$taglib_pre_load = $taglib_pre_load . ',addons\\ad\\Mipjz';
$template = config('template');
$template['taglib_pre_load'] = $taglib_pre_load;
config('template',$template);
    
Route::rule('/ad/ApiAdminAd/:action' ,'\\addons\\ad\\controller\\ApiAdminAd@:action');
Route::rule(Config::get('admin') . '/ApiAdminAd/:action' ,'\\addons\\ad\\ApiAdminAd@:action');