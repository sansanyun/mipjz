<?php
namespace app\admin\controller;

use think\Response;
use think\Controller;

use app\common\controller\Base;

class Login extends Base
{

    public function index()
    {
        return $this->fetch('admin@./login');
    }
}
