<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.
namespace app\user\controller;

use think\Request;
use app\common\controller\AdminBase;

class ApiAdminUser extends AdminBase
{
    protected $beforeActionList = ['start'];
    public function start() {
        $this->itemModelNameSpace = 'app\user\model\Users';
        $this->itemName = $this->siteInfo['userModelName'];
        $this->item = 'Users';
        $this->itemCategory = 'UsersGroup';
        $this->itemType = 'users';
    }
    
    public function itemAdd()
    {
        $data = $this->request->post();
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
        $uuid = uuid();
        $salt = create_salt(8);
        $resArray = array (
            'uid' => $uuid,
            'username' => $data['username'],
            'password' => create_md5($data['password'],$salt),
            'salt' => $salt,
            'reg_time' => time(),
            'reg_ip' => Request::instance()->ip(),
            'email' => $data['email'],
            'group_id' => $data['groupId'],
            'mobile' => $data['mobile'],
            'rank' => 1,
            'terminal' => (isset($data['terminal']) && $data['terminal']) ? $data['terminal'] : 'pc',
        );
        if (db($this->item)->insert($resArray)) {
            return jsonSuccess('添加成功');
        } else {
            return jsonError('添加失败');
        }
        
    }

    public function itemEdit()
    {
        $uid = input('post.uid');
        $groupId = input('post.groupId');
        $username = input('post.username');
        $password = input('post.password');
        $rpassword = input('post.rpassword');
        
        if (!$uid || !$groupId) {
            return jsonError('缺少参数');
        }
       if (!$username) {
            return jsonError('请填写用户名');
        } else {
            if (strlen($username) > 24 || strlen($username) < 2) {
                return jsonError('用户名长度不合法');
            }
        }
        if ($password && !$rpassword) {
            return jsonError('请重复输入密码');

        }
        
        $userInfo = db('Users')->where('uid',$uid)->find();
        if (!$userInfo) {
            return jsonError('用户不存在');
        }
        if ($password != 'd41d8cd98f00b204e9800998ecf8427e' && $rpassword != 'd41d8cd98f00b204e9800998ecf8427e') {
            if ($password != $rpassword) {
                return jsonError('两次输入的密码必须相同');
            } 
            $salt = create_salt(8);
            $password = create_md5($password,$salt);
            $usersInfo = db($this->item)->where('uid',$userInfo['uid'])->update(array(
                'username' => $username,
                'salt' => $salt , 
                'password' => $password, 
                'group_id' => $groupId,
                ));
        } else {
           $usersInfo = db($this->item)->where('uid',$userInfo['uid'])->update(array(
            'username' => $username,
            'group_id' => $groupId,
            ));
        }
        
        return  jsonSuccess('修改成功');
    }
    public function itemDel()
    {
        $uid = input('post.uid');
        if (!$uid) {
          return jsonError('缺少参数');
        }
        $itemInfo = db('Users')->where('uid',$uid)->find();
        if (!$itemInfo) {
            return jsonError('用户不存在');
        }
        if (!$itemInfo['username']) {
            return jsonError('用户不存在');
        }
        if (db('Users')->where('uid',$uid)->delete()) {
            return jsonSuccess('删除成功');
        } else {
            return  jsonError('不存在');
        }

    }
    
    public function itemsDel()
    {
        $uids = input('post.uids');
        if (!$uids) {
          return jsonError('缺少参数');
        }
        $uids = explode(',',$uids);
        if (is_array($uids)) {
            foreach ($uids as $uid){
                $itemInfo = db($this->item)->where('uid',$uid)->find();
                if ($itemInfo) {
                   db($this->item)->where('uid',$uid)->delete();
                }
            }
            return jsonSuccess('删除成功');
        } else {
               return jsonError('参数错误');
        }

    }
    
    public function itemList()
    {
        $page = input('post.page');
        $limit = input('post.limit');
        $orderBy = input('post.orderBy');
        $order = input('post.order');
        $cid = input('post.cid');
        $keywords = input('post.keywords');
        $domain = input('post.domain');
        if (!$page) {
          $page = 1;
        }
        if (!$limit) {
          $limit = 10;
        }
        if (!$orderBy) {
           $orderBy = 'reg_time';
        }
        if (!$order) {
            $order = 'desc';
        }
        $itemList = model($this->itemModelNameSpace)->getItemList($cid,$page,$limit,$orderBy,$order,null,$keywords);
        $itemCount = model($this->itemModelNameSpace)->getCount($cid,'', $keywords);
        return jsonSuccess('',['itemList' => $itemList,'total' => $itemCount,'page' => $page]);
    }

}