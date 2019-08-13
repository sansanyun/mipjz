<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.
namespace app\user\controller;
use app\common\lib\Htmlp;
use think\Request;
use app\common\controller\UserBase;
class ApiUserVote extends UserBase
{
    
    public function voteUpdate()
    {
        $type = input('post.type');
        $uuid = input('post.uuid');
        if ($type == 'article') {
            $itemInfo = db('Articles')->where('uuid',$uuid)->find();
            if (!$itemInfo) {
                return jsonError('内容不存在');
            }
        }
        if ($type == 'post') {
            $itemInfo = db('Posts')->where('uuid',$uuid)->find();
            if (!$itemInfo) {
                return jsonError('内容不存在');
            }
        }
        if ($type == 'ask') {
            $itemInfo = db('Asks')->where('uuid',$uuid)->find();
            if (!$itemInfo) {
                return jsonError('内容不存在');
            }
        }
        if ($type == 'answer') {
            $itemInfo = db('AsksAnswers')->where('id',$uuid)->find();
            $itemInfo['uuid'] = $itemInfo['id'];
            if (!$itemInfo) {
                return jsonError('内容不存在');
            }
        }
        if (!$itemInfo) {
            return jsonError('内容不存在');
        }
        if (db('UsersVote')->where('uid',$this->userId)->where('item_id',$itemInfo['uuid'])->find()) {
            db('UsersVote')->where('uid',$this->userId)->where('item_id',$itemInfo['uuid'])->delete();
        } else {
            db('UsersVote')->insert(array(
                'uuid' => uuid(),
                'uid' => $this->userId,
                'type' => $type,
                'item_id' => $itemInfo['uuid'],
                'add_time' => time(),
            ));
        }
        if ($type == 'answer') {
            $count = db('UsersVote')->where('item_id',$uuid)->count();
            db('AsksAnswers')->where('id',$uuid)->update(array(
                'vote_count' => $count,
            ));
        }
        return jsonSuccess('操作成功');
    }
    public function voteCount()
    {
        $uuid = input('post.uuid');
        $count = db('UsersVote')->where('item_id',$uuid)->count();
        $userCount = db('UsersVote')->where('item_id',$uuid)->where('uid',$this->userId)->count();
        $count = $count ? $count : 0;
        $userCount = $userCount ? $userCount : 0;
        return jsonSuccess('操作成功',array('voteCount' => $count, "voteUserCount" => $userCount));
    }
    
    public function favoriteUpdate()
    {
        $type = input('post.type');
        $uuid = input('post.uuid');
        if ($type == 'article') {
            $itemInfo = db('Articles')->where('uuid',$uuid)->find();
            if (!$itemInfo) {
                return jsonError('内容不存在');
            }
        }
        if ($type == 'post') {
            $itemInfo = db('Posts')->where('uuid',$uuid)->find();
            if (!$itemInfo) {
                return jsonError('内容不存在');
            }
        }
        if ($type == 'ask') {
            $itemInfo = db('Asks')->where('uuid',$uuid)->find();
            if (!$itemInfo) {
                return jsonError('内容不存在');
            }
        }
        if (!$itemInfo) {
            return jsonError('内容不存在');
        }
        if (db('UsersFavorite')->where('uid',$this->userId)->where('item_id',$itemInfo['uuid'])->find()) {
            db('UsersFavorite')->where('uid',$this->userId)->where('item_id',$itemInfo['uuid'])->delete();
        } else {
            db('UsersFavorite')->insert(array(
                'uuid' => uuid(),
                'uid' => $this->userId,
                'type' => $type,
                'item_id' => $itemInfo['uuid'],
                'add_time' => time(),
            ));
        }
        return jsonSuccess('操作成功');
    }
    public function favoriteCount()
    {
        $uuid = input('post.uuid');
        $count = db('UsersFavorite')->where('item_id',$uuid)->count();
        $userCount = db('UsersFavorite')->where('item_id',$uuid)->where('uid',$this->userId)->count();
        $count = $count ? $count : 0;
        $userCount = $userCount ? $userCount : 0;
        return jsonSuccess('操作成功',array('favoriteCount' => $count, "favoriteUserCount" => $userCount));
    }
    
}