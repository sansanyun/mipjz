<?php

namespace app\route;

use think\Route;

use think\Config;

use think\DB;

use think\Request;

    $pages = [];
    $dirs = [];
    $subPages = [];
    $pageTemplatePath = config('template.view_path') . DS . 'diy';
    if (is_dir($pageTemplatePath)) {
        $templateFile = opendir($pageTemplatePath);
        if ($templateFile) {
            while (false !== ($file = readdir($templateFile))) {
                if (substr($file, 0, 1) != '.' AND is_file($pageTemplatePath . DS . $file)) {
                    $pages[] = $file;
                }
                if (substr($file, 0, 1) != '.' AND is_dir($pageTemplatePath . DS . $file)) {
                    $dirs[] = $file;
                }
            }
            closedir($templateFile);
        }
        if ($dirs) {
            foreach ($dirs as $k => $v) {
                $dirTemplatePath = config('template.view_path') . DS . 'diy' . DS . $v;
                if (is_dir($dirTemplatePath)) {
                    $dirTemplateFile = opendir($dirTemplatePath);
                    if ($dirTemplateFile) {
                        while (false !== ($file = readdir($dirTemplateFile))) {
                            if (substr($file, 0, 1) != '.' AND is_file($dirTemplatePath . DS . $file)) {
                                $subPages[] = $file;
                            }
                        }
                        closedir($dirTemplateFile);
                    }
                }

                if ($subPages) {
                    foreach ($subPages as $key => $val) {
                        $subPages[$key] = preg_replace("/.html/","",$val);
                        Route::rule(['/' . $v . '/' . $subPages[$key].'/:params' => ['diy/Diy/index?dir=' . $v .'&name=' . $subPages[$key] . '&params=:params',[],[]]]);
                        Route::rule(['/' . $v . '/' . $subPages[$key] => ['diy/Diy/index?dir=' . $v .'&name=' . $subPages[$key],[],[]]]);
                    }
                }
            }
        }

        if ($pages) {
            foreach ($pages as $key => $val) {
                $pages[$key] = preg_replace("/.html/","",$val);
                Route::rule([$pages[$key].'/:params' => ['diy/Diy/index?name=' . $pages[$key] . '&params=:params',[],[]]]);
                Route::rule([$pages[$key] => ['diy/Diy/index?name=' . $pages[$key],[],[]]]);
            }
        }
    }