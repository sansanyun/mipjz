<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://www.MIPJZ.com All rights reserved.
namespace app\common;
use think\template\TagLib;
class Ssycms extends TagLib {
    
    protected $tags   =  [
        'post'      => ['close' => 1],
    ];
    
    public function tagPost($tag, $content)
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
        $html .= '$__think__list__ = "";';
        $html .= '$__think__list__ = model("app.common.model.Posts")->getItemListByTag($__think__tag__,"list");';
        if ($page) {
            $html .= '$pagination = "";';
        } else {
            $html .= '$pagination = "";';
        }
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }

    
}
