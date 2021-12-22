<?php namespace app\assets;

// Sample
class SiteAsset extends BaseAsset
{
    public $sourcePath = '@app/../assets/site';
    public $css = [
        'css/main.css',
    ];
    public $js = [
        'vendors/jquery-3.6.0.min.js',
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
