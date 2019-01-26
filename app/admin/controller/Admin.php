<?php
namespace app\admin\controller;

use think\Response;

use app\common\controller\Base;

class Admin extends Base
{
    protected $beforeActionList = ['start'];

    public function start()
    {
        if (!$this->userId) {
            $this->redirect($this->domain . '/'. config('admin') . '/login',301);
            exit;
        }
        if ($this->userInfo['group_id'] != 1) {
            $this->redirect($this->domain . '/'. config('admin') . '/login',301);
            exit;
        }
        $this->assign('siteInfoToJson',json_encode(config('siteInfo')));
        
        $this->assign('domain',config('domains'));
        
        $addons = db('Addons')->select();
        $this->addonsList = $addons;
        foreach ($addons as $k => $v) {
            $addInfo[$v['name']] = $v['status'];
        }
        $this->assign('addInfo',$addInfo);
    }
    public function index()
    {
        $this->assign('indexTitle','首页');
        
        $tempIndexData = [];
        $pages = [];
        if ($this->addonsList) {
            foreach ($this->addonsList as $key => $value) {
                if ($value['status']) {
                    $pageTemplatePath = ROOT_PATH . 'addons' . DS . $value['name'] . DS . 'view' . DS . 'admin' . DS;
                    if (is_dir($pageTemplatePath)) {
                        $dirList = fetch_file_lists($pageTemplatePath);
                        foreach ($dirList as $file) {
                            if (strpos($file, '.routes') !== false && (strpos($file, DS . 'src') === false)) {
                                $tempArray = explode(DS, $file);
                                array_push($pages,['pagePath' => $file,'pageName' => $tempArray[count($tempArray) - 1]]);
                            }
                        }
                    }
                }
            }
        }
        if ($pages) {
            foreach ($pages as $key => $val) {
                $fileName = str_replace('.routes', '', $val['pageName']);
                $tempIndexData[] = file_get_contents($val['pagePath']);
            }
        }
        $this->assign('siteRoutes',$tempIndexData);
        
        
        $tempAdminMenu = [];
        $pageTemplatePath = ROOT_PATH . 'app' . DS;
        
        $appList = fetch_file_lists($pageTemplatePath);
        foreach ($appList as $key => $file) {
            if (strstr($file,'adminMenu.php')) {
                $tempAdminMenu[] = require $file;
            }
        }
        if ($tempAdminMenu) {
            sort($tempAdminMenu);
            foreach ($tempAdminMenu as $key => $value) {
                if (isset($tempAdminMenu[$key]['path'])) {
                    $tempAdminMenu[$key]['html'] = $this->display(file_get_contents(ROOT_PATH . $tempAdminMenu[$key]['path']));
                }
                if (is_file(ROOT_PATH . $tempAdminMenu[$key]['routes'])) {
                    $tempAdminMenu[$key]['route'] = file_get_contents(ROOT_PATH . $tempAdminMenu[$key]['routes']);
                }
            }
        }
        $this->assign('AdminMenu',$tempAdminMenu);
        
        return $this->fetch('admin@./main');
    }
    
    
    public function component()
    {
        $tempComponent = '';
        $pages = [];
        $pageTemplatePath = ROOT_PATH . 'app' . DS;
        $vueList = fetch_file_lists($pageTemplatePath);
        foreach ($vueList as $key => $file) {
            if (strpos($file, '.vue') !== false) {
                $tempArray = explode(DS, $file);
                array_push($pages,['pagePath' => $file,'pageName' => $tempArray[count($tempArray) - 1]]);
            }
        }       
        if ($pages) {
            foreach ($pages as $key => $val) {
                $fileName = str_replace('.vue', '', $val['pageName']);
                $tempIndexData = $this->display(file_get_contents($val['pagePath']));
                preg_match_all('/<script>([\w\W]*)<\/script>/iU', $tempIndexData, $scriptResult);
                if ($scriptResult) {
                    preg_match_all('/<template>(.*?)<\/template>/is', $tempIndexData, $templateResult);
                    $tempComponent .= "const ".ucfirst($fileName)." = {";
                    $tempComponent .= "template: `".$templateResult[1][0]."`,";
                    $scriptResult[1][0] = str_replace('export default {', '', $scriptResult[1][0]);
                    $tempComponent .= $scriptResult[1][0];
                    $tempComponent .= "Vue.component('".ucfirst($fileName)."',".ucfirst($fileName).");";
                    $tempIndexData = '';
                    $scriptResult = '';
                    $templateResult = '';
                } 
            }
        }
        return Response::create($tempComponent)->contentType('application/x-javascript');
    }

    public function componentCss()
    {
        $tempComponent = '';
        $pages = [];
        $pageTemplatePath = ROOT_PATH . 'app' . DS;
        $cssList = fetch_file_lists($pageTemplatePath);
        foreach ($cssList as $key => $file) {
            if (strpos($file, '.vue') !== false) {
                $tempArray = explode(DS, $file);
                array_push($pages,['pagePath' => $file,'pageName' => $tempArray[count($tempArray) - 1]]);
            }
        }       
        if ($pages) {
            foreach ($pages as $key => $val) {
                $fileName = str_replace('.vue', '', $val['pageName']);
                $tempIndexData = $this->display(file_get_contents($val['pagePath']));
                preg_match_all('/<script>([\w\W]*)<\/script>/iU', $tempIndexData, $scriptResult);
                if ($scriptResult) {
                    preg_match_all('/<style>([\w\W]*)<\/style>/iU', $tempIndexData, $templateResult);
                    $tempComponent .=  $templateResult[1][0];
                } 
            }
        }
        return Response::create($tempComponent)->contentType('text/css');
    }

    public function addonsJs()
    {
        $tempComponent = '';
        $pages = [];
        if ($this->addonsList) {
            foreach ($this->addonsList as $key => $value) {
                if ($value['status']) {
                    $pageTemplatePath = ROOT_PATH . 'addons' . DS . $value['name'] . DS . 'view' . DS . 'admin' . DS;
                    if (is_dir($pageTemplatePath)) {
                        $addonsList = fetch_file_lists($pageTemplatePath);
                        foreach ($addonsList as $file) {
                            if (strpos($file, '.vue') !== false && (strpos($file, DS . 'src') === false)) {
                                $tempArray = explode(DS, $file);
                                array_push($pages,['pagePath' => $file,'pageName' => $tempArray[count($tempArray) - 1]]);
                            }
                        }
                    }
                }
            }
        }
        if ($pages) {
            foreach ($pages as $key => $val) {
                $fileName = str_replace('.vue', '', $val['pageName']);
                $tempIndexData = $this->display(file_get_contents($val['pagePath']));
                preg_match_all('/<script>([\w\W]*)<\/script>/iU', $tempIndexData, $scriptResult);
                if ($scriptResult) {
                    preg_match_all('/<template>(.*?)<\/template>/is', $tempIndexData, $templateResult);
                    $tempComponent .= "const ".ucfirst($fileName)." = {";
                    $tempComponent .= "template: `".$templateResult[1][0]."`,";
                    $scriptResult[1][0] = str_replace('export default {', '', $scriptResult[1][0]);
                    $tempComponent .= $scriptResult[1][0];
                    $tempComponent .= "Vue.component('".ucfirst($fileName)."',".ucfirst($fileName).");";
                    $tempIndexData = '';
                    $scriptResult = '';
                    $templateResult = '';
                } 
            }
        }
        return Response::create($tempComponent)->contentType('application/x-javascript');
    }
    public function addonsCss()
    {
        $tempComponent = '';
        $pages = [];
        if ($this->addonsList) {
            foreach ($this->addonsList as $key => $value) {
                if ($value['status']) {
                    $pageTemplatePath = ROOT_PATH . 'addons' . DS . $value['name'] . DS . 'view' . DS . 'admin' . DS;
                    if (is_dir($pageTemplatePath)) {
                        $addonsList = fetch_file_lists($pageTemplatePath);
                        foreach ($addonsList as $file) {
                            if (strpos($file, '.vue') !== false && (strpos($file, DS . 'src') === false)) {
                                $tempArray = explode(DS, $file);
                                array_push($pages,['pagePath' => $file,'pageName' => $tempArray[count($tempArray) - 1]]);
                            }
                        }
                    }
                }
            }
        }
        if ($pages) {
            foreach ($pages as $key => $val) {
                $fileName = str_replace('.vue', '', $val['pageName']);
                $tempIndexData = $this->display(file_get_contents($val['pagePath']));
                preg_match_all('/<script>([\w\W]*)<\/script>/iU', $tempIndexData, $scriptResult);
                if ($scriptResult) {
                    preg_match_all('/<style>([\w\W]*)<\/style>/iU', $tempIndexData, $templateResult);
                    $tempComponent .=  $templateResult[1][0];
                } 
            }
        }
        return Response::create($tempComponent)->contentType('text/css');
    }
}
