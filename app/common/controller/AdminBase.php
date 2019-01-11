<?php
namespace app\common\controller;

use think\Session;
use think\Request;
use app\common\controller\Init;

class AdminBase extends Init
{
    public function _initialize()
    {
        parent::_initialize();
        if (!$this->userId) {
            header('Content-type: application/json');  
            exit(json_encode(array('code' => -1 ,'msg' => '尚未登录，无法操作，请重新登录'),true));
        }
        if ($this->userInfo['group_id'] != 1) {
            header('Content-type: application/json');  
            exit(json_encode(array('code' => -1 ,'msg' => '非法请求'),true));
        }
        if (!Request::instance()->isPost()) {
            header('Content-type: application/json');  
            exit(json_encode(array('code' => -1 ,'msg' => '非法请求'),true));
        }
    }
    
}
