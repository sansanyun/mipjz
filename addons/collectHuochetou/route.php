<?php
namespace app\route;use think\Route;use think\Config;

Route::rule('collectHuochetou/ApiAdminHuochetou/:action' ,'\\addons\\collectHuochetou\\controller\\ApiAdminHuochetou@:action');
Route::rule('collect/ApiUserHuochetou/articleAdd' ,'\\addons\\collectHuochetou\\controller\\ApiUserCollectHuochetou@articleAdd');
Route::rule('collect/ApiUserHuochetou/articleAddTime' ,'\\addons\\collectHuochetou\\controller\\ApiUserCollectHuochetou@articleAddTime');
Route::rule('collect/ApiUserShenjianshou/articleAdd' ,'\\addons\\collectHuochetou\\controller\\ApiUserShenjianshou@articleAdd');
Route::rule('collect/ApiUserShenjianshou/articleAddTime' ,'\\addons\\collectHuochetou\\controller\\ApiUserShenjianshou@articleAddTime');