<?php namespace app\assets;

class SiteAsset extends BaseAsset
{
    public $sourcePath = '@app/../assets/site';
    public $css = [
        'css/main.css',
    ];
    public $js = [
        'js/main.js',
    ];
    public $appendTimestamps = [
        'css/main.css',
        'js/main.js',
    ];
    public $publishOptions = [
        'except' => ['*.styl', '*.sass', '*.scss',]
    ];
}
