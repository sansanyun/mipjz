<?php
    namespace app\route;
    use think\Route;
    use think\Config;
    use think\DB;
    use think\Request;
    
    $request = Request::instance();
    if (!strpos($request->url(),'Api')) {
    	Route::group(Config::get('admin'), [
            '/' => 'admin/Admin/index',
            '/login' => 'admin/Login/index',
            '/componentjs' => 'admin/Admin/component',
            '/componentcss' => 'admin/Admin/componentcss',
            '/addons/addonsjs' => 'admin/Admin/addonsJs',
            '/addons/addonscss' => 'admin/Admin/addonsCss',
        ],[],[]);
    }