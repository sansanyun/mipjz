<?php
namespace app\common\controller;

use think\Session;
use think\Request;
use think\Controller;

class Init extends Controller
{
    public function _initialize()
    {
        $this->siteInit();
        $this->userInit();
        $this->addonsInit();
    }
    
    public function siteInit()
    {
        $this->assign('mod',$this->request->module());
        $this->assign('ctr',$this->request->controller());
        $this->assign('act',$this->request->action());
        if (SITE_HOST) {
            $this->assign('assets','public/assets');
            config('assets','public/assets');
        } else {
            $this->assign('assets','assets');
            config('assets','assets');
        }
        $this->assign('tplPath',config('tplPath'));
        
        $siteInfo = config('siteInfo');
        $this->siteInfo = $siteInfo;
        $this->assign('siteInfo',$siteInfo);
        
        $mipInfo = config('siteInfo');
        $this->mipInfo = $mipInfo;
        $this->assign('mipInfo',$mipInfo);
        
        $domain = config('domain');
        $this->domain = $domain;
        $this->assign('domain',$domain);
        
        $domains = config('domains');
        $this->domains = $domains;
        $this->assign('domains',$domains);
        
        $this->domainStatic = config('domainStatic');
        $this->assign('domainStatic',$this->domainStatic);
        
        if ($this->siteInfo['articleDomain']) {
            $this->siteUrl = $this->siteInfo['httpType'] . $this->siteInfo['domain'] . $this->request->url();
        } else {
            $this->siteUrl = $this->domainStatic . $this->request->url();
        }
        $this->assign('siteUrl',$this->siteUrl);
        $this->currentUrl = $this->siteUrl;
        $this->assign('currentUrl',$this->currentUrl);
        
        $tplName = config('tplName');
        $this->tplName = $tplName;
        $this->assign('tplName',$tplName);
        
        $returnUrl = @htmlspecialchars($_SERVER['HTTP_REFERER']);
        $this->returnUrl = $returnUrl;
        $this->assign('returnUrl', $returnUrl);
        
        $this->assign('itemDetailId','');
        $this->assign('categoryUrlName','');
        
        $this->dataId = config('dataId');
        $this->assign('dataId',$this->dataId);
        $this->siteId = config('siteId');
        $this->assign('siteId',$this->siteId);
        
        $this->assign('isBaidu',isBaidu(Request::instance()->ip()));
        
        $this->keyInfo = [];
        $addons = db('Key')->select();
        foreach ($addons as $k => $v) {
            $this->keyInfo[$v['key']] = $v['val'];
        }
        config('keyInfo',$this->keyInfo);
        $this->assign('keyInfo',$this->keyInfo);
        
        $this->assign('config',config());
    }
    
    public function userInit()
    {
        $userInfo = Session::get('userInfo');
        if ($userInfo) {
            $this->userInfo = db('Users')->where('uid' ,$userInfo['uid'])->find();
            $this->userInfo['groupInfo'] = db('UsersGroup')->where('group_id',$this->userInfo['group_id'])->find();
            $this->userId = $this->userInfo['uid'];
            $this->isAdmin = $this->userInfo['group_id'] == 1 ? true : false;
            if ($this->userInfo['status'] == 1) {
                Session::delete('userInfo');
                $this->error('抱歉, 你的账号已经被禁止登录','/');
            }
        } else {
            $this->userInfo = null;
            $this->userId = '';
            $this->isAdmin = '';
        }
        
        config('userId',$this->userId);
        config('userInfo',$this->userInfo);
        config('isAdmin',$this->isAdmin);
        
        $this->assign('userId',$this->userId);
        $this->assign('userInfo',$this->userInfo);
        $this->assign('isAdmin',$this->isAdmin);
        
        if (!$this->siteInfo['systemStatus']) {
            if ($this->request->module() != 'admin') {
                if ($this->userInfo['group_id'] != 1) {
                    if (!$this->request->isPost()) {
                        $this->error('站点关闭中...','');
                    }
                }
            }
        }
    }
    
    public function addonsInit()
    {
        $this->addonsInfo = [];
        $addons = db('Addons')->select();
        foreach ($addons as $k => $v) {
            $this->addonsInfo[$v['name']] = $v['status'];
        }
        config('addonsInfo',$this->addonsInfo);
        $this->assign('addonsInfo',$this->addonsInfo);
        
        $this->globalActionList = db('GlobalAction')->select();
        config('globalActionList',$this->globalActionList);
        $this->assign('globalActionList',$this->globalActionList);
        if ($this->globalActionList) {
            try {
                foreach ($this->globalActionList as $key => $val) {
                    $addonsName = $val['name'];
                    if (strpos($addonsName,"mipinit") !== false) {
                        $addonsNameSpace = "addons" . "\\" . $addonsName . "\\" . "controller" . "\\" . "GlobalAction";
                        model($addonsNameSpace)->$addonsName();
                    }
                }
            } catch (\Exception $e) {}

        }
    }

	protected function addonsFetch($template = '', $addonsName = '')
    {
        $this->assign('webUrl',config('domain'));
        $this->assign('domain',config('domain'));
        $this->assign('domains',config('domains'));
        $this->assign('domainStatic',config('domainStatic'));
        $this->assign('mipInfo',config('siteInfo'));
        $this->assign('siteInfo',config('siteInfo'));
        $tplName = config('view_name');
        $this->assign('tplName',$tplName);
        $this->assign('themeStatic', config('domainStatic') . '/' . config('assets') . '/' . $tplName);
        $this->assign('config',config());
        if (!$addonsName) {
            $this->error('模板渲染，缺少参数','');
        }
        if ($template) {
    		$viewPath = config('template.view_path');
			if (strpos($viewPath, DS . 'html') !== false) {
            	$template = '../../../addons' . DS . $addonsName  . DS . 'view' . DS . $template;
			} else {
            	$template = '../../addons' . DS . $addonsName  . DS . 'view' . DS . $template;
			}
        } else {
            return false;
        }
        return $this->fetch($template);
    }

}
