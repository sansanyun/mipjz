<?php

//MIPJZ.Com [Don't forget the beginner's mind]

//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.

namespace app\user\controller;

use think\Request;
use think\Session;
use think\Validate;
use think\Cache;
use app\common\lib\Htmlp;
use app\common\controller\Base;

class ApiUser extends Base
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->item = 'Users';
        $this->itemType = 'user';
        $this->itemName = '用户';
        $this->itemContent = 'UsersContent';
        $this->itemCategory = 'UsersGroup';
        $this->itemModelNameSpace = 'app\user\model\Users';
        $this->itemCategoryModelNameSpace = 'app\user\model\UsersGroup';
        $this->categoryListData = config('userCategoryListData');
        $this->categoryAllListData = config('userCategoryListData');
        
        $this->siteInfo = config('siteInfo');
    }
     
    public function login()
    {
        $token = input('post.__token__');
        $data = [
            '__token__' => $token,
        ];
        $validate = new Validate([ 
            '__token__' =>'require|token',
        ]);
        if (!$validate->check($data)) {
            return jsonError($validate->getError());
        }
        
        $captcha = input('post.captcha');
        if ($this->siteInfo['loginCaptcha']) {
            if(!captcha_check($captcha)) {
                return jsonError('验证码错误');
            }
        }
        
        $data = $this->request->post();
        if (!isset($data['username']) || !$data['username']) {
            return jsonError('请输入用户名');
        }
        if (!isset($data['password']) || !$data['password']) {
            return jsonError('请输入密码');
        }
        $userInfo = model('app\user\model\Users')->itemLogin($data);
        if ($userInfo) {
            if (!$this->siteInfo['loginStatus']) {
                if ($userInfo['group_id'] != 1) {
                    return jsonError('本站已关闭登录');
                }
            }
            if($userInfo['status'] == 1) {
                return jsonError('你的账号被禁止登录');
            }
            @session::delete('userInfo');
            $session['uid']       = $userInfo['uid'];
            $session['username']  = $userInfo['username'];
            $userInfo['password'] = null;
            $userInfo['salt'] = null;
            session('userInfo',$session);
            return jsonSuccess('登录成功',$userInfo);
        } else {
            return jsonError('账号或密码不正确');
        }

    }
    public function reg()
    {
        if (!$this->siteInfo['registerStatus']) {
            return jsonError('该功能已被管理员关闭');
        }
        $token = input('post.__token__');
        $data = [
            '__token__' => $token,
        ];
        $validate = new Validate([ 
            '__token__' =>'require|token',
        ]);
        if (!$validate->check($data)) {
            return jsonError($validate->getError());
        }
        
        $captcha = input('post.captcha');
        if ($this->siteInfo['loginCaptcha']) {
            if(!captcha_check($captcha)) {
                return jsonError('验证码错误');
            }
        }
        
        $data = $this->request->post();
        
        $data['username'] = Htmlp::htmlp(trim($data['username']," \t\n\r\0\x0B"));

        $data['password'] = Htmlp::htmlp(trim($data['password']," \t\n\r\0\x0B"));

        $data['rpassword'] = Htmlp::htmlp(trim($data['rpassword']," \t\n\r\0\x0B"));
        
        $data['post.reference'] = Htmlp::htmlp(trim($data['post.reference']," \t\n\r\0\x0B"));
//      
//      if (!preg_match("/^1[34578]\d{9}$/", $data['username'])) {
//          return jsonError('用户名必须为手机号码');
//      }
        
        if (!isset($data['username']) || !$data['username']) {
            return jsonError('请输入用户名');
        }
        if (strlen($data['username']) > 24 || strlen($data['username']) < 2) {
            return jsonError('用户名长度不合法');
        }
        if (!isset($data['password']) || !$data['password']) {
            return jsonError('请输入密码');
        }
        if (!isset($data['rpassword']) || !$data['rpassword']) {
            return jsonError('请重复输入密码');
        }
        if ($data['password'] == 'd41d8cd98f00b204e9800998ecf8427e') {
            return jsonError('请输入密码');
        }
        if ($data['rpassword'] == 'd41d8cd98f00b204e9800998ecf8427e') {
            return jsonError('请重复输入密码');
        }
        if ($data['password'] != $data['rpassword']) {
            return jsonError('两次输入的密码必须相同');
        }
        $itemInfo = db($this->item)->where('username',$data['username'])->find();
        if ($itemInfo) {
            return jsonError('用户已存在');
        }
        if ($userInfo = model($this->itemModelNameSpace)->itemAdd($data)) {
            return jsonSuccess('注册成功');
        } else {
            return jsonError('注册失败');
        }
        
    }

    public function userVerification()
    {
        $mobile = Htmlp::htmlp(input('post.mobile'));
        if (!preg_match("/^1[34578]\d{9}$/", $mobile)) {
            return jsonError('请输入正确的手机号码');
        }
        if (!db('Users')->where('username',$mobile)->find() && !db('Users')->where('mobile',$mobile)->find()) {
            return jsonError('用户不存在');
        }
        return jsonSuccess('ok');
    }
    
    public function userForget()
    {
        $mobile = Htmlp::htmlp(input('post.mobile'));
        $password = Htmlp::htmlp(trim(input('post.password')," \t\n\r\0\x0B"));
        $rpassword = Htmlp::htmlp(trim(input('post.rpassword')," \t\n\r\0\x0B"));
        
        $token = input('post.__token__');
        $data = [
            '__token__' => $token,
        ];
        $validate = new Validate([ 
            '__token__' =>'require|token',
        ]);
        if (!$validate->check($data)) {
            return jsonError($validate->getError());
        }
        
        if (!$password) {
            return jsonError('请输入密码');
        }
        if (!$rpassword) {
            return jsonError('请重复输入密码');
        }
        if ($password != $rpassword) {
            return jsonError('两次输入的密码必须相同');
        }
        
        $tempCache = Cache::get('smsCode' . $mobile . md5(session_id()));
        if ($tempCache) {
            $userInfo = db('Users')->where('username',$mobile)->find();
            $md5Password = create_md5($password,$userInfo['salt']);
            db('Users')->where('username',$mobile)->update(array(
                'password' => $md5Password,
            ));
            return jsonSuccess('ok');
        } else {
            return jsonError('验证已过期');
        }
    }

}