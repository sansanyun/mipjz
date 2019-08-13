<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\widget;
use think\template\TagLib;
class Mipjz extends TagLib {
    
    protected $tags   =  [
        'widgetnav'      => ['close' => 1], 
    ];
    
    public function tagWidgetnav($tag, $content)
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
        $html .= '$__think__list__ = model("app.widget.model.Widget")->getItemListByTag($__think__tag__);';
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
}
