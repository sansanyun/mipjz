<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace addons\ad;
use think\template\TagLib;
class Mipjz extends TagLib {
    
    protected $tags   =  [
        'ad'      => ['close' => 0],
    ];
    
    public function tagAd($tag, $content)
    {
        if (!isset($tag['value'])) {
            $tag['value'] = 'v';
        }
        if (!isset($tag['key'])) {
            $tag['key'] = 'index';
        }
        if (isset($tag['page'])) {
            $page = $tag['page'];
        } else {
            $page = '';
        }
        foreach ($tag as $key => $val) {
            if (strpos($val, '$') !== false) {
                if (substr($val, 0, 1) == '$') {
                    if (strpos($val, '"') !== false) {
                        $val = str_replace('"', "'", $val);
                    }
                    $tag[$key] = "'.". $val . ".'";
                    
                }
            }
        }
        $html  = '<?php ';
        $html .= '$__think__tag__ = \''.json_encode($tag).'\';';
        $html .= '$__think__data = "";';
        $html .= '$__think__data = model("addons.ad.model.Ad")->getItemListByTag($__think__tag__);';
        $html .= 'echo $__think__data; ?>';
        return $html;
    }
    
 
}
