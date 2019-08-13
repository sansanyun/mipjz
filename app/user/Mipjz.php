<?php
//MIPJZ.Com [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://www.ssycms.com All rights reserved.
namespace app\user;
use think\template\TagLib;
class Mipjz extends TagLib {
    
    protected $tags   =  [
        'userGroup'      => ['close' => 1], 
        'user'      => ['close' => 1],
        'userinfo'      => ['close' => 0],
     	'usercomments'      => ['close' => 1],
        'usercrumb' => ['close' => 0],
        'userpage' => ['close' => 1],
    ];
    
    public function tagUserGroup($tag, $content)
    {
        if (!isset($tag['value'])) {
            $tag['value'] = 'val';
        }
        if (!isset($tag['key'])) {
            $tag['key'] = 'index';
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
        
        $html  = "<?php ";
        $html .= '$__think__tag__ = \'' . json_encode($tag) . '\';';
        $html .= '$__think__list__ = "";';
        $html .= '$__think__list__ = model("app.user.model.UsersCategory")->getCategoryByTag($__think__tag__);';
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
 
    public function tagUser($tag, $content)
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
        $html .= '$__think__list__ = model("app.user.model.Users")->getItemListByTag($__think__tag__,"list");';
        if ($page) {
            $html .= '$pagination = model("app.user.model.Users")->getItemListByTag($__think__tag__,"pagination");';
        } else {
            $html .= '$pagination = "";';
        }
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }


    public function tagUserinfo($tag, $content)
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
        $html .= '$__think__list__ = model("app.user.model.Users")->getItemListByTag($__think__tag__,"list");';
        $html .= '$'. $tag['value'] .' = $__think__list__; ?>';

        return $html;
    }
    
    public function tagUsercrumb($tag, $content)
    {
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
        $html .= '$__think__info__ = "";';
        $html .= '$__think__info__ = model("app.user.model.Users")->getCrumb($__think__tag__);';
        $html .= 'echo $__think__info__; ?>';
        return $html;
    }
    
    public function tagUserpage($tag, $content)
    {
        if (!isset($tag['value'])) {
            $tag['value'] = 'v';
        }
        if (!isset($tag['key'])) {
            $tag['key'] = 'index';
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
        $html .= '$__think__list__ = model("app.user.model.Users")->getPage($__think__tag__);';
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
    
    
}
