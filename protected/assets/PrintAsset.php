<?php namespace app\assets;

// Sample
class PrintAsset extends BaseAsset
{
    public $sourcePath = '@app/../assets/print';
    public $css = [
        'css/print.css',
    ];
    public $appendTimestamps = [
        'css/print.css',
    ];
    public $publishOptions = [
        'except' => ['*.styl', '*.sass', '*.scss',]
    ];
}
