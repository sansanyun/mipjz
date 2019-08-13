<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\tag;
use think\template\TagLib;
class Mipjz extends TagLib {
    
    protected $tags   =  [
        'tagsategory'      => ['close' => 1], 
        'tag'      => ['close' => 1],
     	'tagcomments'      => ['close' => 1],
        'tagcrumb' => ['close' => 0],
        'tagpage' => ['close' => 1],
    ];
    
    public function tagTagscategory($tag, $content)
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
        $html .= '$__think__list__ = model("app.tag.model.TagsCategory")->getCategoryByTag($__think__tag__);';
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
 
    public function tagTag($tag, $content)
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
        $html .= '$__think__list__ = model("app.tag.model.Tags")->getItemListByTag($__think__tag__,"list");';
        if ($page) {
            $html .= '$paginationTag = model("app.tag.model.Tags")->getItemListByTag($__think__tag__,"pagination");';
        } else {
            $html .= '$paginationTag = "";';
        }
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
    
    public function tagTagcrumb($tag, $content)
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
        $html .= '$__think__info__ = model("app.tag.model.Tags")->getCrumb($__think__tag__);';
        $html .= 'echo $__think__info__; ?>';
        return $html;
    }
    
    public function tagTagpage($tag, $content)
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
        $html .= '$__think__list__ = model("app.tag.model.Tags")->getPage($__think__tag__);';
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
    
    
}
