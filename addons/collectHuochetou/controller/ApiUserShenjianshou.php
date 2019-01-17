<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.Com All rights reserved.
namespace addons\collectHuochetou\controller;
use think\Request;
use think\Loader;
use think\Db;use think\Controller;
class ApiUserShenjianshou extends Controller {
    public function index() {

    }
    public function articleAddTime(Request $request)
    {
        if (Request::instance()->isPost()) {
            
            $password  = db('key')->where('key','collect_huochetou')->find()['val'];
            if (empty($_POST['__sign']) || md5("{$password}shenjianshou.cn") != trim($_POST['__sign'])) {
                echo json_encode([
                        "result" => 2,
                        "reason" => "发布失败, 错误原因: 发布密码验证失败!"
                     ]);
                exit;
            }

            $title = input('post.article_title');
            $url_name = input('post.url_name');
            $content = input('post.article_content');
            $cid = input('cid');
            $cid ? $cid = $cid : $cid = 0;
            $uid = input('post.uid');
            $tags = input('post.tags');
            $publish_time = input('post.publish_time') ? input('post.publish_time') : time();;
            $itemType = 'article';
            $is_recommend = input('post.is_recommend');
            if (!$is_recommend) {
                $is_recommend = 0;
            } 
            if (!$title) {
                echo json_encode(["result" => 2,"reason" => "请输入标题"]);
                exit;
            }
            if (!$content) {
                echo json_encode(["result" => 2,"reason" => "请输入内容"]);
                exit;
            }
            if (!$cid) {
                $cid = 0;
            }
            if (!$uid) {
                $uid = 1;
            }
           
            $articleInfo = db($this->articles)->where('title',$title)->find();
            $articleInfoApproval = db('ArticlesTimeMain')->where('title',$title)->find();
            if ($articleInfo || $articleInfoApproval) {
                echo json_encode(["result" => 2,"reason" => "文章已存在"]);
                exit;
            }			$siteId = 0;			$addonsInfo = db('Addons')->where('name','superSite')->find();			if ($addonsInfo) {				$siteList = db('domainSites')->select();				$tempSites = [];				if ($siteList) {					foreach ($siteList as $key => $value) {						$tempSites[] = $value['id'];					}					if ($tempSites) {						$siteId = $tempSites[array_rand($tempSites,1)];					}				}			}
            $main_id = uuid();
             db('ArticlesTime')->insert(array(
                'uuid' => $main_id,				'site_id' => $siteId,
                'cid' => $cid,
                'create_time' => time(),
                'publish_time' => $publish_time,
                'main_id' => $main_id,
            ));
            $createInfo = db('ArticlesTimeMain')->insert(array(
               'title'=>htmlspecialchars($title),
               'uid' => $this->userId,
               'main_id' => $main_id,
               'is_recommend' => $is_recommend,
               'content' => htmlspecialchars($content),
               'tags' => $tags,
               'url_name' => $url_name
            ));
         
 
            echo json_encode(["result" => 1, "data" => "发布成功"]);
             
            exit;

        }
    }
    public function articleAdd(Request $request) {
   
            $password  = db('key')->where('key','collect_huochetou')->find()['val'];
            if (empty($_POST['__sign']) || md5("{$password}shenjianshou.cn") != trim($_POST['__sign'])) {
                echo json_encode([
                        "result" => 2,
                        "reason" => "发布失败, 错误原因: 发布密码验证失败!"
                     ]);
                exit;
            }
            $title = input('post.article_title');
            $url_name = input('post.url_name');
            $content = input('post.article_content');
            $cid = input('cid');
            $cid ? $cid = $cid : $cid = 0;
            $uid = input('post.uid');
            $tags = input('post.tags');
            $publish_time = input('post.publish_time') ? input('post.publish_time') : time();;
            $itemType = 'article';
            $is_recommend = input('post.is_recommend');
            if (!$is_recommend) {
                $is_recommend = 0;
            }
            if ($tags) {
                $tags = explode(',',$tags);
            }
            if (!$title) {
                echo json_encode(["result" => 2,"reason" => "请输入标题"]);
            }
            if (!$content) {
                echo json_encode(["result" => 2,"reason" => "请输入内容"]);
            }
            if (!$cid) {
                $cid = 0;
            }
            if (!$uid) {
                $uid = 1;
            }
            if ($this->mipInfo['mipPostStatus']) {
                if (!$this->mipInfo['mipApiAddress']) {
                    echo json_encode(["result" => 2,"reason" => "请先去填写百度MIP的接口"]);
                    exit;
                }
            }
            if ($this->mipInfo['baiduYuanChuangStatus']) {
                if (!$this->mipInfo['baiduYuanChuangUrl']) {
                    echo json_encode(["result" => 2,"reason" => "请先去填写百度原创提交的接口"]);
                    exit;
                }
            }
            if ($this->mipInfo['guanfanghaoStatus']) {
                if ($this->mipInfo['guanfanghaoStatusPost']) {
                    if (!$this->mipInfo['guanfanghaoRealtimeUrl']) {
                        echo json_encode(["result" => 2,"reason" => "请先去填写百度官方号推送接口"]);
                        exit;
                    }
                }
            }
            if ($this->mipInfo['baiduTimePcStatus']) {
                if (!$this->mipInfo['baiduTimePcUrl']) {
                    echo json_encode(["result" => 2,"reason" => "请先去填写百度PC链接提交的接口"]);
                    exit;
                }
            }
            if ($this->mipInfo['baiduTimeMStatus']) {
                if (!$this->mipInfo['baiduTimeMUrl']) {
                    echo json_encode(["result" => 2,"reason" => "请先去填写百度M链接提交的接口"]);
                    exit;   
                }
            }
            if ($this->mipInfo['diyUrlStatus']) {
                if ($url_name) {
                    $articleInfoByUrlName = db($this->articles)->where('url_name',$url_name)->find();
                    if ($articleInfoByUrlName) {
                        echo json_encode(["result" => 2,"reason" => "文章已存在"]);
                        exit;
                    }
                }
            }
            $articleInfo = db($this->articles)->where('title',$title)->find();
            if ($articleInfo) {
                echo json_encode(["result" => 2,"reason" => "文章已存在"]);
                exit;
            } else {
                $uuid = uuid();
                $resArray = array(
                   'title'=>htmlspecialchars($title),
                   'uid' => $this->userId,
                   'cid' => $cid,
                   'create_time' => time(),
                   'publish_time' => $publish_time,
                   'uuid' => $uuid,
                   'is_recommend' => $is_recommend,
                   'content_id' => $uuid,
                   'url_name' => $url_name,
                    );
                 if (is_array($fieldList)) {
                     for ($i=0; $i < count($fieldList); $i++) { 
                        $resArray[$fieldList[$i]['Field']] = $fieldList[$i]['value'];
                     }
                 }
                $createInfo = db($this->articles)->insert($resArray);
                $createInfo = $createInfo = db($this->articles)->where('uuid',$uuid)->find();
                if ($createInfo) {
                    db($this->articlesContent)->insert(array(
                       'id' => $uuid,
                       'content' => htmlspecialchars($content),
                    ));
                }
                if ($createInfo) {
                    if ($tags) {
                        model('app\tag\model\ItemTags')->innerTags($tags, $itemType, $createInfo);
                    }
                    db('Users')->where('uid',$this->userId)->update([
                        'article_num' => db($this->articles)->where('uid',$this->userId)->count(),
                    ]);
                    $domainSitesList = db('domainSites')->select();
                    if ($this->mipInfo['superSites'] && $domainSitesList) {
                        foreach ($domainSitesList as $key => $val) {
                            $domainSettingsInfo = db('domainSettings')->where('id',$val['id'])->find();
                            $urls = $createInfo->getUrlByItemInfo($createInfo,$val['http_type'].$val['domain']);
                            $urls = explode(',',$urls);
                            if ($domainSettingsInfo['mipAutoStatus'] && $domainSettingsInfo['mipApi']) {
                                $result = pushData($domainSettingsInfo['mipApi'],$urls);
                            }
                            
                            if ($domainSettingsInfo['ampAutoStatus'] && $domainSettingsInfo['ampApi']) {
                                $result = pushData($domainSettingsInfo['ampApi'],$urls);
                            }
                            
                            if ($domainSettingsInfo['xiongZhangStatus'] && $domainSettingsInfo['xiongZhangNewAutoStatus'] && $domainSettingsInfo['xiongZhangNewApi']) {
                                $result = pushData($domainSettingsInfo['xiongZhangNewApi'],$urls);
                            }
                            
                            if ($domainSettingsInfo['yuanChuangAutoStatus'] && $domainSettingsInfo['yuanChuangApi']) {
                                $result = pushData($domainSettingsInfo['yuanChuangApi'],$urls);
                            }
                            
                            if ($domainSettingsInfo['linkAutoStatus'] && $domainSettingsInfo['linkApi']) {
                                $result = pushData($domainSettingsInfo['linkApi'],$urls);
                            }
                            
                        }
                    } else {
                        $urls = model('app\article\model\Articles')->domainUrlById($createInfo);
                        $urls = explode(',',$urls);
                        if (is_array($urls)) {
                            if ($this->mipInfo['baiduYuanChuangStatus']) {
                                $api = $this->mipInfo['baiduYuanChuangUrl'];
                                $result = pushData($api,$urls);
                            }
                            if ($this->mipInfo['baiduTimePcStatus']) {
                                $api = $this->mipInfo['baiduTimePcUrl'];
                                $result = pushData($api,$urls);
                            }
                            if ($this->mipInfo['guanfanghaoStatus']) {
                                if ($this->mipInfo['guanfanghaoStatusPost']) {
                                    $api = $this->mipInfo['guanfanghaoRealtimeUrl'];
                                    $result = pushData($api,$urls);
                                }
                            }
                            if ($this->mipInfo['mipPostStatus']) {
                                $api = $this->mipInfo['mipApiAddress'];
                                $result = pushData($api,$urls);
                            }
                        }
                    }
                    echo json_encode(["result" => 1, "data" => "发布成功"]);
                    exit;
                }
            }

        
    }

}
