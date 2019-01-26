<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\widget\controller;

use app\common\lib\Htmlp;
use think\Validate;
use app\common\controller\Base;
class ApiWidgetContact extends Base
{
    public function itemAdd()
    {
        if (!Request::instance()->isPost()) {
            exit;
        }
        $token = input('post.token');
        if (!$token) {
          return $this->error('缺少必备参数','');
        }
        $name = Htmlp::htmlp(input('post.name'));
        $tel = Htmlp::htmlp(input('post.tel'));
        $info = Htmlp::htmlp(input('post.info'));
        
        if (!$name) {
            $this->error('请输入姓名','');
        }
        if (!$tel) {
            $this->error('请输入电话号码','');
        }
        if (!$info) {
            $this->error('请输入留言内容','');
        }
        
        db('WidgetContact')->insert(array(
            'name' => $name,
            'tel' => $tel,
            'add_time' => time(),
            'info' => $info,
        ));
        $this->success('留言成功');
    }
     
}