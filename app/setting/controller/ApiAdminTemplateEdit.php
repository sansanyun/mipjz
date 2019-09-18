<?php

//MIPJZ.COM [Don't forget the beginner's mind]

//Copyright (c) 2017~2099 http://MIPJZ.COM All rights reserved.

namespace app\setting\controller;

use think\Request;

use think\Loader;

use app\common\controller\AdminBase;

class ApiAdminTemplateEdit extends AdminBase
{

    public function getJsonList()
    {
        $tempArray = [];
        $pages = [];
        $pageTemplatePath = config('template.view_path');
        if (is_dir($pageTemplatePath)) {
            foreach (fetch_file_lists($pageTemplatePath) as $file) {
                if (strpos($file, '.json') !== false) {
                    $tempArray = explode(DS, $file);
                    array_push($pages,['pagePath' => $file,'name' => $tempArray[count($tempArray) - 1]]);
                }
            }
        }
        if ($pages) {
            foreach ($pages as $key => $val) {
                $name = str_replace('.json', '', $val['name']);
                $tempIndexData[] = array('name' => $name, 'data' => json_decode(file_get_contents($val['pagePath']),true));
            }
        }
        return jsonSuccess('',$tempIndexData);

    }

}