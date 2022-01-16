<?php namespace app\controllers;

use app\actions\file\ThumbAction;
use yii\web\Controller;

class FileThumbController extends Controller
{
    public function actions()
    {
        return [
            'get' => [
                'class' => ThumbAction::class,
            ],
        ];
    }

    public function verbs()
    {
        return [
            'get'  => ['GET', 'HEAD'],
        ];
    }
}
