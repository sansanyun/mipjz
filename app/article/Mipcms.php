<?php
//MIPJZ.COM [Don't forget the beginner's mind]
//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.
namespace app\article;
use think\template\TagLib;
class Mipcms extends TagLib {
    
    protected $tags   =  [
        'articlecategory'      => ['close' => 1], 
        'articlecategoryinfo'      => ['close' => 0], 
        'article'      => ['close' => 1],
        'articleinfo'      => ['close' => 0],
     	'articlecomments'      => ['close' => 1],
        'articlecrumb' => ['close' => 0],
        'crumb' => ['close' => 0], 
        'articlepage' => ['close' => 1],
        'page' => ['close' => 1],
    ];
    
    public function tagArticlecategory($tag, $content)
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
        $html .= '$__think__list__ = model("app.article.model.ArticlesCategory")->getCategoryByTag($__think__tag__);';
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }

    public function tagArticlecategoryinfo($tag, $content)
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
        $html .= '$__think__list__ = model("app.article.model.ArticlesCategory")->getCategoryByTag($__think__tag__);';
        
        $html .= '$'. $tag['value'] .' = $__think__list__; ?>';

        return $html;
    }
 
    public function tagArticle($tag, $content)
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
        $html .= '$__think__list__ = model("app.article.model.Articles")->getItemListByTag($__think__tag__,"list");';
        if ($page) {
            $html .= '$pagination = model("app.article.model.Articles")->getItemListByTag($__think__tag__,"pagination");';
        } else {
            $html .= '$pagination = "";';
        }
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
    
 
    public function tagArticleinfo($tag, $content)
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
        $html .= '$__think__list__ = model("app.article.model.Articles")->getItemListByTag($__think__tag__,"list");';
        $html .= '$'. $tag['value'] .' = $__think__list__; ?>';

        return $html;
    }
    
    public function tagArticlecrumb($tag, $content)
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
        $html .= '$__think__info__ = model("app.article.model.Articles")->getCrumb($__think__tag__);';
        $html .= 'echo $__think__info__; ?>';
        return $html;
    }
    
    public function tagCrumb($tag, $content)
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
        $html .= '$__think__info__ = model("app.article.model.Articles")->getCrumb($__think__tag__);';
        $html .= 'echo $__think__info__; ?>';
        return $html;
    }
    public function tagArticlepage($tag, $content)
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
        $html .= '$__think__list__ = model("app.article.model.Articles")->getPage($__think__tag__);';
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
    
    public function tagPage($tag, $content)
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
        $html .= '$__think__list__ = model("app.article.model.Articles")->getPage($__think__tag__);';
        $html .= ' ?>';
        $html .= '{volist name="__think__list__" id="' . $tag['value'] . '" key="' . $tag['key'] . '"}';
        $html .= $content;
        $html .= '{/volist}';
        return $html;
    }
    
}
