<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\addons\controller;
use think\Request;
use think\Db;
use app\common\lib\File;
use app\common\controller\AdminBase;
class ApiAdminAddons extends AdminBase
{		    public function getSideList()    {        $addonsList = db('addonsMenu')->select();        if ($addonsList) {            foreach ($addonsList as $key => $value) {                $addonsInfo = db('Addons')->where('name',$value['name'])->find();                if (!is_dir(ROOT_PATH . 'addons' . DS . $value['name']) || !$addonsInfo || !intval($addonsInfo['side_status']) || !$addonsInfo['admin_url']) {                    unset($addonsList[$key]);                }            }        }        return jsonSuccess('ok',$addonsList);    }    public function getHeaderList()    {        $addonsList = db('headerMenu')->select();        if ($addonsList) {            foreach ($addonsList as $key => $value) {                $addonsInfo = db('Addons')->where('name',$value['name'])->find();                if (!is_dir(ROOT_PATH . 'addons' . DS . $value['name']) || !$addonsInfo || !intval($addonsInfo['header_status']) || !$addonsInfo['admin_url']) {                    unset($addonsList[$key]);                }            }        }        return jsonSuccess('ok',$addonsList);    }
    public function save()
    {
        $itemList = input('post.itemList');
        $itemList = json_decode($itemList,true);
        if ($itemList) {
            foreach ($itemList as $key => $val) {                 $itemList[$key]['side_status'] =  intval($itemList[$key]['side_status']) ? 1 : 0;                 $itemList[$key]['header_status'] =  intval($itemList[$key]['header_status']) ? 1 : 0;
            }
            foreach ($itemList as $key => $val) {
                db('Addons')->where('id',$val['id'])->update(array(
                    'side_status' => $itemList[$key]['side_status'],
                    'header_status' => $itemList[$key]['header_status'],
                ));
            }
        }
        
        $addonsList = db('Addons')->select();
        db('AddonsMenu')->where('type','addons')->delete();
        foreach ($addonsList as $key => $val) {
            if (intval($addonsList[$key]['side_status']) == 1) {
                db('AddonsMenu')->insert(array(
                    'name' => $val['name'],
                    'title' => $val['title'],
                    'item_id' => $val['id'],
                    'status' => 1,
                    'admin_url' => $val['admin_url'],
                    'type' => 'addons',
                    'sort' => 0,
                ));
            }
        }
        db('HeaderMenu')->where('type','addons')->delete();
        foreach ($addonsList as $key => $val) {
            if ($addonsList[$key]['header_status'] == 1) {
                db('HeaderMenu')->insert(array(
                    'name' => $val['name'],
                    'title' => $val['title'],
                    'item_id' => $val['id'],
                    'admin_url' => $val['admin_url'],
                    'status' => 1,
                    'type' => 'addons',
                    'sort' => 0,
                ));
            }
        }
        
        return jsonSuccess('ok');
    }
    
    public function install()
    {
        $name = input('post.name');
        if (!$name) {
            return jsonError('缺少参数');
        }
        $itemInfo = db('Addons')->where('name',$name)->find();
        if ($itemInfo) {
            return jsonError('该插件已安装，无法再次安装');
        }
        $class = get_addon_class($name);
        $addons = new $class();
        if (!$addons->install()) {
            return jsonError('执行插件预安装操作失败');
        }
        $sqlFile = ROOT_PATH . 'addons' . DS . $name . DS . 'install.sql';
        if (is_file($sqlFile)) {
            try {                if (Db::name('Addons')->where('name','mipzhanqun')->find() && config('dataId')) {                	$dbconfig = Db::connect($config, $force)->name('zhanqun')->where('id',config('dataId'))->find();	                $hostname = $dbconfig['hostname'];	                $database = $dbconfig['database'];	                $hostport = $dbconfig['hostport'];	                $username = $dbconfig['username'];	                $password = $dbconfig['password'];	                $dsn = "mysql:dbname={$database};host={$hostname};port={$hostport};charset=utf8";                } else {	                $hostname = config('database.hostname');	                $database = config('database.database');	                $hostport = config('database.hostport');	                $username = config('database.username');	                $password = config('database.password');	                $dsn = "mysql:dbname={$database};host={$hostname};port={$hostport};charset=utf8";                }
                try {
                    $db = new \PDO($dsn, $username, $password);
                } catch (\PDOException $e) {
                    return jsonError('错误代码:'.$e->getMessage());
                }
                $prefix = config('database.prefix');
                $orginal = 'mip_';
                $sql = str_replace(" `{$orginal}"," `{$prefix}", file_get_contents($sqlFile));
                $sql = str_replace("\r", "\n", $sql);
                $sql = explode(";\n", $sql);
                foreach ($sql as $item) {
                    $item = trim($item);
                    if(empty($item)) continue;
                    preg_match('/CREATE TABLE `([^ ]*)`/', $item, $matches);
                    if($matches) {
                        if(false !== $db->exec($item)){

                        } else {
                           return jsonError('安装失败');
                        }
                    } else {
                        $db->exec($item);
                    }
                }
            } catch (\PDOException $e) {
                return jsonError('数据库安装失败，原因：' . $e);
            }
        }
        $info = $addons->info;
        db('Addons')->insert(array(
            'name' => $info['name'],
            'title' => $info['title'],
            'description' => $info['description'],
            'author' => $info['author'],
            'status' => $info['status'],
            'version' => $info['version'],
            'admin_url' => $info['adminUrl'],
            'add_time' => time(),
            'config' => json_encode($addons->getConfig()),
        ));
        if ($info['isGlobalAction']) {
            db('GlobalAction')->where('type','addons')->where('name',$info['name'])->delete();
            db('GlobalAction')->insert(array(
                'name' => $info['name'],
                'title' => $info['title'],
                'status' => 1,
                'type' => 'addons',
                'sort' => 0,
            ));
        }
        return jsonSuccess('ok');
    }

    public function uninstall()
    {
        $id = input ('id');
        if (!$id) {
            return jsonError('缺少参数');
        }
        $itemInfo = db('Addons')->where('id',$id)->find();
        if (!$itemInfo) {
            return jsonError('卸载项不存在');
        }
        $name = $itemInfo['name'];
        try {
            $class = get_addon_class($itemInfo['name']);
            $addons = new $class();
            if (!$addons->uninstall()) {
                return jsonError('执行插件预卸载操作失败');
            }
        } catch (\Exception $e) {
            //解决文件被删除 卸载问题
        }
        db('Addons')->where('id',$id)->delete();
        $sqlFile = ROOT_PATH . 'addons' . DS . $itemInfo['name'] . DS . 'uninstall.sql';
        if (is_file($sqlFile)) {
            try {                if (Db::name('Addons')->where('name','mipzhanqun')->find() && config('dataId')) {                	$dbconfig = Db::connect($config, $force)->name('zhanqun')->where('id',config('dataId'))->find();	                $hostname = $dbconfig['hostname'];	                $database = $dbconfig['database'];	                $hostport = $dbconfig['hostport'];	                $username = $dbconfig['username'];	                $password = $dbconfig['password'];	                $dsn = "mysql:dbname={$database};host={$hostname};port={$hostport};charset=utf8";                } else {	                $hostname = config('database.hostname');	                $database = config('database.database');	                $hostport = config('database.hostport');	                $username = config('database.username');	                $password = config('database.password');	                $dsn = "mysql:dbname={$database};host={$hostname};port={$hostport};charset=utf8";                }
                $hostname = config('database.hostname');
                $database = config('database.database');
                $hostport = config('database.hostport');
                $username = config('database.username');
                $password = config('database.password');
                $dsn = "mysql:dbname={$database};host={$hostname};port={$hostport};charset=utf8";
                try {
                    $db = new \PDO($dsn, $username, $password);
                } catch (\PDOException $e) {
                    return jsonError('错误代码:'.$e->getMessage());
                }
                $prefix = config('database.prefix');                $orginal = 'mip_';                $sql = str_replace(" `{$orginal}"," `{$prefix}", file_get_contents($sqlFile));
                $sql = str_replace("\r", "\n", $sql);
                $sql = explode(";\n", $sql);
                foreach ($sql as $item) {
                    $item = trim($item);
                    if(empty($item)) continue;
                    preg_match('/CREATE TABLE `([^ ]*)`/', $item, $matches);
                    if($matches) {
                        if(false !== $db->exec($item)){

                        } else {
                           return jsonError('卸载失败');
                        }
                    } else {
                        $db->exec($item);
                    }
                }
            } catch (\PDOException $e) {
                return jsonError('数据库卸载失败，原因：' . $e);
            }
        }
        db('AddonsMenu')->where('type','addons')->where('item_id',$id)->delete();
        db('HeaderMenu')->where('type','addons')->where('item_id',$id)->delete();
        db('GlobalAction')->where('type','addons')->where('name',$itemInfo['name'])->delete();
        return jsonSuccess('ok');
    }
    
    public function enable()
    {
        $id = input ('id');
        if (!$id) {
            return jsonError('缺少参数');
        }
        db('Addons')->where('id',$id)->update(array('status' => 1));
        return jsonSuccess('ok');
    }
    
    public function disable()
    {
        $id = input ('id');
        if (!$id) {
            return jsonError('缺少参数');
        }
        db('Addons')->where('id',$id)->update(array('status' => 0));
        return jsonSuccess('ok');
    }
    
    public function itemList()
    {
        $addonsPath = ROOT_PATH . 'addons' . DS;
        $templateDir = opendir($addonsPath);
        if ($templateDir) {
            while (false !== ($file = readdir($templateDir))) {
                if (substr($file, 0, 1) != '.' AND is_dir($addonsPath . $file)) {
                    $dirs[] = $file;
                }
            }
            closedir($templateDir);
        }
        $addons = array();
        $list = db('Addons')->where('name','in',$dirs)->select();
        if ($list) {
            foreach ($list as $key => $val ) {                $list[$key]['header_status'] = intval($list[$key]['header_status']);                $list[$key]['side_status'] = intval($list[$key]['side_status']);                $list[$key]['status'] = intval($list[$key]['status']);                
                $list[$key]['uninstall'] = 0;
                $addons[$val['name']] = $list[$key];
            }
        }
        $tempAddons = array();
        if ($dirs) {
            foreach ($dirs as $val) {
                if (!isset($addons[$val])) {
                    try {
                        $class = get_addon_class($val);
                        if (!class_exists($class)) {
                            trace($class);
                            continue;
                        }
                        $obj = new $class();
                        $addons[$val] = $obj->info;
                        if ($addons[$val]) {
                            $addons[$val]['uninstall'] = 1;
                            unset($addons[$val]['status']);
                        }
                    } catch (\Exception $e) {
                        
                    }
                }
                $tempAddons[] = $addons[$val];
            }
        }
        return jsonSuccess('',array('itemList' => $tempAddons));
    }
 
}