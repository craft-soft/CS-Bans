<?php

class TwigHelper
{
    public static function setTitle($title)
    {
        Yii::app()->controller->pageTitle = $title;
    }
    
    public static function setBreadcrumbs($links)
    {
        $bcs = [];
        foreach($links as $link) {
            if(is_array($link)) {
                $url = [$link['route']];
                if(isset($link['params'])) {
                    $url = array_merge($url, $link['params']);
                }
                $bcs[$link['label']] = $url;
            } else {
                $bcs[] = $link;
            }
        }
        Yii::app()->controller->breadcrumbs = $bcs;
    }
}
