<?php

//MIPJZ.Com [Don't forget the beginner's mind]

//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.

namespace app\user\controller;
use think\Request;
use think\Session;
use think\Db;
use think\Cache;
use app\common\controller\Base;
class ApiStatus extends Base
{

    public function loginStatus(Request $request)
    {
        if (!Request::instance() -> isPost()) {
            exit();
        }
        
//      $header = Request::instance()->header();
//      $AuthBase = new AuthBase();
//      
//      $accessTokenInfo = $AuthBase->auth($header['access-token'], $header['terminal'], $header['uid']);
//      if (!$accessTokenInfo || !Session::get('userInfo')) {
//          return jsonError('当前登录状态已失效，请备份当前编辑的内容，重新登录');
//      } else {
//          return jsonSuccess('ok');
//      }
        return jsonSuccess('ok');;
	}

    public function token()
    {
        $token = $this->request->token('__token__', 'sha1');
        return jsonSuccess('',$token);
    }
    
}