<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 2017/3/9
 * Time: 下午10:07
 */

namespace backend\controllers;

use yii;
use common\models\File as FileModel;

class AssetsController extends \yii\web\Controller
{


    public function actions()
    {
        return [
            'ueditor' => [
                'class' => 'backend\widgets\ueditor\UeditorAction',
            ],
        ];
    }

}