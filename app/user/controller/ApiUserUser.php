<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.
namespace app\user\controller;
use think\Request;
use think\Session;
use think\Validate;
use think\Cache;
use app\common\lib\Htmlp;
use app\common\controller\UserBase;

class ApiUserUser extends UserBase
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
    
    public function getUserInfo()
    {
        $itemInfo = model($this->itemModelNameSpace)->getItemInfo($this->userId);
        return jsonSuccess('修改成功',$itemInfo);
    }
    public function userEdit(Request $request)
    {
        $nickname = Htmlp::htmlp(input('post.nickname'));
        $email = Htmlp::htmlp(input('post.email'));
        $mobile = Htmlp::htmlp(input('post.mobile'));
        $qq = Htmlp::htmlp(input('post.qq'));
        $sex = Htmlp::htmlp(input('post.sex'));
        $signature = Htmlp::htmlp(input('post.signature'));
        if ($uid = $this->userId) {
            $data['uid'] = $this->userId;
        }
        if (!$uid) {
            return jsonError('缺少用户UID');
        }
        if(!$userInfo = db('Users')->where('uid',$uid)->find()){
            return jsonError('用户不存在');
        }
        if (!$sex) {
            $sex = 1;
        }
        $data['qq'] = $qq;
        $data['sex'] = $sex;
        $data['signature'] = $signature;
        $data['nickname'] = $nickname;
        if ($mobile) {
            if (!preg_match("/^1[34578]\d{9}$/", $mobile)) {
                return jsonError('手机号码输入有误');
            }
            if (db('Users')->where('mobile',$mobile)->find()) {
                return jsonError('手机已存在');
            }
        }
        if ($email) {
            if ($email != $userInfo['email'] AND db('Users')->where('email',$email)->find()) {
                return jsonError('邮箱已存在');
            }
        }
        $data['email'] = $email;
        if ($data) {
            $usersInfo =  db('Users')->where('uid',$uid)->update($data);
        }
        return jsonSuccess('修改成功');
    }
    
    public function userAvatarEdit()
    {
        $avatar = Htmlp::htmlp(input('post.avatar'));
        if(!$avatar){
            return jsonError('缺少参数');
        }
        $usersInfo = db('Users')->where('uid',$this->userId)->update(array(
            'avatar' => $avatar
        ));
        return jsonSuccess('操作成功');
    }
    
    public function userPasswordEdit()
    {
        $uid = $this->userId;
        $oldPassword = Htmlp::htmlp(input('post.oldPassword'));
        $newPassword = Htmlp::htmlp(input('post.newPassword'));
        $rpassword = Htmlp::htmlp(input('post.rpassword'));
        if(!$oldPassword){
            return jsonError('请输入原来密码');
        }
        if(!$newPassword){
             return jsonError('请输入新密码');
        }
        if (strlen($oldPassword) != 32){
             return jsonError('密码长度不符合规则');
        }
        if (strlen($newPassword) != 32){
             return jsonError('密码长度不符合规则');
        }
        if (strlen($rpassword) != 32){
             return jsonError('密码长度不符合规则');
        }
        if ($oldPassword == $newPassword) {
            return jsonError('新密码不能与旧密码重复');
        }
        if($rpassword != $newPassword){
            return jsonError('两次输入的密码不一样');
        }
        $userPasswordInfo = db('Users')->where('uid',$this->userId)->find();
        $oldpassword = create_md5($oldPassword,$userPasswordInfo['salt']);
        if($oldpassword != $userPasswordInfo['password']) {
             return jsonError('旧密码错误，请重新输入');
        }
        $data['salt'] = create_salt(8);
        $data['password'] = create_md5($newPassword,$data['salt']);
        $usersInfo = db('Users')->where('uid',$uid)->update($data);
        @session::delete('userInfo');
        @Cache::set('accessToken_' . $this->terminal . $this->accessToken, NULL);
        @Cache::set('accessTokenAndClient_' . $this->terminal .  $this->accessTokenId, NULL);
        return  jsonSuccess('修改成功','','/login/');
    }

    public function loginOut()
    {
        @session::delete('userInfo');
        @Cache::set('accessToken_' . $this->terminal . $this->accessToken, NULL);
        @Cache::set('accessTokenAndClient_' . $this->terminal .  $this->accessTokenId, NULL);
        return jsonSuccess('退出成功');
    }
    
}