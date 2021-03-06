<?php

namespace app\common\model;
use think\Cache;
use think\Controller;

class Common extends Controller {
     

    public function getContentFilterByContent($content) {

        $patern = "/^^((https|http|ftp)?:?\/\/)[^\s]+$/";

        $itemInfo['content'] = $content;

        $itemInfo['content'] = preg_replace("/style=.+?['|\"]/i", '', $itemInfo['content']);

        preg_match_all('/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/', $itemInfo['content'], $imagesArray);

        foreach ($imagesArray[0] as $key => $val) {

            @preg_match("/alt=[\'|\"](.*?)[\'|\"]/", $val, $tempAlt);

            if ($tempAlt) {

                $alt = $tempAlt[1];

            }

            @preg_match("/width=[\'|\"](.*?)[\'|\"]/", $val, $tempWidth);

            @preg_match("/height=[\'|\"](.*?)[\'|\"]/", $val, $tempHeight);

            $src = $imagesArray[1][$key];

            if (@preg_match($patern, $src)) {

            } else {

                if (strpos($src, ';base64,') === false) {

                    $src = config('domainStatic') . $src;

                }

            }

            if ($tempWidth && $tempHeight) {
                if ($tempWidth[1] > 500) {

                    $layout = '';

                    $tempImg = '<mip-img ' . $layout . ' alt="' . $alt . '" src="' . $src . '" popup></mip-img>';

                } else {

                    $layout = 'layout="fixed"';

                    $tempImg = '<mip-img ' . $layout . ' ' . $tempWidth[0] . ' ' . $tempHeight[0] . ' alt="' . $alt . '" src="' . $src . '" popup></mip-img>';

                }

            } else {

                $layout = '';

                $tempImg = '<mip-img ' . $layout . ' alt="' . $alt . '" src="' . $src . '" popup></mip-img>';

            }

            $itemInfo['content'] = str_replace($val, $tempImg, $itemInfo['content']);

        }

        @preg_match_all('/<a[^>]*>[^>]+a>/', $itemInfo['content'], $tempLink);

        foreach ($tempLink[0] as $k => $v) {

            if (strpos($v, "href")) {

            } else {

                $itemInfo['content'] = str_replace($v, '', $itemInfo['content']);

            }
        }

        return $itemInfo['content'];

    }

}
