<?php
namespace app\route;
use think\Route;
use think\Config;
use think\DB;
use think\Request;

$taglib_pre_load = config('template')['taglib_pre_load'];
$taglib_pre_load = $taglib_pre_load . ',addons\\friendlink\\Mipjz';
$template = config('template');
$template['taglib_pre_load'] = $taglib_pre_load;
config('template',$template);

Route::rule(Config::get('admin') . '/ApiAdminLink/:action' ,'addons\\friendlink\\controller\\ApiAdminLink@:action');