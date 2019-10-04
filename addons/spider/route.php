<?php
namespace app\route;use think\Route;use think\Config;use think\DB;use think\Request;
Route::rule(Config::get('admin') . '/ApiAdminSpider/:action' ,'\\addons\\spider\\controller\\ApiAdminSpider@:action');