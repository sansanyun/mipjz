<?php

namespace app\route;

use think\Route;

use think\Config;

use think\DB;

use think\Request;



Route::group('user', [

    ':action'  => ['user/User/account?name=:action'],

],[],[]);